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
                        <h1 class="amount-card-title">Welcome, <span class="span-img3"  style="font-weight: 600;">User</span></h1>
                        <p class="amount-card-subtitle">Redirecting to <span style="font-weight: 600;">secure checkout</span></p>
                        <p>Please do not close this window</p>


                        <p class="amount-card-subtitle"><span style="font-weight: 600;">Invoice Here</span></p>
                        <!-- Invoice Box -->
                        <div class="invoice-box">
                            <svg width="34" height="39" viewBox="0 0 34 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.291016 38.1028V0.952606L3.0349 3.39134L5.85458 0.952606L8.67426 3.39134L11.4939 0.952606L14.3141 3.39134L17.1338 0.952606L19.9535 3.39134L22.7736 0.952606L25.5933 3.39134L28.413 0.952606L31.2327 3.39134L33.9766 0.952606V38.1028L31.2327 35.6641L28.413 38.1028L25.5933 35.6641L22.7736 38.1028L19.9535 35.6641L17.1338 38.1028L14.3141 35.667L11.4939 38.1028L8.67426 35.667L5.85458 38.1028L3.0349 35.667L0.291016 38.1028ZM5.66634 27.9412H28.6949V25.4519H5.66634V27.9412ZM5.66634 20.7726H28.6949V18.2828H5.66634V20.7726ZM5.66634 13.5703H28.6949V11.0811H5.66634V13.5703ZM2.78077 33.9317H31.4868V5.12367H2.78077V33.9317Z" fill="#665C5C"/>
                            </svg>

                            Invoice
                        </div>



                        <div class="invoice-actions d-flex align-items-center justify-content-center">
                         <a href="{{ url('/order/'.$order->id.'/failed') }}"> <button class="invoiceFaliurebtn">Failure</button></a>
                         <a href="{{ url('/order/'.$order->id.'/success') }}"><button class="invoiceSuccessbtn">Success</button></a>
                        </div>
                </div>
              </div>
            </div>

    </div>


      <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>
</main>



@endsection
