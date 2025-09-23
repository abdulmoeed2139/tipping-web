@extends('layouts.master')

@section('content')
<main id="mainContent">
    <div class="container login-container">
        <div class="row ">

          <!-- Left Side (Form) -->
          <div class="col-md-5 loginCol">
          <img src="assets/img/Logo.png" alt="Login image" class="img-fluid logo-img" style="margin-bottom: 40px;">

          <div class="col-md-12 login-left">


            <h2>Create Account</h2>
            <p>Sign up to start your journey with us</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

              <div class="mb-3">
                    <label class="form-label" for="name">Full Name</label>
                    <input id="name" placeholder="Enter Name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
              </div>

              <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                    <input id="email" placeholder="Enter email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
              </div>
              <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Make a strong password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
              </div>
              <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input id="password-confirm" type="password" class="form-control" placeholder="Make a strong password" name="password_confirmation" required autocomplete="new-password">
              </div>


              <button type="submit" class="btn btn-login w-100 mt-3">Sign Up</button>
            </form>

            <div class="text-center mt-3">
                Already have an account? <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Log In</a>
            </div>
          </div>
          </div>

          <!-- Right Side (Welcome) -->
          <div class="col-md-6 login-right text-center text-md-start">
            <h1>Welcome User!</h1>
            <h2>Please Create Your <span class="span-img">Pobay</span> Account</h2>
            <p class="mt-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </p>
            <img src="assets/img/signup-img.png" alt="Login image">
          </div>
        </div>
      </div>


      <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

</main>

@endsection
