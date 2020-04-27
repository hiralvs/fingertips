<?php 
                    $user = Auth::user();
                    $id = '';
                    $default_img = config('constants.NO_IMG');
                    $path = URL::to('/public/img/');
                    $url =env('APP_URL');
                    $profilepic = $path . '/' . $default_img;
                    $name = '';
                    if(!empty($user->profile_pic) && $user->profile_pic != 'null'){
                      $profilepic = $url.'public/upload/'.$user->profile_pic;
                    }
                    if(!empty($user->id)){
                        $id = $user->id;
                    }
                    if(!empty($user->name)){
                        $name = $user->name;
                    }
                ?>
<nav class="navbar top-navbar col-lg-12 col-12 p-0">
        <div class="container-fluid">
          <div class="navbar-menu-wrapper d-flex align-items-center justify-content-between">
            <div class="navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo" href="index.html"><img src="{{asset('/public/images/fingertips.png')}}" style="margin-bottom:5px;" alt="logo"/></a>
                <a class="navbar-brand brand-logo-mini" href="index.html"><img src="{{asset('/public/images/logo-mini.svg')}}" alt="logo"/></a>
            </div>
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item nav-profile dropdown">
                  <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <span class="nav-profile-name">{{ $name}}</span>
                    <span class="online-status"></span>
                    <img src="{{$profilepic}}" alt="profile"/>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                      <a class="dropdown-item" href="{{url('/logout')}}"> 
                        <i class="mdi mdi-logout text-primary"></i>
                        Logout
                      </a>
                  </div>
                </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="horizontal-menu-toggle">
              <span class="mdi mdi-menu"></span>
            </button>
          </div>
        </div>
      </nav>