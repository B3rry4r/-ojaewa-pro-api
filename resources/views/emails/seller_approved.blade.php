@extends('emails.layout')

@section('title', 'Seller Profile {{ $status === "approved" ? "Approved" : "Update Required" }} - Oja Ewa')

@section('content')
    <div class="greeting">
        Hello {{ $seller->user->firstname ?? 'Seller' }}!
    </div>
    
    @if($status === 'approved')
        <div class="message">
            🎉 Congratulations! Your seller profile has been approved! You can now start listing products on Oja Ewa.
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <span class="status-badge status-success">Approved</span>
        </div>
        
        <div class="info-box">
            <h3>Seller Details</h3>
            <p><strong>Business Name:</strong> {{ $seller->business_name }}</p>
            <p><strong>Email:</strong> {{ $seller->business_email }}</p>
            <p><strong>Approved Date:</strong> {{ $seller->updated_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        
        <div class="message">
            <h3>What's Next?</h3>
            <ul>
                <li>Start adding your products to your store</li>
                <li>Complete your seller profile with additional details</li>
                <li>Set up your payment information to receive payouts</li>
                <li>Engage with customers and build your reputation</li>
            </ul>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ config('app.frontend_url') }}/seller/products/create" class="button">
                List Your First Product
            </a>
        </div>
    @else
        <div class="message">
            Your seller profile for <strong>{{ $seller->business_name }}</strong> requires some updates before it can be approved.
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <span class="status-badge status-warning">Action Required</span>
        </div>
        
        @if($rejectionReason)
            <div class="info-box">
                <h3>Feedback from our Team</h3>
                <p>{{ $rejectionReason }}</p>
            </div>
        @endif
        
        <div class="info-box">
            <h3>Next Steps</h3>
            <p>Please review the feedback above and update your seller profile accordingly. Once you've made the necessary changes, resubmit your profile for review.</p>
            <p>Our team typically reviews updated profiles within 24-48 hours.</p>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ config('app.frontend_url') }}/seller/profile/edit" class="button">
                Update Seller Profile
            </a>
        </div>
        
        <div class="message">
            <p>If you have any questions about the required updates, please don't hesitate to contact our support team.</p>
        </div>
    @endif
    
    <div class="divider"></div>
    
    <div class="message">
        <p>Thank you for choosing to sell on Oja Ewa.</p>
        <p><strong>The Oja Ewa Team</strong></p>
    </div>
@endsection
