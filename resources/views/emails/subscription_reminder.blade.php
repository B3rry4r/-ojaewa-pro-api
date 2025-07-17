@extends('emails.layout')

@section('title', 'Subscription Renewal Reminder - Oja Ewa')

@section('content')
    <div class="greeting">
        Hello {{ $subscription->user->firstname ?? 'Valued Customer' }}!
    </div>
    
    <div class="message">
        This is a friendly reminder that your Oja Ewa subscription is approaching its renewal date.
    </div>
    
    <div class="info-box">
        <h3>Subscription Details</h3>
        <p><strong>Plan:</strong> {{ $subscription->plan_name ?? 'Premium Plan' }}</p>
        <p><strong>Current Status:</strong> <span class="status-badge status-success">Active</span></p>
        <p><strong>Renewal Date:</strong> {{ $subscription->expires_at->format('F j, Y') }}</p>
        <p><strong>Amount:</strong> ₦{{ number_format($subscription->amount, 2) }}</p>
    </div>
    
    <div class="highlight">
        <h3>⏰ Renewal in {{ $daysUntilRenewal }} {{ $daysUntilRenewal == 1 ? 'Day' : 'Days' }}</h3>
        <p>Your subscription will automatically renew on {{ $subscription->expires_at->format('F j, Y') }} unless you choose to cancel.</p>
        @if($subscription->auto_renew)
            <p>✅ <strong>Auto-renewal is enabled</strong> - Your subscription will renew automatically using your saved payment method.</p>
        @else
            <p>⚠️ <strong>Auto-renewal is disabled</strong> - You'll need to manually renew your subscription to continue enjoying premium features.</p>
        @endif
    </div>
    
    <div class="info-box">
        <h3>Your Premium Benefits</h3>
        <ul style="text-align: left; margin: 15px 0;">
            <li>Priority customer support</li>
            <li>Advanced business analytics</li>
            <li>Featured business listings</li>
            <li>Unlimited product uploads</li>
            <li>Custom business branding</li>
            <li>Early access to new features</li>
        </ul>
    </div>
    
    @if(!$subscription->auto_renew)
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.frontend_url') }}/subscription/renew" class="button">
                Renew Subscription
            </a>
        </div>
    @endif
    
    <div class="divider"></div>
    
    <div class="message">
        <h3>Need to Make Changes?</h3>
        <p>You can manage your subscription settings, update your payment method, or cancel your subscription at any time.</p>
    </div>
    
    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/subscription/manage" class="button" style="background: #6c757d;">
            Manage Subscription
        </a>
    </div>
    
    <div class="message">
        <p>Thank you for being a valued Oja Ewa premium member!</p>
        <p><strong>The Oja Ewa Team</strong></p>
    </div>
    
    <div class="info-box" style="margin-top: 30px;">
        <p><small>This is an automated reminder. If you have any questions about your subscription, please contact our support team.</small></p>
    </div>
@endsection
