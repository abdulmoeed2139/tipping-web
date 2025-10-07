@extends('layouts.master')

@section('content')
<style>
    .crypto-payment-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .payment-info-card {
        background: #fff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    
    .crypto-amount {
        font-size: 2rem;
        font-weight: bold;
        color: #ff6600;
        margin: 10px 0;
    }
    
    .payment-address {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
        word-break: break-all;
        font-family: monospace;
        font-size: 14px;
    }
    
    .qr-code-container {
        text-align: center;
        margin: 20px 0;
    }
    
    .qr-code-container img {
        max-width: 200px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    
    .payment-status {
        text-align: center;
        margin: 20px 0;
    }
    
    .status-pending {
        color: #ff9800;
    }
    
    .status-paid {
        color: #4caf50;
    }
    
    .status-failed {
        color: #f44336;
    }
    
    .countdown-timer {
        background: #fff3e0;
        border: 1px solid #ff9800;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        margin: 15px 0;
    }
    
    .copy-btn {
        background: #ff6600;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        margin-left: 10px;
    }
    
    .copy-btn:hover {
        background: #e55a00;
    }
    
    .payment-instructions {
        background: #e3f2fd;
        border-left: 4px solid #2196f3;
        padding: 15px;
        margin: 20px 0;
    }
</style>

<main class="dashboard-con">
    {{-- Sidebar Include --}}
    @if(Auth::check())
        @include('layouts.sidebar')
    @endif

    <div id="dashboard2">
        <div class="content">
            <div class="crypto-payment-container">
                <div class="payment-info-card">
                    <h2 class="text-center mb-4">Crypto Payment</h2>
                    
                    <div class="text-center">
                        <h4>Pay {{ number_format($order->amount, 2) }} {{ strtoupper($order->price_currency ?? 'USD') }}</h4>
                        <div class="crypto-amount">
                            {{ number_format($order->pay_amount ?? $order->crypto_amount, 8) }} {{ strtoupper($order->pay_currency ?? $order->crypto_currency) }}
                        </div>
                        <p class="text-muted">Using {{ ucfirst($order->crypto_currency) }} on {{ ucfirst($order->network ?? 'blockchain') }} network</p>
                        
                        @if($order->amount_received > 0)
                        <div class="alert alert-info mt-3">
                            <strong>Amount Received:</strong> {{ number_format($order->amount_received, 8) }} {{ strtoupper($order->pay_currency ?? $order->crypto_currency) }}
                        </div>
                        @endif
                    </div>

                    <div class="countdown-timer">
                        <h5>Payment Expires In:</h5>
                        <div id="countdown" class="h4 text-danger"></div>
                    </div>

                    <div class="payment-instructions">
                        <h5>Payment Instructions:</h5>
                        <ol>
                            <li>Send exactly <strong>{{ number_format($order->pay_amount ?? $order->crypto_amount, 8) }} {{ strtoupper($order->pay_currency ?? $order->crypto_currency) }}</strong></li>
                            <li>To the address below</li>
                            <li>Make sure you're using the correct network/chain</li>
                        </ol>
                    </div>

                    <div class="payment-address">
                        <strong>Payment Address:</strong><br>
                        <span id="paymentAddress">{{ $order->payment_address }}</span>
                        <button class="copy-btn" onclick="copyAddress()">Copy</button>
                    </div>


                    <!-- NOWPayments Payment Details -->
                    <div class="payment-details" style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin: 15px 0;">
                        <h6><i class="fas fa-info-circle"></i> Payment Details:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Payment ID:</strong> {{ $order->nowpayments_payment_id }}</p>
                                <p><strong>Status:</strong> <span class="badge badge-{{ $order->payment_status === 'waiting' ? 'warning' : ($order->payment_status === 'finished' ? 'success' : 'danger') }}">{{ ucfirst($order->payment_status ?? 'waiting') }}</span></p>
                                <p><strong>Type:</strong> {{ ucfirst($order->type ?? 'crypto2crypto') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Fixed Rate:</strong> {{ $order->is_fixed_rate ? 'Yes' : 'No' }}</p>
                                <p><strong>Fee Paid By:</strong> {{ $order->is_fee_paid_by_user ? 'User' : 'Merchant' }}</p>
                                @if($order->valid_until)
                                <p><strong>Valid Until:</strong> {{ $order->valid_until->format('M d, Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="qr-code-container">
                        <h5>Or Scan QR Code:</h5>
                        <div id="qrCode">
                            {!! QrCode::size(200)->generate($order->payment_address) !!}
                        </div>
                    </div>

                    <div class="payment-status">
                        <h4 id="paymentStatus" class="status-pending">
                            <i class="fas fa-clock"></i> Waiting for Payment...
                        </h4>
                        <p id="statusMessage">Please send the exact amount to the address above.</p>
                    </div>

                    <div class="wallet-help" style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin: 20px 0;">
                        <h5><i class="fas fa-wallet"></i> How to Send Payment:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Popular Wallets:</h6>
                                <ul>
                                    @if($order->crypto_currency === 'btc')
                                    <li>Bitcoin Core</li>
                                    <li>Electrum</li>
                                    <li>Exodus</li>
                                    <li>Trust Wallet</li>
                                    @elseif($order->crypto_currency === 'eth' || $order->crypto_currency === 'usdt' || $order->crypto_currency === 'usdc')
                                    <li>MetaMask</li>
                                    <li>Trust Wallet</li>
                                    <li>Coinbase Wallet</li>
                                    <li>MyEtherWallet</li>
                                    @elseif($order->crypto_currency === 'bnb')
                                    <li>Trust Wallet</li>
                                    <li>MetaMask (BSC)</li>
                                    <li>Binance Chain Wallet</li>
                                    @else
                                    <li>Trust Wallet</li>
                                    <li>Exodus</li>
                                    <li>Official Wallet</li>
                                    @endif
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Steps:</h6>
                                <ol>
                                    <li>Open your wallet app</li>
                                    <li>Select "Send" or "Transfer"</li>
                                    <li>Paste the payment address</li>
                                    <li>Enter the exact amount</li>
                                    <li>Confirm the transaction</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ url('/tip') }}" class="btn btn-secondary">Create New Payment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Preloader -->
    <div id="preloader"></div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let countdownInterval;
    let statusCheckInterval;
    let debugMode = {{env('APP_DEBUG')}};
    
    // Set up countdown timer
    const expiresAt = new Date('{{ $order->payment_expires_at }}').getTime();
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = expiresAt - now;
        
        if (distance < 0) {
            clearInterval(countdownInterval);
            $('#countdown').html('EXPIRED');
            $('#paymentStatus').html('<i class="fas fa-times"></i> Payment Expired').removeClass('status-pending').addClass('status-failed');
            $('#statusMessage').text('This payment has expired. Please create a new payment.');
            return;
        }
        
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        $('#countdown').html(minutes + 'm ' + seconds + 's');
    }
    
    // Update countdown every second
    countdownInterval = setInterval(updateCountdown, 1000);
    updateCountdown();
    
    // Check payment status every 10 seconds
    statusCheckInterval = setInterval(checkPaymentStatus, 10000);
    
    function checkPaymentStatus() {
        $.ajax({
            url: "{{ url('/crypto-payment-status/' . $order->id) }}",
            type: "GET",
            success: function(response) {
                if (debugMode) {
                   response.status = 'confirmed';
                }
                if (response.status === 'confirmed') {
                    clearInterval(statusCheckInterval);
                    clearInterval(countdownInterval);
                    $('#paymentStatus').html('<i class="fas fa-check"></i> Payment Received!').removeClass('status-pending').addClass('status-paid');
                    $('#statusMessage').text('Thank you! Your payment has been confirmed.');
                    
                    // Redirect to success page after 3 seconds
                    setTimeout(function() {
                        window.location.href = "{{ url('/order/' . $order->id . '/success') }}";
                    }, 3000);
                } else if (response.status === 'failed') {
                    clearInterval(statusCheckInterval);
                    clearInterval(countdownInterval);
                    $('#paymentStatus').html('<i class="fas fa-times"></i> Payment Failed').removeClass('status-pending').addClass('status-failed');
                    $('#statusMessage').text('Payment failed. Please try again.');
                }
            },
            error: function() {
                console.log('Error checking payment status');
            }
        });
    }
    
    // Initial status check
    checkPaymentStatus();
});

function copyAddress() {
    const address = document.getElementById('paymentAddress').textContent;
    navigator.clipboard.writeText(address).then(function() {
        const btn = document.querySelector('.copy-btn');
        const originalText = btn.textContent;
        btn.textContent = 'Copied!';
        btn.style.background = '#4caf50';
        
        setTimeout(function() {
            btn.textContent = originalText;
            btn.style.background = '#ff6600';
        }, 2000);
    });
}
</script>

@endsection
