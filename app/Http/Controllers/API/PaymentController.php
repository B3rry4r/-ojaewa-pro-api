<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PaystackService $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Generate payment link for an order
     */
    public function createOrderPaymentLink(Request $request): JsonResponse
    {
        $request->validate([
            "order_id" => "required|integer|exists:orders,id",
        ]);

        $user = Auth::user();
        $order = Order::where("user_id", $user->id)
            ->where("id", $request->order_id)
            ->firstOrFail();

        // Check if order is already paid
        if ($order->status === "paid") {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Order is already paid",
                ],
                400,
            );
        }

        // Generate payment reference
        $reference = $this->paystackService->generateReference("ORDER");

        // Prepare payment data
        // Use the API callback URL - Paystack will redirect here after payment
        // This endpoint will then redirect to the mobile app via deep link
        $callbackUrl = config('app.url') . '/api/payment/callback';
        
        $paymentData = [
            "email" => $user->email,
            "amount" => $this->paystackService->convertToKobo(
                $order->total_price,
            ),
            "reference" => $reference,
            "currency" => "NGN",
            "callback_url" => $callbackUrl,
            "metadata" => [
                "order_id" => $order->id,
                "user_id" => $user->id,
                "payment_type" => "order",
                "custom_fields" => [
                    [
                        "display_name" => "Order ID",
                        "variable_name" => "order_id",
                        "value" => $order->id,
                    ],
                ],
            ],
        ];

        $result = $this->paystackService->initializePayment($paymentData);

        if ($result["status"] === "success") {
            // Store payment reference in order for tracking
            $order->update(["payment_reference" => $reference]);

            return response()->json([
                "status" => "success",
                "message" => "Payment link generated successfully",
                "data" => [
                    "payment_url" => $result["data"]["authorization_url"],
                    "access_code" => $result["data"]["access_code"],
                    "reference" => $reference,
                    "amount" => $this->paystackService->convertFromKobo(
                        $paymentData["amount"],
                    ),
                    "currency" => "NGN",
                ],
            ]);
        }

        return response()->json(
            [
                "status" => "error",
                "message" => $result["message"],
            ],
            400,
        );
    }

    /**
     * Verify payment status
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        $request->validate([
            "reference" => "required|string",
        ]);

        $result = $this->paystackService->verifyPayment($request->reference);

        if ($result["status"] === "success") {
            $paymentData = $result["data"];

            // Find the order using reference
            $order = Order::where(
                "payment_reference",
                $request->reference,
            )->first();

            if (!$order) {
                return response()->json(
                    [
                        "status" => "error",
                        "message" =>
                            "Order not found for this payment reference",
                    ],
                    404,
                );
            }

            // Check payment status
            if ($paymentData["status"] === "success") {
                // Update order status
                $order->update([
                    "status" => "paid",
                    "payment_data" => json_encode($paymentData),
                ]);

                return response()->json([
                    "status" => "success",
                    "message" => "Payment verified successfully",
                    "data" => [
                        "order_id" => $order->id,
                        "payment_status" => $paymentData["status"],
                        "amount" => $this->paystackService->convertFromKobo(
                            $paymentData["amount"],
                        ),
                        "currency" => $paymentData["currency"],
                        "paid_at" => $paymentData["paid_at"],
                    ],
                ]);
            }
        }

        return response()->json(
            [
                "status" => "error",
                "message" => $result["message"],
            ],
            400,
        );
    }

    /**
     * Handle Paystack callback redirect (after user completes payment on Paystack)
     * This endpoint receives the redirect from Paystack and redirects to the mobile app
     */
    public function handleCallback(Request $request)
    {
        $reference = $request->query('reference') ?? $request->query('trxref');
        
        if (!$reference) {
            // Redirect to app with error
            return redirect('ojaewa://payment/callback?status=error&message=No reference provided');
        }

        // Verify the payment
        $result = $this->paystackService->verifyPayment($reference);
        
        if ($result['status'] === 'success') {
            $paymentData = $result['data'];
            
            // Find the order using reference
            $order = Order::where('payment_reference', $reference)->first();
            
            if ($order && $paymentData['status'] === 'success') {
                // Update order status if not already updated
                if ($order->status !== 'paid') {
                    $order->update([
                        'status' => 'paid',
                        'payment_data' => json_encode($paymentData),
                    ]);
                }
                
                // Redirect to mobile app with success
                return redirect("ojaewa://payment/callback?status=success&reference={$reference}&order_id={$order->id}");
            }
            
            // Payment not successful
            $status = $paymentData['status'] ?? 'failed';
            return redirect("ojaewa://payment/callback?status={$status}&reference={$reference}");
        }
        
        // Verification failed
        return redirect("ojaewa://payment/callback?status=error&message=Payment verification failed&reference={$reference}");
    }

    /**
     * Handle unified Paystack webhook for all payment types
     */
    public function handleOrderWebhook(Request $request): JsonResponse
    {
        // Verify webhook signature
        $signature = $request->header("x-paystack-signature");
        $input = $request->getContent();

        if (
            !$this->paystackService->verifyWebhookSignature($input, $signature)
        ) {
            Log::warning("Invalid webhook signature received");
            return response()->json(["message" => "Invalid signature"], 400);
        }

        $event = $request->all();

        Log::info("Paystack webhook received", ["event" => $event]);

        // Check payment type from metadata
        $paymentType = $event["data"]["metadata"]["payment_type"] ?? "order";

        // Handle different event types
        switch ($event["event"]) {
            case "charge.success":
                if ($paymentType === "order") {
                    $this->handleSuccessfulPayment($event["data"]);
                } elseif ($paymentType === "school") {
                    $this->handleSuccessfulSchoolPayment($event["data"]);
                }
                break;

            case "charge.failed":
                if ($paymentType === "order") {
                    $this->handleFailedPayment($event["data"]);
                } elseif ($paymentType === "school") {
                    $this->handleFailedSchoolPayment($event["data"]);
                }
                break;

            default:
                Log::info("Unhandled webhook event", [
                    "event" => $event["event"],
                ]);
        }

        return response()->json(["message" => "Webhook received"], 200);
    }

    /**
     * Handle successful payment webhook
     */
    private function handleSuccessfulPayment(array $paymentData): void
    {
        $reference = $paymentData["reference"];
        $order = Order::where("payment_reference", $reference)->first();

        if ($order && $order->status !== "paid") {
            $order->update([
                "status" => "paid",
                "payment_data" => json_encode($paymentData),
            ]);

            Log::info("Order payment confirmed via webhook", [
                "order_id" => $order->id,
                "reference" => $reference,
            ]);

            // You can add notification logic here
            // e.g., send email confirmation, update inventory, etc.
        }
    }

    /**
     * Handle failed payment webhook
     */
    private function handleFailedPayment(array $paymentData): void
    {
        $reference = $paymentData["reference"];
        $order = Order::where("payment_reference", $reference)->first();

        if ($order) {
            Log::warning("Order payment failed", [
                "order_id" => $order->id,
                "reference" => $reference,
                "failure_reason" =>
                    $paymentData["gateway_response"] ?? "Unknown",
            ]);

            // You can add logic here to handle failed payments
            // e.g., send notification to user, mark order as failed, etc.
        }
    }

    /**
     * Handle successful school payment webhook
     */
    private function handleSuccessfulSchoolPayment(array $paymentData): void
    {
        $reference = $paymentData["reference"];
        // Assuming you have a SchoolRegistration model
        // $registration = SchoolRegistration::where('payment_reference', $reference)->first();

        Log::info("School payment confirmed via webhook", [
            "reference" => $reference,
            "amount" => $paymentData["amount"] ?? 0,
        ]);

        // Add logic to handle successful school payment
        // e.g., update registration status, send confirmation email, etc.
    }

    /**
     * Handle failed school payment webhook
     */
    private function handleFailedSchoolPayment(array $paymentData): void
    {
        $reference = $paymentData["reference"];

        Log::warning("School payment failed", [
            "reference" => $reference,
            "failure_reason" => $paymentData["gateway_response"] ?? "Unknown",
        ]);

        // Add logic to handle failed school payments
        // e.g., send notification, mark registration as failed, etc.
    }
}
