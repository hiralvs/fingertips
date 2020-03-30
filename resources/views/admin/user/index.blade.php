@extends('layouts.app')

@section('content')

<style>
/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
</style>
<div class="content-wrapper">
    <div class="row">
        <div class="col-sm-6 mb-4 mb-xl-0">
			<div class="d-lg-flex align-items-center">
            <h4 class="card-title" style="float:left">{{$title}}</h4>
            </div>
        </div>
        <div class="col-sm-6">
			<div class="d-flex align-items-center justify-content-md-end">
                <div class="pr-1 mb-3 mb-xl-0">
                    <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text" id="search">
                                <i class="mdi mdi-magnify"></i>
                            </span>
                            </div>
                            <input type="text" class="form-control" placeholder="search" id="searchtext" aria-label="search" aria-describedby="search">
                    </div>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="search" class="btn btn-primary"  tabindex="" style="">FILTER</a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="clear16" class="btn btn-secondary" href="{{route('usermanagement')}}" tabindex="" >
                        CLEAR
                    </a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addUser" tabindex="">
                        ADD NEW
                    </a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="export14" class="btn btn-secondary" onclick="fnExcelReport()"  tabindex="">
                        EXPORT
                    </a>
                        <!-- <button type="button" class="btn btn-outline-inverse-info btn-icon-text">
                            Feedback
                        </button> -->
                </div>                
            </div>
        </div>
    </div>
    <div class="tab">
        <button class="tablinks active admin"  onclick="openCity(event, 'admin')">Admin</button>
        <button class="tablinks customer" onclick="openCity(event, 'customer')">Customer</button>
        <button class="tablinks property_admin" onclick="openCity(event, 'property_admin')">Property Admin</button>
        <button class="tablinks brand_merchant" onclick="openCity(event, 'brand_merchant')">Brand Merchant</button>
    </div>
    <div id="admin" class="tabcontent">
        <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                    <h4 class="card-title" style="float:left">{{$title}}-Admin</h4>
                    <!-- <select name="per_page" id="per_page" style="float:right;">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    </select> -->
                    

                    <div class="box-header ">
                            @if (session()->has('success'))
                            <h4 class="mess" style="text-align: center; color: green;">{{ session('success') }}</h4>
                            @endif
                            @if (session()->has('error'))
                            <h4 class="mess" style="text-align: center; color: red;">{{ session('error') }}</h4>
                            @endif
                        </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="admintableData">
                        <thead>
                            <tr>
                            <th>Image</th>
                            <th>@sortablelink('id')</th>
                            <th>@sortablelink('name')</th>
                            <th>@sortablelink('email')</th>
                            <th>Gender</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>@sortablelink('created_at','Created on')</th>
                            <!-- <th>@sortablelink('created_at','Created on',['filter' => 'active, visible'], ['class' => 'btn btn-block', 'rel' => 'nofollow'])</th> -->
                            <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($admindata) && $admindata->count() > 0)
                                @foreach($admindata as $key => $value)
                            <tr>
                            <td>
                                @if($value->profile_pic!= null)
                                    <img src="{{asset('public/upload/')}}{{'/'.$value->profile_pic}}" alt="">
                                @else

                                @endif
                                </td>
                            <td>{{$value->unique_id}}</td>
                            <td>{{$value->name}}</td>
                            <td>{{$value->email}}</td>
                            <td>{{$value->gender}}</td>
                            <td>{{$value->role}}</td>
                            <td> @if($value->status == '1') 
                                    Inactive
                                @else
                                    Active
                                    @endif
                                </td>
                            <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                            <td><a class="edit open_modal" data-toggle="modal" data-target="#editUser{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                            <a class="delete" onclick="return confirm('Are you sure you want to delete this User?')" href="{{route('user.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                            </tr>
                            <!-- Edit Modal HTML Markup -->
                            <div id="editUser{{$value->id}}" class="modal fade">
                                <div class="modal-dialog  modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title">Edit User</h1>
                                        </div>
                                        <div class="modal-body">
                                        <p class="statusMsg"></p>
                                            <form name="adduserform" id="edituserform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                                @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Name</label>
                                                    <input type="text" class="form-control" required id="fullname" value="{{$value->name}}" name="name" placeholder="Name">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="name-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputEmail1">Email id</label>
                                                    <input type="email" class="form-control" required id="email" name="email" value="{{$value->email}}"  placeholder="Email">
                                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="email-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectGender">Gender</label>
                                                    <select class="form-control" name="gender" id="exampleSelectGender">
                                                    <option value="male" {{ $value->gender == 'male' ? 'selected' : ''}}>Male</option>
                                                    <option value="female" {{ $value->gender == 'female' ? 'selected' : ''}}>Female</option>
                                                    </select>
                                                </div>
                                            
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputRole">Role</label>
                                                    <select class="form-control" id="role" name="role">
                                                        <option value="" selected="">Role</option>
                                                        <option value="admin" {{ $value->role == 'admin' ? 'selected' : ''}} >Admin</option>
                                                        <option value="brand_merchant" {{ $value->role == 'brand_merchant' ? 'selected' : ''}}>Brand Merchant</option>
                                                        <option value="property_admin" {{ $value->role == 'property_admin' ? 'selected' : ''}}>Property Admin</option>
                                                        <option value="customer" {{ $value->role == 'customer' ? 'selected' : ''}}>Customer</option>
                                                    </select>
                                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="role-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Status</label>
                                                    <select class="form-control" id="status" name="status">
                                                        <option value="" selected="">Status</option>
                                                        <option value="0" {{ $value->status == '0' ? 'selected' : ''}}>Active</option>
                                                        <option value="1" {{ $value->status == '1' ? 'selected' : ''}}>Inactive</option>
                                                    </select>
                                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="status-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Photo</label>
                                                    <input type="file" name="profile_pic" class="file-upload-default">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" value="{{ $value->profile_pic != null ? $value->profile_pic : ''}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                        <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputEmail1">Date of Birth</label>
                                                    <input type="date" required class="form-control" id="dob" name="dob" placeholder="dob" value="{{ $value->dob != null ? $value->dob : ''}}">
                                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="dob-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputPassword">Contact</label>
                                                    <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact" value="{{ $value->mobile != null ? $value->mobile : ''}}">
                                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="mobile-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>               
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editUserSubmit" data-id="{{$value->id}}" id="addUserSubmit">Submit</button>
                                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button> 
                                            </form>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- edit /.modal -->
                            @endforeach
                            @else
                            <tr>
                            <td colspan="9">No Records Found</td>
                            </tr>
                            @endif


                        </tbody>
                        </table>

                    </div>
                        <div id="adminpaging">
                        {{ $admindata->appends(['role' => 'admin'])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!--admin tabcontent ends -->
    <div id="customer" class="tabcontent">
        @include('admin/user/customer')
    </div> <!--customer tabcontent ends -->
    <div id="property_admin" class="tabcontent">
        @include('admin/user/propertyadmin')
    </div> <!--PropertyAdmin tabcontent ends -->
    <div id="brand_merchant" class="tabcontent">
        @include('admin/user/brandmerchant')
    </div> <!--BrandMerchant tabcontent ends -->
</div><!-- content-wrapper ends -->

<script src="{{asset('public/js/file-upload.js')}}" ></script>

<script>
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
function fnExcelReport()
{
    $('thead tr th').last().remove();
    var tT = new XMLSerializer().serializeToString(document.querySelector('#admintableData')); //Serialised table
    var tF = 'brand.xls'; //Filename
    var tB = new Blob([tT]); //Blub
    if(window.navigator.msSaveOrOpenBlob){
        //Store Blob in IE
        window.navigator.msSaveOrOpenBlob(tB, tF)
    }
    else{
        //Store Blob in others
        var tA = document.body.appendChild(document.createElement('a'));
        tA.href = URL.createObjectURL(tB);
        tA.download = tF;
        tA.style.display = 'none';
        tA.click();
        tA.parentNode.removeChild(tA)
    }

    $('thead tr').last().append('<th>Action</th>');
}
$(document).ready(function(){

    setTimeout(function(){
        $("h4.mess").remove();
    }, 5000 ); 

    $(".tablinks").removeClass("active");
    var role = "<?php echo $role; ?>";
    $(".tabcontent").css('display','none');
    document.getElementById(role).style.display = "block";
    $("."+role).addClass("active");

    setTimeout(function() {
            $('.box-header').slideUp();
        }, 5000);

    document.getElementById(role).style.display = "block";
    $(document).on('click','.editUserSubmit',function(e){
        var id = $(this).data('id');
        var formData = new FormData($("#edituserform"+id)[0]);
        $( '#name-error'+id ).html( "" );
        $( '#email-error'+id ).html( "" );
        $( '#role-error'+id).html( "" );
        $( '#status-error'+id).html( "" );
        $( '#dob-error'+id ).html( "" );
        $( '#mobile-error'+id ).html( "" );
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('user.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.name){
                        $( '#name-error'+id ).html( result.errors.name[0] );
                    }
                    if(result.errors.email){
                        $( '#email-error'+id ).html( result.errors.email[0] );
                    }
                    if(result.errors.role){
                        $( '#role-error'+id ).html( result.errors.role[0] );
                    }
                    if(result.errors.status){
                        $( '#status-error'+id ).html( result.errors.status[0] );
                    }
                    if(result.errors.dob){
                        $( '#dob-error'+id ).html( result.errors.dob[0] );
                    }
                    if(result.errors.contact){
                        $( '#mobile-error'+id ).html( result.errors.contact[0] );
                    }                    
                }
                if(result.status == true)
                {
                    var data = result.data;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editUser'+id).modal('hide');
                        var url = window.location.href;

                        if (url.indexOf("?") > -1) 
                        {
                            url = url.substring(0, url.indexOf('?'));
                        }
                        window.location.href = url + "?role="+data.role;
                    }, 3000);
                }
                else
                {
                    $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');
                }
                }
            });
        });

        $('#addUserSubmit').click(function(e){
        var formData = new FormData($("#adduserform")[0]);
        $( '#name-error' ).html( "" );
        $( '#email-error' ).html( "" );
        $( '#password-error' ).html( "" );
        $( '#role-error' ).html( "" );
        $( '#status-error' ).html( "" );
        $( '#dob-error' ).html( "" );
        $( '#mobile-error' ).html( "" );
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('adduser') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.name){
                        $( '#name-error' ).html( result.errors.name[0] );
                    }
                    if(result.errors.email){
                        $( '#email-error' ).html( result.errors.email[0] );
                    }
                    if(result.errors.password){
                        $( '#password-error' ).html( result.errors.password[0] );
                    }
                    if(result.errors.role){
                        $( '#role-error' ).html( result.errors.role[0] );
                    }
                    if(result.errors.status){
                        $( '#status-error' ).html( result.errors.status[0] );
                    }
                    if(result.errors.dob){
                        $( '#dob-error' ).html( result.errors.dob[0] );
                    }
                    if(result.errors.contact){
                        $( '#mobile-error' ).html( result.errors.contact[0] );
                    }                    
                }
                if(result.status == true)
                { $(".statusMsg").show();
                    var data = result.data;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#adduserform")[0].reset();
                        $('#addUser').modal('hide');
                        if (window.location.href.indexOf("?") > -1) 
                        {
                            var url = window.location.href;
                            url = url.substring(0, url.indexOf('?'));
                        }
                        window.location.href = url + "?role="+data.role;
                    }, 3000);
                    
                    // var findnorecord = $('#usertableData tr.norecord').length;
                    // if(findnorecord > 0){
                    //     $('#usertableData tr.norecord').remove();
                    //     }
                    
                    // var profilepic = status = '';
                    // var imageurl = "{{asset('public/upload/')}}";

                    // if(data.profile_pic != null)
                    // {
                    //     profilepic = "<img src="+imageurl+"/"+data.profile_pic+">";
                    // }
                    // if(data.status == 0)
                    // {
                    //     status = 'Active';
                    // }
                    // else
                    // {
                    //     status = 'Inactive';
                    // }
                    // if(data.created_at)
                    // {
                    //     var cdate = "<?php //echo date("d F Y",strtotime($value->created_at)) ?>";
                    // }
                    // var deleteurl = '{{ route("user.delete", ":id") }}';
                    // deleteurl = deleteurl.replace(':id', data.id);
                    // var tr_str = "<tr>"+
                    // "<td>"+profilepic+"</td>" +
                    // "<td>"+data.unique_id+"</td>" +
                    // "<td>"+data.name+"</td>" +
                    // "<td>"+data.email+"</td>"+
                    // "<td>"+data.gender+"</td>" +
                    // "<td>"+data.role+"</td>" +
                    // "<td>"+status+"</td>" +
                    // "<td>"+cdate+"</td>" +
                    // "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editUser'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this User?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    // "</tr>";
                    // $("#usertableData tbody").prepend(tr_str);
                    // var gender = '';
                    // if(data.gender == 'male')
                    // {
                    //     gender = 'selected';
                    // }
                    // else if(data.gender == 'female')
                    // {
                    //     gender = 'selected';
                    // }
                    // var role = '';
                    // if(data.role == 'admin')
                    // {
                    //     role = 'selected';
                    // }
                    // else if(data.role == 'brand_merchant')
                    // {
                    //     role = 'selected';
                    // }
                    // else if(data.role == 'property_admin')
                    // {
                    //     role = 'selected';
                    // }
                    // else if(data.role == 'customer')
                    // {
                    //     role = 'selected';
                    // }
                    // var selstatus='';
                    // if(data.status == 0)
                    // {
                    //     selstatus = 'selected';
                    // }
                    // else
                    // {
                    //     selstatus = 'selected';
                    // }

                    // $("#usertableData tbody").append('<div id="editUser'+data.id+'" class="modal fade"><div class="modal-dialog  modal-xl" role="document"><div class="modal-content"><div class="modal-header"><h1 class="modal-title">Edit User</h1></div><div class="modal-body"><p class="statusMsg"></p><form name="adduserform" id="edituserform'+data.id+'" role="form" method="POST" enctype= "multipart/form-data">@csrf<div class="row"><div class="form-group col-md-4"><label for="exampleInputName">Name</label><input type="text" class="form-control" required id="fullname" value="'+data.name+'" name="name" placeholder="Name"><input type="hidden" name="id" value="'+data.id+'"></div><div class="form-group col-md-4"><label for="exampleInputEmail1">Email id</label><input type="email" class="form-control" required id="email" name="email" value="'+data.email+'"  placeholder="Email"></div><div class="form-group col-md-4"><label for="exampleSelectGender">Gender</label><select class="form-control" name="gender" id="exampleSelectGender"><option value="male" '+gender+'>Male</option><option value="female" '+gender+'>Female</option></select></div></div><div class="row"><div class="form-group col-md-4"><label for="exampleInputRole">Role</label><select class="form-control" id="role" name="role"><option value="" selected="">Role</option><option value="admin" '+role+' >Admin</option><option value="brand_merchant" '+role+'>Brand Merchant</option><option value="property_admin" '+role+'>Property Admin</option><option value="customer" '+role+'>Customer</option></select></div><div class="form-group col-md-4"><label for="exampleInputStatus">Status</label><select class="form-control" id="status" name="status"><option value="" selected="">Status</option><option value="0" '+selstatus+'>Active</option><option value="1" '+selstatus+'>Inactive</option></select></div><div class="form-group col-md-4"><label for="exampleSelectPhoto">Photo</label><input type="file" name="profile_pic" class="file-upload-default"><div class="input-group col-xs-12"><input type="text" value="'+profilepic+'" class="form-control file-upload-info" disabled placeholder="Upload Image"><span class="input-group-append"><button class="file-upload-browse btn btn-primary" type="button">Upload</button></span></div></div></div><button type="button" class="btn btn-primary mr-2 editUserSubmit" data-id="'+data.id+'" id="addUserSubmit">Submit</button><button type="button" class="btn btn-light" data-dismiss="modal">Close</button> </form></div></div></div></div>');
                    // $("#adduserform")[0].reset();
                    
                }
                else
                {
                    $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');
                }
                }
            });
        });
    $(document).on('click','#search',function(){ 
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });       
        $.ajax({
                url: "{{route('user.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val()},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    
                    var findnorecord = $('#'+data.role+'tableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#'+data.role+'tableData tr.norecord').remove();
                        }
                    
                    var profilepic = status = '';
                    if(data.profile_pic != null)
                    {
                        profilepic = data.profile_pic;
                    }
                    if(data.status == 0)
                    {
                        status = 'Active';
                    }
                    else
                    {
                        status = 'Inactive';
                    }
                    if(data.created_at)
                    {
                        var cdate = "<?php echo date("d F Y",strtotime($value->created_at)) ?>";
                    }
                    var deleteurl = '{{ route("user.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);
                    var imageurl = "{{asset('public/upload/')}}";
                    var tr_str = "<tr>"+
                    "<td><img src="+imageurl+"/"+profilepic+"></td>" +
                    "<td>"+data.unique_id+"</td>" +
                    "<td>"+data.name+"</td>" +
                    "<td>"+data.email+"</td>"+
                    "<td>"+data.gender+"</td>" +
                    "<td>"+data.role+"</td>" +
                    "<td>"+status+"</td>" +
                    "<td>"+cdate+"</td>" +
                    "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editUser'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this User?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    $("#"+data.role+"tableData tbody").html(tr_str);
                    $(".tabcontent").css('display','none');
                    document.getElementById(data.role).style.display = "block";
                    $(".tablinks").removeClass("active");

                    $("."+data.role).addClass("active");

                    $("#"+data.role+"paging").hide();
                }
                else
                {
                    $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');
                }
                }
            });
    });
});
</script>
@endsection

<!-- Modal HTML Markup -->
<div id="addUser" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add User</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="adduserform" id="adduserform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="exampleInputName">Name</label>
                        <input type="text" required class="form-control" Re id="fullname" name="name" placeholder="Name">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="name-error"></strong>
                        </span>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleInputEmail1">Email id</label>
                        <input type="email" required class="form-control" id="email" name="email" placeholder="Email">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="email-error"></strong>
                        </span>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleInputPassword">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="password-error"></strong>
                        </span>
                    </div>                                
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="exampleSelectGender">Gender</label>
                        <select class="form-control" name="gender" id="exampleSelectGender">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        </select>                        
                    </div>    
                    <div class="form-group col-md-4">
                        <label for="exampleInputRole">Role</label>
                        <select class="form-control" id="role" name="role">
                            <option value="" selected="">Role</option>
                            <option value="admin">Admin</option>
                            <option value="brand_merchant">Brand Merchant</option>
                            <option value="property_admin">Property Admin</option>
                            <option value="customer">Customer</option>
                        </select>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="role-error"></strong>
                        </span>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleInputStatus">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="" selected="">Status</option>
                            <option value="0">Active</option>
                            <option value="1">Inactive</option>
                        </select>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="status-error"></strong>
                        </span>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="exampleSelectPhoto">Photo</label>
                        <input type="file" name="profile_pic" class="file-upload-default">
                        <div class="input-group col-xs-12">
                            <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                            <span class="input-group-append">
                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleInputEmail1">Date of Birth</label>
                        <input type="date" required class="form-control" id="dob" name="dob" placeholder="dob">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="dob-error"></strong>
                        </span>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleInputPassword">Contact</label>
                        <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="mobile-error"></strong>
                        </span>
                    </div>               
                </div>
                <button type="button" class="btn btn-primary mr-2" id="addUserSubmit">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>  
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->