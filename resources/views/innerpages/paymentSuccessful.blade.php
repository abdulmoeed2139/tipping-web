@extends('layouts.master')

@section('content')

<main class="dashboard-con">
     {{-- Sidebar Include --}}
         @if(Auth::check())
              @include('layouts.sidebar')
             
         @endif

    <div id="dashboard2">

               <!-- Content -->
            <div class="content">
                <div class="welcome-card">
                    <div class="amount-card">
                        <img src="assets/img/success.png" alt="" class="mb-4">
                        <h1 class="amount-card-title">Payment <span class="span-img4"  style="font-weight: 600;">Successful</span></h1>
                        <p>Your payment has been completed successfully.</p>
                        
                        <!-- Invoice Section -->
                        <div class="invoice-section mt-4">
                            <div class="invoice-header">
                                <h3 class="text-center mb-3">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                                        <path d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20Z" fill="currentColor"/>
                                    </svg>
                                    Payment Invoice
                                </h3>
                            </div>
                            
                            <div class="invoice-details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="invoice-info">
                                            <h5>Invoice Details</h5>
                                            <p><strong>Invoice #:</strong> INV-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                                            <p><strong>Date:</strong> {{ $order->updated_at->format('M d, Y H:i') }}</p>
                                            <p><strong>Status:</strong> <span class="badge bg-success">Paid</span></p>
                                            <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'Unknown') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="invoice-info">
                                            <h5>Payment Information</h5>
                                            @if($order->isCryptoPayment())
                                                <p><strong>Cryptocurrency:</strong> {{ strtoupper($order->crypto_currency ?? 'N/A') }}</p>
                                                <p><strong>Amount Paid:</strong> {{ number_format($order->pay_amount ?? $order->crypto_amount, 8) }} {{ strtoupper($order->pay_currency ?? $order->crypto_currency) }}</p>
                                                <p><strong>Network:</strong> {{ ucfirst($order->network ?? 'Blockchain') }}</p>
                                                @if($order->nowpayments_payment_id)
                                                <p><strong>Transaction ID:</strong> {{ $order->nowpayments_payment_id }}</p>
                                                @endif
                                            @else
                                                <p><strong>Amount:</strong> ${{ number_format($order->amount, 2) }}</p>
                                                @if($order->nowpayments_payment_id)
                                                <p><strong>Transaction ID:</strong> {{ $order->nowpayments_payment_id }}</p>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="invoice-summary mt-4">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5>Payment Summary</h5>
                                            <p><strong>Service:</strong> Tip Payment</p>
                                            <p><strong>Description:</strong> Payment for order #{{ $order->id }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="total-amount">
                                                <h4>Total Amount</h4>
                                                <h3 class="orderAmount">${{ number_format($order->amount, 2) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($order->isCryptoPayment() && $order->amount_received > 0)
                                <div class="crypto-details mt-3">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle"></i> Crypto Payment Details</h6>
                                        <p><strong>Amount Received:</strong> {{ number_format($order->amount_received, 8) }} {{ strtoupper($order->pay_currency ?? $order->crypto_currency) }}</p>
                                        <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status ?? 'Completed') }}</p>
                                        @if($order->is_fixed_rate)
                                        <p><strong>Rate Type:</strong> Fixed Rate</p>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <div class="invoice-actions mt-4 text-center">
                                <button class="btn btn-outline-primary me-2" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print Invoice
                                </button>
                                <a href="{{ url('/order/' . $order->id . '/invoice') }}" target="_blank" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-file-pdf"></i> View PDF Invoice
                                </a>
                                <button class="btn btn-outline-info me-2" onclick="downloadInvoice()">
                                    <i class="fas fa-download"></i> Download PDF
                                </button>
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                                    <i class="fas fa-home"></i> Go to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


    </div>


      <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
  
  <!-- Invoice Styles -->
  <style>
    .invoice-section {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        padding: 30px;
        margin: 20px 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .invoice-header h3 {
        color: #333;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
    }
    
    .invoice-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .invoice-info h5 {
        color: #007bff;
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .invoice-info p {
        margin-bottom: 8px;
        color: #555;
    }
    
    .total-amount {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }
    
    .total-amount h4 {
        margin-bottom: 10px;
        font-size: 1.1rem;
    }
    
    .total-amount h3 {
        font-size: 2rem;
        font-weight: bold;
        margin: 0;
    }
    
    .invoice-actions .btn {
        margin: 5px;
        padding: 10px 20px;
    }
    
    .crypto-details .alert {
        border-left: 4px solid #17a2b8;
    }
    
    .badge {
        font-size: 0.9rem;
        padding: 6px 12px;
    }
    
    /* Print Styles */
    @media print {
        .invoice-section {
            box-shadow: none;
            border: 1px solid #000;
        }
        
        .invoice-actions {
            display: none !important;
        }
        
        .sidebar, .scroll-top, .preloader {
            display: none !important;
        }
        
        body {
            background: white !important;
        }
    }
  </style>
  
  <!-- Invoice JavaScript -->
  <script>
    function downloadInvoice() {
        // Open the PDF invoice in a new window for printing/downloading
        const invoiceUrl = "{{ url('/order/' . $order->id . '/invoice') }}";
        const printWindow = window.open(invoiceUrl, '_blank');
        
        // Wait for the window to load, then print
        printWindow.onload = function() {
            setTimeout(function() {
                printWindow.print();
            }, 1000);
        };
    }
    
    // Auto-show success message
    window.onload = function() {
        // Show a success notification
        if (typeof toastr !== 'undefined') {
            toastr.success('Payment completed successfully! Invoice generated.');
        }
        
        // Optional: Auto-print invoice (uncomment if needed)
        // setTimeout(function() {
        //     if (confirm('Would you like to print the invoice?')) {
        //         window.print();
        //     }
        // }, 2000);
    };
  </script>
</main>



@endsection
