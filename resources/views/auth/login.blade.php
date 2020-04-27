<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>FingerTips | Login</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- base:css -->
  <link rel="stylesheet" href="{{asset('/public/vendors/mdi/css/materialdesignicons.min.css')}}">
  <link rel="stylesheet" href="{{asset('/public/vendors/base/vendor.bundle.base.css')}}">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{asset('/public/css/style.css')}}">
  <!-- endinject -->
  <link rel="shortcut icon" href="{{asset('/public/images/favicon.png')}}" />
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="main-panel">
        <div class="content-wrapper d-flex align-items-center auth px-0">
          <div class="row w-100 mx-0">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                <div class="brand-logo">
                  <img src="{{asset('/public/images/logo.png')}}" alt="logo">
                </div>
                <p>
                  {{ $errors->first('email') }}
                  {{ $errors->first('password') }}
                 <div class="box-header ">
                   @if (session()->has('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session()->has('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                        </div>
                    @endif
                    </div>
              </p>
                <!-- <h4>Hello! let's get started</h4> -->
                <!-- <h6 class="font-weight-light">Sign in to continue.</h6> -->
                <form class="pt-3" action="{{ route('postlogin') }}" method="POST">
                @csrf
                  <div class="form-group { $errors->has('email') ? ' has-error' : '' }}">
                    <input type="email" required name="email" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Username">
                    @if (session()->has('error'))
                        <span class="help-block">
                            <strong style="color: #e23333">{{ session('error') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                    <input type="password" name="password" required  class="form-control form-control-lg" id="exampleInputPassword1" placeholder="Password">
                  </div>
                  <div class="mt-3">
                  <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" >
                                    {{ __('Login') }}
                                </button>
                    <!-- <a class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" href="../../index.html">SIGN IN</a> -->
                  </div>
                  <div class="my-2 d-flex justify-content-between align-items-center">
                    
                    <a href="{{ route('password.reset') }}" class="auth-link text-black">Forgot password?</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- base:js -->
  <script src="{{asset('/public/vendors/base/vendor.bundle.base.js')}}"></script>
  <!-- endinject -->
  <!-- inject:js -->
  <script src="{{asset('/public/js/template.js')}}"></script>
  <!-- endinject -->
</body>

</html>
