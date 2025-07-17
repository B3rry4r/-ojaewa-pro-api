@extends('emails.layout')

@section('title', 'Business Profile {{ $approved ? "Approved" : "Update Required" }} - Oja Ewa')

@section('content')
    <div class="greeting">
        Hello {{ $business->user->firstname ?? 'Business Owner' }}!
    </div>
    
    @if($approved)
        <div class="message">
            🎉 Congratulations! Your business profile has been approved and is now live on Oja Ewa.
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <span class="status-badge status-success">Approved</span>
        </div>
        
        <div class="info-box">
            <h3>Business Details</h3>
            <p><strong>Business Name:</strong> {{ $business->business_name }}</p>
            <p><strong>Category:</strong> {{ ucfirst($business->category) }}</p>
            <p><strong>Approved Date:</strong> {{ $business->updated_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        
        <div class="highlight">
            <h3>What's Next?</h3>
            <p>Your business profile is now visible to customers on Oja Ewa. Here's what you can do:</p>
            <ul style="text-align: left; margin: 15px 0;">
                <li>Update your business information anytime</li>
                <li>Add more photos and details</li>
                <li>Start receiving customer inquiries</li>
                <li>Monitor your business analytics</li>
            </ul>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ config('app.frontend_url') }}/business/{{ $business->id }}" class="button">
                View Your Business Profile
            </a>
        </div>
        
        <div class="message">
            <p>Thank you for joining the Oja Ewa business community. We're excited to help you grow your business!</p>
        </div>
    @else
        <div class="message">
            We've reviewed your business profile submission and it requires some updates before it can be approved.
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <span class="status-badge status-warning">Needs Updates</span>
        </div>
        
        <div class="info-box">
            <h3>Business Details</h3>
            <p><strong>Business Name:</strong> {{ $business->business_name }}</p>
            <p><strong>Category:</strong> {{ ucfirst($business->category) }}</p>
            <p><strong>Submitted:</strong> {{ $business->created_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        
        @if($feedback)
            <div class="highlight">
                <h3>Required Updates</h3>
                <p>{{ $feedback }}</p>
            </div>
        @endif
        
        <div class="info-box">
            <h3>Next Steps</h3>
            <p>Please review the feedback above and update your business profile accordingly. Once you've made the necessary changes, resubmit your profile for review.</p>
            <p>Our team typically reviews updated profiles within 24-48 hours.</p>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ config('app.frontend_url') }}/business/edit/{{ $business->id }}" class="button">
                Update Business Profile
            </a>
        </div>
        
        <div class="message">
            <p>If you have any questions about the required updates, please don't hesitate to contact our support team.</p>
        </div>
    @endif
    
    <div class="divider"></div>
    
    <div class="message">
        <p>Thank you for choosing Oja Ewa for your business needs.</p>
        <p><strong>The Oja Ewa Team</strong></p>
    </div>
@endsection
