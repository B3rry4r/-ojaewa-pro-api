@extends('emails.layout')

@section('title', 'Order Confirmation - Oja Ewa')

@section('content')
    <div class="greeting">
        Hello {{ $order->user->firstname ?? 'Valued Customer' }}!
    </div>
    
    <div class="message">
        Thank you for your order! We're excited to confirm that we've received your order and it's being processed.
    </div>
    
    <div class="info-box">
        <h3>Order Details</h3>
        <p><strong>Order ID:</strong> #{{ $order->id }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        <p><strong>Total Amount:</strong> ₦{{ number_format($order->total_amount, 2) }}</p>
        <p><strong>Status:</strong> <span class="status-badge status-info">{{ ucfirst($order->status) }}</span></p>
    </div>
    
    <div class="highlight">
        <h3>Items Ordered</h3>
        @foreach($order->orderItems as $item)
            <div style="border-bottom: 1px solid #eee; padding: 10px 0; margin-bottom: 10px;">
                <strong>{{ $item->product->name }}</strong><br>
                <span style="color: #666;">Quantity: {{ $item->quantity }} × ₦{{ number_format($item->price, 2) }}</span><br>
                <span style="color: #333;">Subtotal: ₦{{ number_format($item->quantity * $item->price, 2) }}</span>
            </div>
        @endforeach
    </div>
    
    @if($order->delivery_address)
    <div class="info-box">
        <h3>Delivery Address</h3>
        <p>{{ $order->delivery_address }}</p>
        @if($order->delivery_phone)
            <p><strong>Phone:</strong> {{ $order->delivery_phone }}</p>
        @endif
    </div>
    @endif
    
    <div class="divider"></div>
    
    <div class="message">
        <p>We'll send you another email when your order ships. You can also track your order status anytime.</p>
        <p>If you have any questions about your order, please don't hesitate to contact us.</p>
    </div>
    
    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/orders/{{ $order->id }}" class="button">
            Track Your Order
        </a>
    </div>
    
    <div class="message">
        <p>Thank you for choosing Oja Ewa!</p>
        <p><strong>The Oja Ewa Team</strong></p>
    </div>
@endsection
