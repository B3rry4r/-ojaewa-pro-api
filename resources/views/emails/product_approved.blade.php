@extends('emails.layout')

@section('title', 'Product {{ $status === "approved" ? "Approved" : "Update Required" }} - Oja Ewa')

@section('content')
    <div class="greeting">
        Hello {{ $product->sellerProfile->user->firstname ?? 'Seller' }}!
    </div>
    
    @if($status === 'approved')
        <div class="message">
            🎉 Great news! Your product has been approved and is now live on Oja Ewa!
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <span class="status-badge status-success">Approved</span>
        </div>
        
        <div class="info-box">
            <h3>Product Details</h3>
            <p><strong>Product Name:</strong> {{ $product->name }}</p>
            <p><strong>Price:</strong> ₦{{ number_format($product->price, 2) }}</p>
            <p><strong>Category:</strong> {{ $product->style ?? 'N/A' }}</p>
            <p><strong>Approved Date:</strong> {{ $product->updated_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        
        <div class="message">
            <h3>What's Next?</h3>
            <ul>
                <li>Your product is now visible to all buyers</li>
                <li>Share your product on social media to increase visibility</li>
                <li>Monitor your dashboard for orders and inquiries</li>
                <li>Keep your product information up to date</li>
            </ul>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ config('app.frontend_url') }}/products/{{ $product->id }}" class="button">
                View Your Product
            </a>
        </div>
    @else
        <div class="message">
            Your product <strong>{{ $product->name }}</strong> requires some updates before it can be approved.
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
            <h3>Common Reasons for Product Review</h3>
            <ul>
                <li>Incomplete or unclear product description</li>
                <li>Missing or low-quality product images</li>
                <li>Pricing inconsistencies</li>
                <li>Category mismatch</li>
            </ul>
        </div>
        
        <div class="info-box">
            <h3>Next Steps</h3>
            <p>Please review the feedback above and update your product accordingly. Once you've made the necessary changes, resubmit your product for review.</p>
            <p>Our team typically reviews updated products within 24-48 hours.</p>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ config('app.frontend_url') }}/seller/products/{{ $product->id }}/edit" class="button">
                Update Product
            </a>
        </div>
        
        <div class="message">
            <p>If you have any questions about the required updates, please don't hesitate to contact our support team.</p>
        </div>
    @endif
    
    <div class="divider"></div>
    
    <div class="message">
        <p>Thank you for selling on Oja Ewa.</p>
        <p><strong>The Oja Ewa Team</strong></p>
    </div>
@endsection
