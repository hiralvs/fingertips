@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-sm-6 mb-4 mb-xl-0">
			<div class="d-lg-flex align-items-center">
            <h4 class="card-title" style="float:left">{{$title}}</h4>
            </div>
        </div>

    </div>
    <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title" style="float:left">{{$title}}</h4>
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
                                <img src="{{asset('public/upload/')}}{{'/'.$value->bannerimage}}" alt="">
                            @else

                            @endif
                            </td>
                            <td><a class="edit open_modal" data-toggle="modal" data-target="#editBanner{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Banner?')" href="{{route('user.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editBanner{{$value->id}}" class="modal fade">
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
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="exampleInputEmail1">Email id</label>
                                                <input type="email" class="form-control" required id="email" name="email" value="{{$value->email}}"  placeholder="Email">
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
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="exampleInputStatus">Status</label>
                                                <select class="form-control" id="status" name="status">
                                                    <option value="" selected="">Status</option>
                                                    <option value="0" {{ $value->status == '0' ? 'selected' : ''}}>Active</option>
                                                    <option value="1" {{ $value->status == '1' ? 'selected' : ''}}>Inactive</option>
                                                </select>
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
                                                <!-- <input type="file" class="form-control" id="photo" name="profile_pic" placeholder="Photo"> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-2" style="text-align:center">
                                            <button type="button" class="btn btn-primary mr-2 editUserSubmit" data-id="{{$value->id}}" id="addUserSubmit">Submit</button>
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
                    <div class="form-group col-md-3">
                        <label for="exampleInputName">Name</label>
                        <input type="text" required class="form-control" Re id="fullname" name="name" placeholder="Name">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleInputEmail1">Email id</label>
                        <input type="email" required class="form-control" id="email" name="email" placeholder="Email">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleInputPassword">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleSelectGender">Gender</label>
                        <select class="form-control" name="gender" id="exampleSelectGender">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        </select>
                    </div>
                 
                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="exampleInputRole">Role</label>
                        <select class="form-control" id="role" name="role">
                            <option value="" selected="">Role</option>
                            <option value="admin">Admin</option>
                            <option value="brand_merchant">Brand Merchant</option>
                            <option value="property_admin">Property Admin</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleInputStatus">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="" selected="">Status</option>
                            <option value="0">Active</option>
                            <option value="1">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleSelectPhoto">Photo</label>
                        <input type="file" class="form-control" id="photo" name="profile_pic" placeholder="Photo">
                        </div>
                    </div>
                </div>
                <!-- <div class="row">                  
                    <div class="form-group col-md-3">
                        <label for="exampleSelectPhoto">Password</label>
                        <input type="password" class="form-control" id="password" name="phopasswordto" placeholder="password">
                        </div>
                    </div>
                </div> -->
                <div class="row">
                    <div class="form-group col-md-2" style="text-align:center">
                    <button type="button" class="btn btn-primary mr-2" id="addUserSubmit">Submit</button>
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