@extends('layouts.master')

@section('content')

<style>
    .fiat-payment-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .payment-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        text-align: center;
    }
    
    .payment-header {
        margin-bottom: 30px;
    }
    
    .payment-header h1 {
        color: #333;
        font-size: 28px;
        margin-bottom: 10px;
    }
    
    .payment-header p {
        color: #666;
        font-size: 16px;
    }
    
    .payment-amount {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
    }
    
    .payment-amount h2 {
        color: #ff9800;
        font-size: 32px;
        margin: 0;
    }
    
    .payment-amount p {
        color: #666;
        margin: 5px 0 0 0;
    }
    
    .payment-info {
        background: #e3f2fd;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
    }
    
    .payment-info h3 {
        color: #1976d2;
        margin-bottom: 15px;
    }
    
    .payment-info p {
        color: #666;
        margin: 5px 0;
    }
    
    .countdown-timer {
        background: #fff3e0;
        border-radius: 8px;
        padding: 15px;
        margin: 20px 0;
    }
    
    .countdown-timer h4 {
        color: #f57c00;
        margin-bottom: 10px;
    }
    
    .countdown-timer #countdown {
        font-size: 24px;
        font-weight: bold;
        color: #e65100;
    }
    
    .payment-status {
        background: #f3e5f5;
        border-radius: 8px;
        padding: 15px;
        margin: 20px 0;
    }
    
    .payment-status h4 {
        color: #7b1fa2;
        margin-bottom: 10px;
    }
    
    .payment-status #paymentStatus {
        font-size: 18px;
        font-weight: bold;
        color: #4a148c;
    }
    
    .action-buttons {
        margin-top: 30px;
    }
    
    .btn {
        padding: 12px 30px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        margin: 0 10px;
        display: inline-block;
    }
    
    .btn-success {
        background: #4caf50;
        color: white;
    }
    
    .btn-danger {
        background: #f44336;
        color: white;
    }
    
    .btn-primary {
        background: #2196f3;
        color: white;
    }
    
    .btn:hover {
        opacity: 0.9;
    }
</style>

<main class="dashboard-con">
    @if(Auth::check())
        @include('layouts.sidebar')
    @endif
    <div id="dashboard2">
        <div class="content">
            <div class="fiat-payment-container">
                <div class="payment-card">
                    <div class="payment-header">
                        <h1>Complete Your Payment</h1>
                        <p>You will be redirected to NOWPayments secure payment page</p>
                    </div>

                    <div class="payment-amount">
                        <h2>${{ number_format($order->amount, 2) }}</h2>
                        <p>Order #{{ $order->id }}</p>
                    </div>

                    <div class="payment-info">
                        <h3>Payment Information</h3>
                        <p><strong>Payment Method:</strong> Visa/Mastercard/Apple Pay</p>
                        <p><strong>Order Description:</strong> Tip payment for order #{{ $order->id }}</p>
                        <p><strong>Payment ID:</strong> {{ $order->nowpayments_payment_id }}</p>
                    </div>

                    <div class="countdown-timer">
                        <h4>Payment Expires In:</h4>
                        <div id="countdown"></div>
                    </div>

                    <div class="payment-status">
                        <h4>Payment Status:</h4>
                        <div id="paymentStatus">{{ ucfirst($order->status) }}</div>
                    </div>

                    <div class="action-buttons">
                        @if($order->payment_qr_code)
                            <a href="{{ $order->payment_qr_code }}" class="btn btn-primary" target="_blank">
                                Complete Payment
                            </a>
                        @endif
                        
                        <a href="{{ url('/fiat-payment-success/' . $order->id) }}" class="btn btn-success" id="successBtn" style="display: none;">
                            Payment Successful
                        </a>
                        
                        <a href="{{ url('/fiat-payment-cancel/' . $order->id) }}" class="btn btn-danger" id="failedBtn" style="display: none;">
                            Payment Failed
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const expiresAt = new Date('{{ $order->payment_expires_at }}').getTime();
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = expiresAt - now;
        
        if (distance < 0) {
            document.getElementById('countdown').innerHTML = 'EXPIRED';
            document.getElementById('paymentStatus').innerHTML = 'Expired';
            return;
        }
        
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById('countdown').innerHTML = minutes + 'm ' + seconds + 's ';
    }
    
    // Update countdown every second
    updateCountdown();
    setInterval(updateCountdown, 1000);
    
    // Check payment status every 10 seconds
    function checkPaymentStatus() {
        fetch('{{ url("/api/nowpayments/payment-status/" . $order->nowpayments_payment_id) }}')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'finished') {
                    document.getElementById('paymentStatus').innerHTML = 'Paid';
                    document.getElementById('successBtn').style.display = 'inline-block';
                } else if (data.status === 'failed') {
                    document.getElementById('paymentStatus').innerHTML = 'Failed';
                    document.getElementById('failedBtn').style.display = 'inline-block';
                }
            })
            .catch(error => {
                console.error('Error checking payment status:', error);
            });
    }
    
    // Check status every 10 seconds
    setInterval(checkPaymentStatus, 10000);
});
</script>

@endsection
