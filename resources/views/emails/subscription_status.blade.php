@extends('emails.layout')

@section('title', 'Subscription {{ $success ? "Renewed" : "Payment Failed" }} - Oja Ewa')

@section('content')
    <div class="greeting">
        Hello {{ $subscription->user->firstname ?? 'Valued Customer' }}!
    </div>
    
    @if($success)
        <div class="message">
            🎉 Great news! Your Oja Ewa subscription has been successfully renewed.
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <span class="status-badge status-success">Renewed Successfully</span>
        </div>
        
        <div class="info-box">
            <h3>Renewal Details</h3>
            <p><strong>Plan:</strong> {{ $subscription->plan_name ?? 'Premium Plan' }}</p>
            <p><strong>Renewed On:</strong> {{ $subscription->renewed_at ? $subscription->renewed_at->format('F j, Y \a\t g:i A') : now()->format('F j, Y \a\t g:i A') }}</p>
            <p><strong>Next Renewal:</strong> {{ $subscription->expires_at->format('F j, Y') }}</p>
            <p><strong>Amount Charged:</strong> ₦{{ number_format($subscription->amount, 2) }}</p>
            @if($subscription->payment_method)
                <p><strong>Payment Method:</strong> {{ $subscription->payment_method }}</p>
            @endif
        </div>
        
        <div class="highlight">
            <h3>Your Premium Benefits Continue</h3>
            <p>You can continue enjoying all premium features without interruption:</p>
            <ul style="text-align: left; margin: 15px 0;">
                <li>Priority customer support</li>
                <li>Advanced business analytics</li>
                <li>Featured business listings</li>
                <li>Unlimited product uploads</li>
                <li>Custom business branding</li>
                <li>Early access to new features</li>
            </ul>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ config('app.frontend_url') }}/dashboard" class="button">
                Access Your Dashboard
            </a>
        </div>
        
        <div class="message">
            <p>Thank you for continuing your premium membership with Oja Ewa!</p>
        </div>
    @else
        <div class="message">
            We encountered an issue processing your subscription renewal payment.
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <span class="status-badge status-danger">Payment Failed</span>
        </div>
        
        <div class="info-box">
            <h3>Subscription Details</h3>
            <p><strong>Plan:</strong> {{ $subscription->plan_name ?? 'Premium Plan' }}</p>
            <p><strong>Expiration Date:</strong> {{ $subscription->expires_at->format('F j, Y') }}</p>
            <p><strong>Amount:</strong> ₦{{ number_format($subscription->amount, 2) }}</p>
            <p><strong>Status:</strong> <span class="status-badge status-warning">Payment Required</span></p>
        </div>
        
        @if($errorMessage)
            <div class="highlight">
                <h3>Payment Error</h3>
                <p>{{ $errorMessage }}</p>
            </div>
        @endif
        
        <div class="info-box">
            <h3>What Happens Next?</h3>
            <p>Your subscription is currently in a grace period. You have <strong>{{ $gracePeriodDays ?? 7 }} days</strong> to update your payment method and complete the renewal.</p>
            <p>If payment is not received within this period, your subscription will be cancelled and you'll lose access to premium features.</p>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.frontend_url') }}/subscription/payment" class="button">
                Update Payment Method
            </a>
        </div>
        
        <div class="message">
            <h3>Need Help?</h3>
            <p>If you're experiencing issues with payment or need assistance, our support team is here to help.</p>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ config('app.frontend_url') }}/support" class="button" style="background: #6c757d;">
                Contact Support
            </a>
        </div>
    @endif
    
    <div class="divider"></div>
    
    <div class="message">
        <p>You can manage your subscription settings anytime from your account dashboard.</p>
        <p><strong>The Oja Ewa Team</strong></p>
    </div>
    
    <div class="info-box" style="margin-top: 30px;">
        <p><small>This is an automated notification. If you have any questions, please contact our support team.</small></p>
    </div>
@endsection
