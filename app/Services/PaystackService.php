<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    private string $baseUrl;
    private string $secretKey;
    private string $publicKey;

    public function __construct()
    {
        $this->baseUrl = 'https://api.paystack.co';
        $this->secretKey = config('services.paystack.secret_key') ?? 'sk_test_placeholder';
        $this->publicKey = config('services.paystack.public_key') ?? 'pk_test_placeholder';
    }

    /**
     * Initialize a payment transaction
     */
    public function initializePayment(array $paymentData): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/transaction/initialize', [
            'email' => $paymentData['email'],
            'amount' => $paymentData['amount'], // Amount in kobo
            'reference' => $paymentData['reference'],
            'currency' => $paymentData['currency'] ?? 'NGN',
            'callback_url' => $paymentData['callback_url'] ?? null,
            'metadata' => $paymentData['metadata'] ?? [],
            'channels' => $paymentData['channels'] ?? ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer'],
        ]);

        if ($response->successful()) {
            return [
                'status' => 'success',
                'data' => $response->json()['data']
            ];
        }

        Log::error('Paystack payment initialization failed', [
            'response' => $response->json(),
            'status' => $response->status()
        ]);

        return [
            'status' => 'error',
            'message' => $response->json()['message'] ?? 'Payment initialization failed',
            'data' => null
        ];
    }

    /**
     * Verify a payment transaction
     */
    public function verifyPayment(string $reference): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
        ])->get($this->baseUrl . '/transaction/verify/' . $reference);

        if ($response->successful()) {
            return [
                'status' => 'success',
                'data' => $response->json()['data']
            ];
        }

        Log::error('Paystack payment verification failed', [
            'reference' => $reference,
            'response' => $response->json(),
            'status' => $response->status()
        ]);

        return [
            'status' => 'error',
            'message' => $response->json()['message'] ?? 'Payment verification failed',
            'data' => null
        ];
    }

    /**
     * Get payment page link
     */
    public function getPaymentLink(string $reference): string
    {
        // You can also use the checkout URL from initialize response
        return "https://checkout.paystack.com/" . $reference;
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $input, string $signature): bool
    {
        $computedSignature = hash_hmac('sha512', $input, $this->secretKey);
        return hash_equals($signature, $computedSignature);
    }

    /**
     * Generate unique payment reference
     */
    public function generateReference(string $prefix = 'TXN'): string
    {
        return $prefix . '_' . time() . '_' . uniqid();
    }

    /**
     * Convert amount to kobo (Paystack uses kobo)
     */
    public function convertToKobo(float $amount): int
    {
        return (int) ($amount * 100);
    }

    /**
     * Convert amount from kobo to naira
     */
    public function convertFromKobo(int $amount): float
    {
        return $amount / 100;
    }
}