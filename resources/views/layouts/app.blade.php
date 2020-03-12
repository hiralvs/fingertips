<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="_token" content="{{csrf_token()}}" />
    <title>Kapella Bootstrap Admin Dashboard Template</title>
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
    <script src="{{ asset('/public/js/jquery.min.js') }}"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="{{ asset('/public/js/jquery-ui.min.js') }}"></script>
        <link rel="stylesheet" href="{{asset('/public/css/dataTables.bootstrap.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{ asset('/public/css/jquery.multiselect.css')}}"/>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
       
  </head>
  <body>
    <div class="container-scroller">
		<!-- partial:partials/_horizontal-navbar.html -->
    <div class="horizontal-menu">
	@include('includes/header')
	@include('includes/menu')
    
    </div>
    <!-- partial -->
		<div class="container-fluid page-body-wrapper">
			<div class="main-panel">
			@yield('content')

				<!-- partial:partials/_footer.html -->
				@include('includes/footer')
				<!-- partial -->
			</div>
			<!-- main-panel ends -->
		</div>
		<!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- base:js -->
    <script src="{{asset('/public/vendors/base/vendor.bundle.base.js')}}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <!-- End plugin js for this page-->
    <!-- inject:js -->
    <script src="{{asset('/public/js/template.js')}}"></script>
    <!-- endinject -->
    <!-- plugin js for this page -->
    <!-- End plugin js for this page -->
    <script src="{{asset('/public/vendors/chart.js/Chart.min.js')}}"></script>
    <script src="{{asset('/public/vendors/progressbar.js/progressbar.min.js')}}"></script>
		<script src="{{asset('/public/vendors/chartjs-plugin-datalabels/chartjs-plugin-datalabels.js')}}"></script>
		<script src="{{asset('/public/vendors/justgage/raphael-2.1.4.min.js')}}"></script>
		<script src="{{asset('/public/vendors/justgage/justgage.js')}}"></script>
    <!-- Custom js for this page-->
    <script src="{{asset('/public/js/dashboard.js')}}"></script>
    <!-- End custom js for this page-->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>  
    <script src="{{ asset('/public/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/public/js/dataTables.bootstrap.min.js') }}"></script>

<script src="{{ asset('/public/js/jquery.multiselect.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>  
<script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>

  </body>
</html>