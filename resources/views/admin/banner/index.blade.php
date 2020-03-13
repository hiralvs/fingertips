@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-sm-6 mb-4 mb-xl-0">
			<div class="d-lg-flex align-items-center">
            <h4 class="card-title" style="float:left">{{$title ?? ''}}</h4>
            </div>
        </div>
        <div class="col-sm-6">
			<div class="d-flex align-items-center justify-content-md-end">
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="waves-effect waves-light btn btn_box_shadow btn element" data-toggle="modal" data-target="#addBanner" tabindex="" style="">
                        ADD NEW
                    </a>
                        <!-- <button type="button" class="btn btn-outline-inverse-info btn-icon-text">
                            Print
                            <i class="mdi mdi-printer btn-icon-append"></i>                          
                        </button> -->
                </div>               
            </div>
        </div>
    </div>
    <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title" style="float:left">{{$title ?? ''}}</h4>
                  <!-- <select name="per_page" id="per_page" style="float:right;">
                  <option value="10">10</option>
                  <option value="20">20</option>
                  <option value="30">30</option>
                  </select> -->
                  <div class="box-header ">
                        @if (session()->has('success'))
                        <h4 style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="usertableData">
                      <thead>
                        <tr>
                          <th>@sortablelink('location')</th>
                          <th>@sortablelink('bannerimage')</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                            <td>{{$value->location}}</td>
                            <td>
                            @if($value->bannerimage!= null)
                                <img src="{{asset('public/upload/banner/')}}{{'/'.$value->bannerimage}}" alt="">
                            @else

                            @endif
                            </td>
                            <td><a class="edit open_modal" data-toggle="modal" data-target="#editBanner{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Banner?')" href="{{route('banner.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editBanner{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Banner</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="adduserform" id="editbannerform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="exampleInputLocation">Location</label>
                                                <select class="form-control" id="location" name="location">
                                                    <option value="" selected="">Location</option>
                                                    <option value="home" {{ $value->location == 'home' ? 'selected' : ''}}>Home</option>
                                                    <option value="event" {{ $value->location == 'event' ? 'selected' : ''}}>Event</option>
                                                    <option value="mall" {{ $value->location == 'mall' ? 'selected' : ''}}>Mall</option>
                                                    <option value="attraction {{ $value->location == 'attraction' ? 'selected' : ''}}">Attraction</option>
                                                </select>
                                            <input  type="hidden" name="id" value="{{$value->id}}"> 
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="exampleSelectPhoto">Banner Photo</label>
                                                <input type="file" class="file-upload-default" name="bannerimage">
                                                <div class="input-group col-xs-12">
                                                    <input type="text" value="{{ $value->bannerimage != null ? $value->bannerimage : ''}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                    <span class="input-group-append">
                                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="exampleInputStatus">Type</label>
                                                <select class="form-control" id="type" name="type">
                                                    <option value="" selected="">Select</option>
                                                    <option value="inapp" {{ $value->type == 'inapp' ? 'selected' : ''}}>In App</option>
                                                    <option value="outsideapp {{ $value->type == 'outsideapp' ? 'selected' : ''}}">Outside App</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4" id="ema">
                                                <label for="exampleInputStatus">Select</label>
                                                <select class="form-control" id="ema" name="ema" >
                                                    <option value="" selected="">EMA</option>
                                                    <option value="0" {{ $value->ema == 'event' ? 'selected' : ''}}>Event</option>
                                                    <option value="1" {{ $value->ema == 'mall' ? 'selected' : ''}}>Mall</option>
                                                    <option value="2" {{ $value->ema == 'attraction' ? 'selected' : ''}}>Attraction</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4" id="property_user_id">
                                                <label for="exampleInputStatus">Property</label>
                                                <select name="property_user_id" id="property_user_id" class="form-control">
                                                    <option value="">Select One</option>
                                                    @foreach ($property_user_id as $banner)
                                                        <option value="{{ $banner->id }}"  {{ $value->property_user_id ==$banner->id ? 'selected' : ''}}>{{ $banner->name }}</option>
                                                    @endforeach 
                                                </select>
                                            </div>

                                        </div>
                                        <div class="row">
                                            
                                        </div>
                                    </div>
                                        <div class="row">
                                            <div class="form-group col-md-2" style="text-align:center">
                                            <button type="button" class="btn btn-primary mr-2 editBannerSubmit" data-id="{{$value->id}}" id="editBannerSubmit">Submit</button>
                                            </div>
                                            <div class="form-group col-md-2" >
                                                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- edit /.modal -->
                        @endforeach
                        @endif

                      </tbody>
                    </table>

                  </div>
                  <div id="paging">
                  {{ $data->links() }}
                  </div>
                </div>
              </div>
            </div>
    </div>
</div>
<!-- content-wrapper ends -->
<script src="{{asset('public/js/file-upload.js')}}" ></script>

<script>
    $('.editBannerSubmit').click(function(e){
        var id = $(this).data('id');
        var formData = new FormData($("#editbannerform"+id)[0]);
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('banner.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setInterval(function(){ 
                        $('#editBanner'+id).modal('hide');
                        window.location.reload();
                    }, 3000);
                }
                else
                {
                    $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');
                    // $.each(result.errors, function(key, value){
                    //     $('.alert-danger').show();
                    //     $('.alert-danger').append('<li>'+value+'</li>');
                    // });
                }
                }
            });
        });

// $(document).ready(function(){
//     $('.editUserSubmit').click(function(e){
//         var id = $(this).data('id');
//         var formData = new FormData($("#edituserform"+id)[0]);
//             e.preventDefault();
//             $.ajaxSetup({
//                 headers: {
//                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
//                 }
//             });
//             $.ajax({
//                 url: "{{ route('user.update') }}",
//                 method: 'post',
//                 cache: false,
//                 contentType: false,
//                 processData: false,
//                 data: formData,
//                 success: function(result){
//                 if(result.status == true)
//                 {
//                     $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
//                     setInterval(function(){ 
//                         $('#editUser'+id).modal('hide');
//                         window.location.reload();
//                     }, 3000);
//                 }
//                 else
//                 {
//                     $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');


//                     // $.each(result.errors, function(key, value){
//                     //     $('.alert-danger').show();
//                     //     $('.alert-danger').append('<li>'+value+'</li>');
//                     // });
//                 }
//                 }
//             });
//         });

//         $('#addUserSubmit').click(function(e){
//         var formData = new FormData($("#adduserform")[0]);
//             e.preventDefault();
//             $.ajaxSetup({
//                 headers: {
//                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
//                 }
//             });
//             $.ajax({
//                 url: "{{ route('adduser') }}",
//                 method: 'post',
//                 cache: false,
//                 contentType: false,
//                 processData: false,
//                 data: formData,
//                 success: function(result){
//                 if(result.status == true)
//                 {
//                         var data = result.data;
//                     $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
//                     setInterval(function(){ 
//                         $('#addUser').modal('hide');
//                         //  $('#done-message').addClass('hide');
//                     }, 3000);
                    
//                     var findnorecord = $('#usertableData tr.norecord').length;
//                     if(findnorecord > 0){
//                         $('#usertableData tr.norecord').remove();
//                         }
                    
//                     var profilepic = status = '';
//                     if(data.profile_pic != null)
//                     {
//                         profilepic = data.profile_pic;
//                     }
//                     if(data.status == 0)
//                     {
//                         status = 'Active';
//                     }
//                     else
//                     {
//                         status = 'Inactive';
//                     }
//                     if(data.created_at)
//                     {
//                         var cdate = "<?php //echo date("d F Y",strtotime($value->created_at)) ?>";
//                     }
//                     var deleteurl = '{{ route("user.delete", ":id") }}';
//                     deleteurl = deleteurl.replace(':id', data.id);
//                     var tr_str = "<tr>"+
//                     "<td>"+profilepic+"</td>" +
//                     "<td>"+data.unique_id+"</td>" +
//                     "<td>"+data.name+"</td>" +
//                     "<td>"+data.email+"</td>"+
//                     "<td>"+data.gender+"</td>" +
//                     "<td>"+data.role+"</td>" +
//                     "<td>"+status+"</td>" +
//                     "<td>"+cdate+"</td>" +
//                     "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editUser'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this User?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
//                     "</tr>";
//                     console.log(tr_str);
//                     $("#usertableData tbody").prepend(tr_str);
//                     $("#adduserform")[0].reset();
                    
//                 }
//                 else
//                 {
//                     $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');


//                     // $.each(result.errors, function(key, value){
//                     //     $('.alert-danger').show();
//                     //     $('.alert-danger').append('<li>'+value+'</li>');
//                     // });
//                 }
//                 }
//             });
//         });
//     $(document).on('click','#search',function(){ 
//         $.ajaxSetup({
//                 headers: {
//                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
//                 }
//             });       
//         $.ajax({
//                 url: "{{route('user.search')}}",
//                 method: 'post',
//                 data: {'search':$("#searchtext").val()},
//                 success: function(result){
//                 if(result.status == true)
//                 {
//                     var data = result.data;
                    
                    
//                     var findnorecord = $('#usertableData tr.norecord').length;
//                     if(findnorecord > 0){
//                         $('#usertableData tr.norecord').remove();
//                         }
                    
//                     var profilepic = status = '';
//                     if(data.profile_pic != null)
//                     {
//                         profilepic = data.profile_pic;
//                     }
//                     if(data.status == 0)
//                     {
//                         status = 'Active';
//                     }
//                     else
//                     {
//                         status = 'Inactive';
//                     }
//                     if(data.created_at)
//                     {
//                         var cdate = "<?php// echo date("d F Y",strtotime($value->created_at)) ?>";
//                     }
//                     var deleteurl = '{{ route("user.delete", ":id") }}';
//                     deleteurl = deleteurl.replace(':id', data.id);
//                     var tr_str = "<tr>"+
//                     "<td>"+profilepic+"</td>" +
//                     "<td>"+data.unique_id+"</td>" +
//                     "<td>"+data.name+"</td>" +
//                     "<td>"+data.email+"</td>"+
//                     "<td>"+data.gender+"</td>" +
//                     "<td>"+data.role+"</td>" +
//                     "<td>"+status+"</td>" +
//                     "<td>"+cdate+"</td>" +
//                     "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editUser'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this User?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
//                     "</tr>";
//                     console.log(tr_str);
//                     $("#usertableData tbody").html(tr_str);
//                     $("#paging").hide();
//                 }
//                 else
//                 {
//                     $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');


//                     // $.each(result.errors, function(key, value){
//                     //     $('.alert-danger').show();
//                     //     $('.alert-danger').append('<li>'+value+'</li>');
//                     // });
//                 }
//                 }
//             });
//     });
   
// });

$('#addBannerSubmit').click(function(e){
        var formData = new FormData($("#addBannerform")[0]);
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addBanner') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $('#addBanner').modal('hide');
                    }, 3000);
                    
                    var findnorecord = $('#bannerstableData tr.norecord').length;
                    if(findnorecord > 0)
                    {
                        $('#bannerstableData tr.norecord').remove();
                    }
                    
                    var bannerimage = '';
                    if(data.bannerimage != null)
                    {
                        bannerpic = data.bannerimage;
                    }
                    var deleteurl = '{{ route("banner.delete", ":id") }}';
                    var imageurl = "{{asset('public/upload/baner/')}}";
                    deleteurl = deleteurl.replace(':id', data.id);
                    var tr_str = "<tr>"+
                    // "<td>"+data.category_name+"</td>" +
                    "<td><img src="+imageurl+"/"+bannerimage+"></td>" +
                    // "<td>"+data.property_user_id+"</td>" +
                    // "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editBanner'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this Category?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    $("#bannerstableData tbody").prepend(tr_str);
                    $("#bannerstableData tbody").append('<div id="editBanner'+data.id+'" class="modal fade"><div class="modal-dialog  modal-xl" role="document"><div class="modal-content"><div class="modal-header"><h1 class="modal-title">Edit Banner</h1></div><div class="modal-body"><p class="statusMsg"></p><form name="editBanner" id="editBannerform'+data.id+'" role="form" method="POST" enctype= "multipart/form-data">@csrf<div class="row"><div class="form-group col-md-4"><label for="exampleInputName">Banner Name</label><input type="text" class="form-control" required id="Banner_name" value="'+data.banner_name+'" name="banner_name" placeholder="Name"><input type="hidden" name="id" value="'+data.id+'"><input type="hidden" name="type" value="banner"></div><div class="form-group col-md-4"><label for="exampleSelectPhoto">Photo</label><input type="file" name="bannerimage" class="file-upload-default"><div class="input-group col-xs-12"><input type="text" value="'+bannerpic+'" class="form-control file-upload-info" disabled placeholder="Upload Image"><span class="input-group-append"><button class="file-upload-browse btn btn-primary" type="button">Upload</button></span></div></div></div><button type="button" class="btn btn-primary mr-2 editBannerSubmit" data-id="'+data.id+'" id="editBannerSubmit">Submit</button><button type="button" class="btn btn-light" data-dismiss="modal">Close</button></form></div></div></div></div>');
                    $("#addBannerform")[0].reset();
                    window.location.reload();
                }
                else
                {
                    $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');
                }
                }
            });
        }); 
$(function () {
        $("#type").change(function () {
            // alert($(this).val());
            if ($(this).val() == 'inapp') {
                // console.log($(this).val());
                $("#ema").show();
                $("#property_user_id").show();
                $("#url").hide();
            }else if ($(this).val() == 'outsideapp'){
                $("#ema").hide();
                $("#property_user_id").hide();
                $("#url").show();
            }else{
                // console.log($(this).val());
                $("#ema").hide();
                $("#property_user_id").hide();
                $("#url").hide();
            } 
        });
    });
</script>
@endsection

<!-- Modal HTML Markup -->
<div id="addBanner" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Banner</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addBanner" id="addBannerform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                <div class="row">
                    {{-- <div class="form-group col-md-3">
                        <label for="exampleInputName">Location</label>
                        <input type="text" required class="form-control" Re id="fullname" name="name" placeholder="Name">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleInputEmail1">Email id</label>
                        <input type="email" required class="form-control" id="email" name="email" placeholder="Email">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleInputPassword">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    </div> --}}
                    <div class="form-group col-md-3">
                        <label for="exampleSelectGender">Location</label>
                        <select class="form-control" name="location" id="exampleSelectLocation">
                            <option value="" selected="">Location</option>
                            <option value="home">Home</option>
                            <option value="event">Event</option>
                            <option value="mall">Mall</option>
                            <option value="attraction">Attraction</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleSelectPhoto">Banner Photo</label>
                        <input type="file" class="form-control" id="photo" name="bannerimage" placeholder="Photo">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="exampleInputStatus">Type</label>
                        <select class="form-control" id="type" name="type">
                            <option value="">--Select--</option>
                            <option value="inapp">In App</option>
                            <option value="outsideapp">Outside App</option>
                        </select>
                    </div>
                        <div class="form-group col-md-4" id="ema" style="display:none;">
                            <label for="exampleInputStatus">EMA</label>
                            <select class="form-control" id="ema" name="ema" >
                                <option value="" selected="">EMA</option>
                                <option value="0">Event</option>
                                <option value="1">Mall</option>
                                <option value="2">Attraction</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4" id="property_user_id" style="display:none;">
                            <label for="exampleInputStatus">Property</label>
                            <select name="property_user_id" id="property_user_id" class="form-control">
                                <option value="" selected="">Property</option>
                                @foreach ($property_user_id as $brand)
                                    <option value="{{ $brand->id }}"  {{ (isset($brand->id) || old('id'))? "":"selected" }}>{{ $brand->name }}</option>
                                @endforeach 
                            </select>
                        </div>
                        <div class="form-group col-md-4" id="url" style="display:none;">
                            <label for="exampleInputName">URL</label>
                            <input type="text" required class="form-control" id="url" name="url" placeholder="url">
                            <input type="hidden" class="form-control" id="type" name="type" value='url'>
                        </div> 
                </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-2" style="text-align:center">
                    <button type="button" class="btn btn-primary mr-2" id="addBannerSubmit">Submit</button>
                    </div>
                    <div class="form-group col-md-2" >
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>   
                    </div>
                </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->