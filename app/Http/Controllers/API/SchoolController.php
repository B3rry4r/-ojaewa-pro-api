<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SchoolRegistration;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SchoolController extends Controller
{
    protected PaystackService $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Register for the school
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'country' => 'required|string|max:100',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:500',
        ]);

        $schoolRegistration = SchoolRegistration::create($request->only([
            'country', 'full_name', 'phone_number', 'state', 'city', 'address'
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'School registration submitted successfully',
            'data' => $schoolRegistration
        ], 201);
    }

    /**
     * Generate payment link for school registration fee
     */
    public function createPaymentLink(Request $request): JsonResponse
    {
        $request->validate([
            'registration_id' => 'required|integer|exists:school_registrations,id',
            'email' => 'required|email'
        ]);

        $registration = SchoolRegistration::findOrFail($request->registration_id);

        // Check if already paid
        if ($registration->payment_reference) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration fee already paid'
            ], 400);
        }

        // Generate payment reference
        $reference = $this->paystackService->generateReference('SCHOOL');
        
        // School registration fee (in NGN)
        $amount = 50000; // 500 NGN

        // Prepare payment data
        $paymentData = [
            'email' => $request->email,
            'amount' => $this->paystackService->convertToKobo($amount),
            'reference' => $reference,
            'currency' => 'NGN',
            'callback_url' => config('app.frontend_url') . '/school/payment/callback',
            'metadata' => [
                'registration_id' => $registration->id,
                'payment_type' => 'school_registration',
                'custom_fields' => [
                    [
                        'display_name' => 'Registration ID',
                        'variable_name' => 'registration_id',
                        'value' => $registration->id
                    ],
                    [
                        'display_name' => 'Full Name',
                        'variable_name' => 'full_name',
                        'value' => $registration->full_name
                    ]
                ]
            ]
        ];

        $result = $this->paystackService->initializePayment($paymentData);

        if ($result['status'] === 'success') {
            // Store payment reference
            $registration->update(['payment_reference' => $reference]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment link generated successfully',
                'data' => [
                    'payment_url' => $result['data']['authorization_url'],
                    'access_code' => $result['data']['access_code'],
                    'reference' => $reference,
                    'amount' => $amount,
                    'currency' => 'NGN'
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result['message'],
        ], 400);
    }

    /**
     * Handle Paystack webhook for school registration payments
     */
    public function handlePaymentWebhook(Request $request): JsonResponse
    {
        // Verify webhook signature
        $signature = $request->header('x-paystack-signature');
        $input = $request->getContent();

        if (!$this->paystackService->verifyWebhookSignature($input, $signature)) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $event = $request->all();
        
        // Handle payment success
        if ($event['event'] === 'charge.success') {
            $paymentData = $event['data'];
            $reference = $paymentData['reference'];
            
            $registration = SchoolRegistration::where('payment_reference', $reference)->first();
            
            if ($registration) {
                $registration->update([
                    'status' => 'processing',
                    'payment_data' => json_encode($paymentData)
                ]);
            }
        }

        return response()->json(['message' => 'Webhook received'], 200);
    }
}