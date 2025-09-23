@extends('layouts.master')

@section('content')
    <style>
        .generateUrl,
        .show_qr {
            padding: 12px 45px;
            border-radius: 12px;
            font-weight: 600;
            color: #0A0909;
            border: 1px solid #717171;
            font-size: 20px;
            background: #F6F6F6;
        }

        button:disabled,
        button[disabled] {
            opacity: 0.6;
            /* halka grey effect */
            cursor: not-allowed;
            /* cursor bhi disable jaisa */
            filter: grayscale(40%);
        }

        #qrModal .modal-content {
            width: max-content !important;
        }

        #qrModal .modal-dialog {
            max-width: max-content !important;
        }



        @media screen and (max-width:600px) {

            body #qrModal .modal-dialog {
                max-width: 100% !important;
                width: 100% !important;
                display: flex;
                justify-content: center;
                align-items: center;
            }
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
                            <h1 class="amount-card-title">Welcome, <span class="span-img3"
                                    style="font-weight: 600;">User</span></h1>
                            <p class="amount-card-subtitle">Select <strong>Amount</strong></p>


                            <div class="amount-options">
                                <button class="amount-option-btn">$19</button>
                                <button class="amount-option-btn">$49</button>
                                <button class="amount-option-btn">$99</button>
                                <button class="amount-option-btn">Custom</button>
                            </div>
                            <div class="amount-custom-input" id="custom-input-box">
                                <input type="number" min="5" max="9999"
                                    placeholder="Enter amount (min $5 and max $9999)" class="amount-input-field"
                                    id="customAmount" disabled />
                                <button class="amount-input-btn">$</button>
                            </div>

                            <!-- hidden input for storing selected amount
                            <input type="hidden" id="selectedAmount" name="selected_amount" value=""> -->

                        </div>



                        {{-- <a href="{{ url('/select-merchant') }}"> <button class="amount-next-btn">Next</button></a> --}}

                        <form action="{{ url('/create-order') }}" method="POST" id="orderForm">
                            @csrf
                            <input type="hidden" name="amount" id="selectedAmount">
                            <button type="submit" class="amount-next-btn">Check Out</button>
                        </form>

                        <div class="amount-actions">
                            <button id="generateUrl" class="generateUrl" disabled>Generate Link</button>
                            <button id="show_qr" class="show_qr" disabled data-bs-toggle="modal"
                                data-bs-target="#qrModal">View QR</button>
                        </div>
                    </div>
                </div>
          

            </div>

        </div>


        <!-- Scroll Top -->
        <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
                class="bi bi-arrow-up-short"></i></a>

        <!-- Preloader -->
        <div id="preloader"></div>

    </main>


    <!-- Modal for QR -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 text-center">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">Your QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="qrcode"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    @if (session('error'))
        <div class="alert alert-danger text-center">
            {{ session('error') }}
        </div>
    @endif

    <script>
        $(document).ready(function() {

            let $selectedAmount = $("#selectedAmount");
            let $generateBtn = $("#generateUrl");
            let $qrBtn = $("#show_qr");

            function updateButtons() {
                let amount = parseFloat($selectedAmount.val());
                if (amount && amount >= 5 && amount <= 9999) {
                    $generateBtn.prop("disabled", false);
                    $qrBtn.prop("disabled", false);
                } else {
                    $generateBtn.prop("disabled", true);
                    $qrBtn.prop("disabled", true);
                }
            }


            $(".amount-option-btn").click(function() {
                // sab buttons se inline style hata do
                $(".amount-option-btn").removeAttr("style");

                let value = $(this).text().replace('$', '').trim();

                if ($(this).text().trim() === "Custom") {
                    // agar Custom hai to input enable & empty
                    $("#customAmount").prop("disabled", false).val("").focus();
                    $("#selectedAmount").val("");
                } else {
                    // agar normal button hai to value input me render ho jaaye
                    $("#customAmount").prop("disabled", true).val(value);
                    $("#selectedAmount").val(value);
                }

                updateButtons();

                // clicked button ko highlight karo
                $(this).css({
                    "background": "linear-gradient(to right, #ff0066, #ff6600)",
                    "color": "#fff",
                    "border-radius": "12px"
                });
            });

            // custom input change hone par hidden input update karo
            $("#customAmount").on("input", function() {
                let val = $(this).val();
                if (val >= 5 && val <= 9999) {
                    $("#selectedAmount").val(val);
                } else {
                    $("#selectedAmount").val("");
                }
                updateButtons();
            });

            // Generate Link
            $generateBtn.on("click", function() {
                let amount = $selectedAmount.val();
                $.ajax({
                    url: "{{ url('/generate-link') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        amount: amount
                    },
                    success: function(response) {
                        if (response.success) {
                            latestLink = response.url;
                            navigator.clipboard.writeText(latestLink);
                            alert("Payment link copied: " + latestLink);
                        }
                    }
                });
            });

            // // Show QR
            // $qrBtn.on("click", function () {
            //     if (latestLink) {
            //         $("#qrcode").html("");
            //         new QRCode(document.getElementById("qrcode"), {
            //             text: latestLink,
            //             width: 200,
            //             height: 200
            //         });
            //     } else {
            //         alert("Please generate a link first.");
            //     }
            // });


            $qrBtn.on("click", function() {
                let amount = $selectedAmount.val();

                $.ajax({
                    url: "{{ url('/generate-link') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        amount: amount
                    },
                    success: function(response) {
                        if (response.success) {
                            latestLink = response.url;
                            navigator.clipboard.writeText(latestLink);
                            // alert("Payment link copied: " + latestLink);

                            // QR generate
                            $("#qrcode").html(""); // old QR clear
                            new QRCode(document.getElementById("qrcode"), {
                                text: latestLink,
                                width: 200,
                                height: 200
                            });
                        }
                    }
                });
            });


        });
    </script>
@endsection
