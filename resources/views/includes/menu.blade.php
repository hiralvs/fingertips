<nav class="bottom-navbar">
        <div class="container">
            <ul class="nav page-navigation">
              <li class="nav-item">
                <a class="nav-link" href="{{route('dashboard')}}">
                  <i class="mdi mdi-file-document-box menu-icon"></i>
                  <span class="menu-title">Dashboard</span>
                </a>
              </li>
              <li class="nav-item">
                  <a href="{{route('brand')}}" class="nav-link">
                    <i class="mdi mdi-cube-outline menu-icon"></i>
                    <span class="menu-title">Brands</span>
                    <i class="menu-arrow"></i>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{route('products')}}" class="nav-link">
                    <i class="mdi mdi-cube-outline menu-icon"></i>
                    <span class="menu-title">Products</span>
                    <i class="menu-arrow"></i>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{route('usermanagement')}}" class="nav-link">
                    <i class="mdi mdi-account-multiple menu-icon"></i>
                    <span class="menu-title">User Management</span>
                    <i class="menu-arrow"></i>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{route('shopsmalls')}}" class="nav-link">
                    <i class="mdi mdi-finance menu-icon"></i>
                    <span class="menu-title">Shops & Malls</span>
                    <i class="menu-arrow"></i>
                  </a>
                  <div class="submenu">
                      <ul class="submenu-item">
                        <li class="nav-item"><a class="nav-link" href="{{route('floor')}}">Floor</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{route('directory')}}">Directory</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{route('mallbrands')}}">Brands</a></li>
                        {{-- <li class="nav-item"><a class="nav-link" href="{{route('checkin')}}">Check In</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{route('highlights')}}">Highlights</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{route('orders')}}">Orders</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{route('flashsale')}}">Flash Sale</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{route('sliderimage')}}">Slider Image</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{route('mapimage')}}">Map Image</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{route('photos')}}">Photos</a></li> --}}
                      </ul>
                  </div>
              </li>
              <li class="nav-item">
                  <a href="{{route('event')}}" class="nav-link">
                    <i class="mdi mdi-finance menu-icon"></i>
                    <span class="menu-title">Events</span>
                    <i class="menu-arrow"></i>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{route('attractions')}}" class="nav-link">
                    <i class="mdi mdi-finance menu-icon"></i>
                    <span class="menu-title">Attractions</span>
                    <i class="menu-arrow"></i>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{route('rewards')}}" class="nav-link">
                    <i class="mdi mdi-finance menu-icon"></i>
                    <span class="menu-title">Rewards</span>
                    <i class="menu-arrow"></i>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{route('notifications')}}" class="nav-link">
                    <i class="mdi mdi-finance menu-icon"></i>
                    <span class="menu-title">Notifications</span>
                    <i class="menu-arrow"></i>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{route('trending')}}" class="nav-link">
                    <i class="mdi mdi-emoticon menu-icon"></i>
                    <span class="menu-title">Trending Now</span>
                    <i class="menu-arrow"></i>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{route('sponsors')}}" class="nav-link">
                    <i class="mdi mdi-emoticon menu-icon"></i>
                    <span class="menu-title">Sponsors</span>
                    <i class="menu-arrow"></i>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="mdi mdi-codepen menu-icon"></i>
                    <span class="menu-title">Master</span>
                    <i class="menu-arrow"></i>
                  </a>
                  <div class="submenu">
                      <ul class="submenu-item">
                          <li class="nav-item"><a class="nav-link" href="{{route('banner')}}">Banner</a></li>
                          <li class="nav-item"><a class="nav-link" href="{{route('emacategory')}}">EMA Category</a></li>
                          <li class="nav-item"><a class="nav-link" href="{{route('productcategory')}}">Product Category</a></li>
                          <li class="nav-item"><a class="nav-link" href="{{route('tax')}}">Tax</a></li>
                          <li class="nav-item"><a class="nav-link" href="{{route('help')}}">Help</a></li>
                          <li class="nav-item"><a class="nav-link" href="{{route('privacy')}}">Privacy</a></li>
                          <li class="nav-item"><a class="nav-link" href="{{route('area')}}">Area</a></li>
                          <li class="nav-item"><a class="nav-link" href="{{route('rewardsetting')}}">Reward Setting</a></li>
                          <li class="nav-item"><a class="nav-link" href="{{route('loginprivacy')}}">Login Privacy</a></li>
                      </ul>
                  </div>
              </li>
              <li class="nav-item">
                  <a href="docs/documentation.html" class="nav-link">
                    <i class="mdi mdi-file-document-box-outline menu-icon"></i>
                    <span class="menu-title">Documentation</span></a>
              </li>
            </ul>
        </div>
      </nav>