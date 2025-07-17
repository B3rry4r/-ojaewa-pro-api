@extends('emails.layout')

@section('title', 'Order Status Update - Oja Ewa')

@section('content')
    <div class="greeting">
        Hello {{ $order->user->firstname ?? 'Valued Customer' }}!
    </div>
    
    <div class="message">
        We have an update on your order. Your order status has been changed to:
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <span class="status-badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
    </div>
    
    <div class="info-box">
        <h3>Order Details</h3>
        <p><strong>Order ID:</strong> #{{ $order->id }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        <p><strong>Total Amount:</strong> ₦{{ number_format($order->total_amount, 2) }}</p>
        <p><strong>Updated:</strong> {{ $order->updated_at->format('F j, Y \a\t g:i A') }}</p>
    </div>
    
    @if($order->status === 'shipped')
        <div class="highlight">
            <h3>🚚 Your Order is On Its Way!</h3>
            <p>Great news! Your order has been shipped and is on its way to you.</p>
            @if($order->tracking_number)
                <p><strong>Tracking Number:</strong> {{ $order->tracking_number }}</p>
            @endif
            <p>Expected delivery: {{ $order->expected_delivery_date ? $order->expected_delivery_date->format('F j, Y') : '2-3 business days' }}</p>
        </div>
    @elseif($order->status === 'delivered')
        <div class="highlight">
            <h3>🎉 Order Delivered!</h3>
            <p>Your order has been successfully delivered. We hope you love your purchase!</p>
            <p>Delivered on: {{ $order->delivered_at ? $order->delivered_at->format('F j, Y \a\t g:i A') : 'Today' }}</p>
        </div>
    @elseif($order->status === 'cancelled')
        <div class="highlight">
            <h3>Order Cancelled</h3>
            <p>Your order has been cancelled as requested.</p>
            @if($order->cancellation_reason)
                <p><strong>Reason:</strong> {{ $order->cancellation_reason }}</p>
            @endif
            <p>If you were charged for this order, your refund will be processed within 3-5 business days.</p>
        </div>
    @elseif($order->status === 'processing')
        <div class="highlight">
            <h3>Order Being Processed</h3>
            <p>Your order is currently being prepared for shipment. We'll notify you once it's shipped.</p>
        </div>
    @endif
    
    <div class="info-box">
        <h3>Items in This Order</h3>
        @foreach($order->orderItems as $item)
            <div style="border-bottom: 1px solid #eee; padding: 10px 0; margin-bottom: 10px;">
                <strong>{{ $item->product->name }}</strong><br>
                <span style="color: #666;">Quantity: {{ $item->quantity }} × ₦{{ number_format($item->price, 2) }}</span>
            </div>
        @endforeach
    </div>
    
    <div class="divider"></div>
    
    <div class="message">
        @if($order->status === 'delivered')
            <p>We'd love to hear about your experience! Please consider leaving a review for the products you purchased.</p>
        @else
            <p>You can track your order status anytime by clicking the button below.</p>
        @endif
    </div>
    
    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/orders/{{ $order->id }}" class="button">
            @if($order->status === 'delivered')
                Leave a Review
            @else
                Track Your Order
            @endif
        </a>
    </div>
    
    <div class="message">
        <p>If you have any questions or concerns, please don't hesitate to contact our support team.</p>
        <p><strong>The Oja Ewa Team</strong></p>
    </div>
@endsection
