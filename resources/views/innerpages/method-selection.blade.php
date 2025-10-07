{{-- @php
    dd(123);
@endphp --}}

@extends('layouts.master')

@section('content')

<style>
    .payment-option {
        border: 2px solid transparent;
        border-radius: 8px;
        padding: 8px;
        cursor: pointer;
        transition: 0.3s;
    }
    .payment-option.selected {
        border: 4px solid #ff9800; /* orange highlight */
        /* background: rgba(255, 152, 0, 0.1); */
    }
    .payment-option img {
        width: 80px;
    }
    
    .crypto-payment-section {
        border-top: 1px solid #eee;
        padding-top: 20px;
    }
    
    .crypto-payment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    
    .crypto-payment-option {
        border: 2px solid transparent;
        border-radius: 8px;
        padding: 15px;
        cursor: pointer;
        transition: 0.3s;
        text-align: center;
        background: #f8f9fa;
    }
    
    .crypto-payment-option:hover {
        border-color: #ff9800;
        background: #fff3e0;
    }
    
    .crypto-payment-option.selected {
        border-color: #ff9800;
        background: #fff3e0;
    }
    
    .crypto-payment-option img {
        width: 40px;
        height: 40px;
        margin-bottom: 8px;
    }
    
    .crypto-payment-option span {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #333;
    }
    
    .error-message {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
        display: none;
    }
    
    .error-message.show {
        display: block;
    }
    
    .error-message.nowpayments-error {
        background: #fff3cd;
        color: #856404;
        border-color: #ffeaa7;
    }
    
    .error-message.network-error {
        background: #d1ecf1;
        color: #0c5460;
        border-color: #bee5eb;
    }
</style>

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
                        <h1 class="amount-card-title">Welcome, <span class="span-img3"  style="font-weight: 600;">User</span></h1>
                        <p class="amount-card-subtitle">Secure Card <b>Checkout</b></p>

                        <!-- Orange Buttons -->
                        <div class="checkout-button-grid">
                            <div class="checkout-button">
                                <svg width="49" height="49" viewBox="0 0 49 49" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3.31875 27.0315V32.6044C3.31875 32.8009 3.40038 32.9809 3.56365 33.1446C3.72735 33.3079 3.90742 33.3895 4.10385 33.3895H44.5937C44.7902 33.3895 44.9702 33.3079 45.1339 33.1446C45.2972 32.9809 45.3788 32.8009 45.3788 32.6044V27.0315H3.31875ZM4.10385 0.879517H44.5937C45.6869 0.879517 46.6253 1.27132 47.4089 2.05494C48.1925 2.83855 48.5843 3.77694 48.5843 4.87009V32.6044C48.5843 33.6976 48.1925 34.636 47.4089 35.4196C46.6253 36.2032 45.6869 36.595 44.5937 36.595H32.7477V48.4181L24.3488 44.1928L15.9499 48.4181V36.595H4.10385C3.0107 36.595 2.07232 36.2032 1.2887 35.4196C0.505089 34.636 0.113281 33.6976 0.113281 32.6044V4.87009C0.113281 3.77694 0.505089 2.83855 1.2887 2.05494C2.07232 1.27132 3.0107 0.879517 4.10385 0.879517ZM3.31875 20.8368H45.3788V4.87009C45.3788 4.67365 45.2972 4.49358 45.1339 4.32989C44.9702 4.16662 44.7902 4.08498 44.5937 4.08498H4.10385C3.90742 4.08498 3.72735 4.16662 3.56365 4.32989C3.40038 4.49358 3.31875 4.67365 3.31875 4.87009V20.8368ZM3.31875 32.6044V4.08498H4.10385C3.90742 4.08498 3.72735 4.16662 3.56365 4.32989C3.40038 4.49358 3.31875 4.67365 3.31875 4.87009V32.6044C3.31875 32.8009 3.40038 32.9809 3.56365 33.1446C3.72735 33.3079 3.90742 33.3895 4.10385 33.3895H3.31875V32.6044Z" fill="white"/>
                                </svg>

                            Card
                            </div>
                            <div class="checkout-button">
                                <svg width="42" height="48" viewBox="0 0 42 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M31.053 36.6758C32.095 36.6758 32.9807 36.3018 33.7103 35.5539C34.4398 34.806 34.8046 33.9151 34.8046 32.8814C34.8046 31.8472 34.4384 30.9639 33.706 30.2315C32.9736 29.4991 32.0903 29.1329 31.0561 29.1329C30.0219 29.1329 29.1309 29.4969 28.3829 30.2248C27.635 30.9531 27.2611 31.8374 27.2611 32.8777C27.2611 33.9176 27.6348 34.8107 28.3823 35.557C29.1299 36.3029 30.0201 36.6758 31.053 36.6758ZM31.0126 44.1782C32.3432 44.1782 33.5498 43.8926 34.6322 43.3213C35.715 42.7496 36.6165 41.951 37.3366 40.9254C36.3438 40.3615 35.3263 39.9364 34.2844 39.6501C33.2429 39.3639 32.1596 39.2208 31.0346 39.2208C29.9093 39.2208 28.8185 39.3639 27.7622 39.6501C26.7059 39.9364 25.6949 40.3615 24.729 40.9254C25.4495 41.951 26.3442 42.7496 27.4132 43.3213C28.4821 43.8926 29.6819 44.1782 31.0126 44.1782ZM18.4662 47.3709C13.187 45.9744 8.80019 42.8786 5.30589 38.0835C1.8116 33.2884 0.0644531 27.8335 0.0644531 21.7188V7.75864L18.4662 0.869629L36.868 7.75864V22.9456C36.39 22.7399 35.8778 22.5494 35.3315 22.3739C34.7851 22.1981 34.2697 22.0703 33.7851 21.9906V9.88957L18.4662 4.22611L3.14736 9.88957V21.7188C3.14736 24.4734 3.57306 27.0552 4.42445 29.4641C5.27584 31.8731 6.3965 34.0388 7.78645 35.9612C9.17599 37.8836 10.7553 39.5224 12.5243 40.8775C14.2933 42.2327 16.0979 43.2334 17.9381 43.8795L18.0086 43.8556C18.2385 44.3569 18.5505 44.8812 18.9447 45.4283C19.3389 45.9759 19.7178 46.4341 20.0813 46.8029C19.8118 46.9395 19.5401 47.0479 19.2661 47.128C18.9917 47.2082 18.7251 47.2891 18.4662 47.3709ZM31.1113 47.4181C28.1605 47.4181 25.6456 46.3744 23.5666 44.2868C21.4876 42.1992 20.4481 39.6927 20.4481 36.7672C20.4481 33.7931 21.4874 31.2634 23.566 29.1783C25.645 27.0928 28.1668 26.05 31.1316 26.05C34.0689 26.05 36.5813 27.0928 38.6689 29.1783C40.7565 31.2634 41.8003 33.7931 41.8003 36.7672C41.8003 39.6927 40.7565 42.1992 38.6689 44.2868C36.5813 46.3744 34.0621 47.4181 31.1113 47.4181Z" fill="white"/>
                                </svg>

                            Security
                            </div>
                            <div class="checkout-button">
                                <svg width="46" height="38" viewBox="0 0 46 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.509766 37.4581V28.493H3.1397V34.8282H9.47487V37.4581H0.509766ZM36.524 37.4581V34.8282H42.8592V28.493H45.4891V37.4581H36.524ZM6.08882 31.9356V6.33068H10.2749V31.9356H6.08882ZM12.3895 31.9356V6.33068H14.5385V31.9356H12.3895ZM18.7246 31.9356V6.33068H22.9887V31.9356H18.7246ZM25.1247 31.9356V6.33068H31.4389V31.9356H25.1247ZM33.5749 31.9356V6.33068H35.7239V31.9356H33.5749ZM37.8384 31.9356V6.33068H39.8886V31.9356H37.8384ZM0.509766 9.79469V0.82959H9.47487V3.45952H3.1397V9.79469H0.509766ZM42.8592 9.79469V3.45952H36.524V0.82959H45.4891V9.79469H42.8592Z" fill="white"/>
                                </svg>

                            QR Code
                            </div>
                            <div class="checkout-button">
                                <svg width="46" height="22" viewBox="0 0 46 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.0494 21.8882H10.838C7.8654 21.8882 5.33153 20.8406 3.23642 18.7455C1.14131 16.6508 0.09375 14.1173 0.09375 11.1451C0.09375 8.17288 1.14131 5.63902 3.23642 3.54351C5.33153 1.44761 7.8654 0.399658 10.838 0.399658H20.0494V3.37424H10.838C8.68134 3.37424 6.84723 4.12903 5.33567 5.63862C3.82412 7.1486 3.06834 8.98054 3.06834 11.1344C3.06834 13.2887 3.82412 15.1238 5.33567 16.6397C6.84723 18.1556 8.68134 18.9136 10.838 18.9136H20.0494V21.8882ZM13.8576 12.6312V9.65662H31.3245V12.6312H13.8576ZM25.1179 21.8882V18.9136H34.3293C36.486 18.9136 38.3201 18.1588 39.8316 16.6492C41.3432 15.1392 42.099 13.3073 42.099 11.1534C42.099 8.99909 41.3432 7.16399 39.8316 5.64809C38.3201 4.13219 36.486 3.37424 34.3293 3.37424H25.1179V0.399658H34.3293C37.3019 0.399658 39.8358 1.44721 41.9309 3.54233C44.026 5.63704 45.0736 8.17051 45.0736 11.1427C45.0736 14.115 44.026 16.6488 41.9309 18.7443C39.8358 20.8402 37.3019 21.8882 34.3293 21.8882H25.1179Z" fill="white"/>
                                </svg>


                            PayLink
                            </div>
                            <div class="checkout-button">
                                <svg width="35" height="38" viewBox="0 0 35 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.808594 37.4582V0.307983L3.55248 2.74672L6.37216 0.307983L9.19184 2.74672L12.0115 0.307983L14.8317 2.74672L17.6514 0.307983L20.471 2.74672L23.2912 0.307983L26.1109 2.74672L28.9306 0.307983L31.7503 2.74672L34.4941 0.307983V37.4582L31.7503 35.0194L28.9306 37.4582L26.1109 35.0194L23.2912 37.4582L20.471 35.0194L17.6514 37.4582L14.8317 35.0224L12.0115 37.4582L9.19184 35.0224L6.37216 37.4582L3.55248 35.0224L0.808594 37.4582ZM6.18392 27.2965H29.2124V24.8073H6.18392V27.2965ZM6.18392 20.128H29.2124V17.6382H6.18392V20.128ZM6.18392 12.9257H29.2124V10.4364H6.18392V12.9257ZM3.29835 33.2871H32.0044V4.47904H3.29835V33.2871Z" fill="white"/>
                                    </svg>

                            Receipt
                            </div>
                        </div>

                        {{-- <!-- Payment Logos -->
                        <div class="checkout-payment-grid">
                            <div class="checkout-payment">
                                <img src="{{ asset('assets/img/visa.png') }}" alt="Visa" data-method="visa">
                            </div>
                            <div class="checkout-payment">
                                <img src="{{ asset('assets/img/mastercard.png') }}" alt="MasterCard" data-method="mastercard">
                            </div>
                            <div class="checkout-payment">
                                <img src="{{ asset('assets/img/apple-pay.png') }}" alt="Apple" data-method="applePay">
                            </div>
                        </div>

                        <a href="{{ url('/invoice') }}"><button id="payNowBtn" class="amount-next-btn mt-5">Pay Now</button></a> --}}

                        <div class="checkout-payment-grid">
                            <div class="checkout-payment payment-option" data-method="visa">
                                <img src="{{ url('assets/img/visa.png') }}" alt="Visa">
                            </div>
                            <div class="checkout-payment payment-option" data-method="mastercard">
                                <img src="{{ url('assets/img/mastercard.png') }}" alt="MasterCard">
                            </div>
                            <div class="checkout-payment payment-option" data-method="applePay">
                                <img src="{{ url('assets/img/apple-pay.png') }}" alt="Apple Pay">
                            </div>
                        </div>

                        <!-- Crypto Payment Section -->
                        <div class="crypto-payment-section mt-4">
                            <h4 class="text-center mb-3 text-white">Or Pay with Cryptocurrency</h4>
                            <div class="crypto-payment-grid" id="cryptoPaymentGrid">
                                <!-- Cryptocurrencies will be loaded dynamically -->
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading cryptocurrencies...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Error Message Display -->
                        <div id="errorMessage" class="error-message">
                            <strong>Error:</strong> <span id="errorText"></span>
                        </div>

                        {{-- <a href="{{ url('/invoice') }}"><button id="payNowBtn" class="amount-next-btn mt-5" disabled>Pay Now</button></a> --}}

                        <a href="{{ url('/checkout/' . $order->id) }}"><button id="payNowBtn" class="amount-next-btn mt-5" disabled>Pay Now</button></a>
                </div>
              </div>
            </div>

    </div>


      <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        let selectedPaymentMethod = null;
        let selectedCrypto = null;
        
        // Load supported cryptocurrencies
        loadSupportedCrypto();

        function loadSupportedCrypto() {
            $.ajax({
                url: "{{ url('/supported-crypto') }}",
                type: "GET",
                success: function(response) {
                    const cryptoGrid = $('#cryptoPaymentGrid');
                    cryptoGrid.empty();
                    
                    if (response && Object.keys(response).length > 0) {
                        Object.keys(response).forEach(function(key) {
                            const crypto = response[key];
                            const cryptoOption = `
                                <div class="crypto-payment-option" data-crypto="${key}">
                                    <img src="{{ url('assets/img/crypto/') }}/${crypto.icon}" alt="${crypto.name}" style="width: 40px; height: 40px;">
                                    <span>${crypto.symbol}</span>
                                </div>
                            `;
                            cryptoGrid.append(cryptoOption);
                        });
                        
                        // Re-attach click handlers for dynamically loaded elements
                        attachCryptoClickHandlers();
                    } else {
                        cryptoGrid.html('<div class="text-center text-muted">No cryptocurrencies available</div>');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading cryptocurrencies:', xhr);
                    $('#cryptoPaymentGrid').html('<div class="text-center text-danger">Failed to load cryptocurrencies</div>');
                }
            });
        }

        function attachCryptoClickHandlers() {
            // Handle crypto payment selection
            $(".crypto-payment-option").on("click", function () {
                $(".crypto-payment-option").removeClass("selected");
                $(".payment-option").removeClass("selected");
                $(this).addClass("selected");

                selectedCrypto = $(this).data("crypto");
                selectedPaymentMethod = null;
                $("#payNowBtn").prop("disabled", false);
            });
        }

        // Handle fiat payment selection
        $(".payment-option").on("click", function () {
            $(".payment-option").removeClass("selected");
            $(".crypto-payment-option").removeClass("selected");
            $(this).addClass("selected");
            
            selectedPaymentMethod = $(this).data("method");
            selectedCrypto = null;
            $("#payNowBtn").prop("disabled", false);
        });

        // Handle pay now button click
        $("#payNowBtn").on("click", function(e) {
            e.preventDefault();
            
            if (selectedCrypto) {
                // Create crypto payment
                createCryptoPayment();
            } else if (selectedPaymentMethod) {
                // Create fiat payment
                createFiatPayment();
            }
        });

        function createCryptoPayment() {
            $.ajax({
                url: "{{ url('/create-crypto-payment') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    amount: {{ $order->amount }},
                    crypto_currency: selectedCrypto
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect_url;
                    } else {
                        console.log('Crypto Payment Error:', response);
                        showErrorMessage(response.message, response.error_type);
                    }
                },
                error: function(xhr) {
                    console.log('AJAX Error:', xhr);
                    let errorMessage = 'Failed to create crypto payment';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                        showErrorMessage(errorMessage, response.error_type);
                    } catch (e) {
                        showErrorMessage(errorMessage, 'network_error');
                    }
                }
            });
        }

        function createFiatPayment() {
            $.ajax({
                url: "{{ url('/create-fiat-payment') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    amount: {{ $order->amount }},
                    fiat_method: selectedPaymentMethod
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect_url;
                    } else {
                        console.log('Fiat Payment Error:', response);
                        showErrorMessage(response.message, response.error_type);
                    }
                },
                error: function(xhr) {
                    console.log('AJAX Error:', xhr);
                    let errorMessage = 'Failed to create fiat payment';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                        showErrorMessage(errorMessage, response.error_type);
                    } catch (e) {
                        showErrorMessage(errorMessage, 'network_error');
                    }
                }
            });
        }

        function showErrorMessage(message, errorType) {
            const errorDiv = $('#errorMessage');
            const errorText = $('#errorText');
            
            // Hide any existing error messages
            errorDiv.removeClass('show nowpayments-error network-error');
            
            // Set the error message
            errorText.text(message);
            
            // Add appropriate CSS class based on error type
            if (errorType) {
                errorDiv.addClass(errorType);
            }
            
            // Show the error message
            errorDiv.addClass('show');
            
            // Scroll to error message
            errorDiv[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Hide error after 10 seconds
            setTimeout(function() {
                errorDiv.removeClass('show');
            }, 10000);
        }
    });
</script>


@endsection
