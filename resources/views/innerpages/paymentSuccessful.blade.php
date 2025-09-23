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
                        <p>Your payment has been completed.</p>
                        <a href="{{ url('/dashboard') }}"><button class="amount-next-btn">Go to Home</button></a>
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
</main>



@endsection
