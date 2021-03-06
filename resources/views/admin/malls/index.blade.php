
@extends('layouts.app')

@section('content')
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
                    <a id="clear16" class="btn btn-secondary" href="{{route('usermanagement')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addShopsandMalls" tabindex="">ADD NEW</a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="export14" class="btn btn-secondary" onclick="fnExcelReport()"  tabindex="">EXPORT</a>
                </div>             
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
                        <h4 class="mess" style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 class="mess" style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="mallstableData">
                      <thead>
                        <tr>
                            <th>@sortablelink('id')</th>
                            <th>Image</th>
                            <th>@sortablelink('name')</th>
                            <th>@sortablelink('location')</th>
                            <th>@sortablelink('openinghrs','Opening Hours')</th>
                            <th>@sortablelink('contact','Contact Info')</th>
                            <th>@sortablelink('type','Mall Type')</th>
                            <th>@sortablelink('','Mall Admin')</th>
                            <th>@sortablelink('','Set as banner')</th>
                            <th>@sortablelink('created_at','Created On')</th>
                            <th>@sortablelink('','Created By')</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                          <td>{{$value->unique_id}}</td>
                          <td><img src="{{asset('public/upload/malls/')}}/{{$value->image}}" alt=""></td>
                          <td>{{$value->name}}</td>
                          <td>{{$value->location}}</td>
                          <td>{{$value->openinghrs}}</td>
                          <td>{{$value->contact}}</td>
                          <td>{{$value->type}}</td>
                          <td>{{$value->propertyadmin}}</td>
                          <td>@if($value->set_as_banner == 1)
                                <input type="checkbox" name="banner" class="bannerclass" checked id="banner" data-id="{{$value->id}}" value="{{$value->set_as_banner}}">
                            @else
                                <input type="checkbox" name="banner"  class="bannerclass" id="banner" data-id="{{$value->id}}" value="{{$value->set_as_banner}}">
                            @endif
                            </td>
                          <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                          <td>{{$value->created_by}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editShopsandmalls{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Mall?')" href="{{route('shopsmalls.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editShopsandmalls{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit {{$title}}</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="editShopsmalls" id="editShopsmallsform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Photo</label>
                                                    <input type="file" name="image" class="file-upload-default">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" value="{{$value->image}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                        <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                        </span>
                                                    </div>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="image-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Name</label>
                                                    <input type="text" required class="form-control" Re id="fullname" value="{{$value->name}}" name="name" placeholder="Name">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="name-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputEmail1">Location</label>
                                                    <input type="email" required class="form-control" id="location{{$value->id}}" value="{{$value->location}}" name="location" placeholder="location">
                                                    <input type="hidden" name="latitude" id="lat{{$value->id}}" value="{{$value->latitude}}">
                                                    <input type="hidden" name="longitude" id="long{{$value->id}}" value="{{$value->longitude}}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputPassword">Opening Hours</label>
                                                    <input type="text" class="form-control timepicker" value="{{$value->openinghrs}}"  id="openinghrs" name="openinghrs" placeholder="Opening Houres">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="openinghrs-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectGender">Closing Hours</label>
                                                    <input type="text" class="form-control timepicker" value="{{$value->closinghrs}}" id="closinghrs" name="closinghrs" placeholder="Closing Hours">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="closinghrs-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>                 
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectGender">Contact Info</label>
                                                    <input type="text" class="form-control" id="contact" value="{{$value->contact}}" name="contact" placeholder="Contact">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="contact-error{{$value->id}}"></strong>
                                                    </span>
                                                </div> 
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputRole">Property Admin</label>
                                                    <select class="form-control" id="property_admin" name="property_admin">
                                                    <option value="">--Select--</option>
                                                    @if(!empty($property_admin) && $property_admin->count() > 0)
                                                        @foreach($property_admin as $key => $pd)
                                                            <option value="{{$pd->id}}" {{ $value->property_admin_user_id == $pd->id ? 'selected' : ''}} >{{$pd->name}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="property_admin-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Filter</label>
                                                    <select class="form-control category_id" multiple name="filter[]" id="filter">
                                                    <!-- <option value="">--Select--</option> -->
                                                    @if(!empty($category) && $category->count() > 0)
                                                        @foreach($category as $key => $cat)
                                                        <option value="{{$cat->id}}"  {{ in_array($cat->id,explode(",",$value->category_id))  ? 'selected' : ''}}>{{$cat->category_name}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="category-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Area</label>
                                                    <select class="form-control " id="area" name="area">
                                                    <option value="">--Select--</option>
                                                    @if(!empty($area) && $area->count() > 0)
                                                        @foreach($area as $key => $avalue)
                                                            <option value="{{$avalue->id}}" {{ $value->area_id == $avalue->id ? 'selected' : ''}}>{{$avalue->area_name}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="area-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputRole">Featured Mall</label>
                                                    <select class="form-control " id="featured_mall" name="featured_mall">
                                                        <option value="">--Select--</option>
                                                        <option value="yes" {{ $value->featured_mall == 'yes' ? 'selected' : ''}}>Yes</option>
                                                        <option value="no" {{ $value->featured_mall == 'no' ? 'selected' : ''}}>No</option>
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="featured_mall-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Layer</label>
                                                    <select class="form-control" id="layer" name="layer">
                                                        <option value="">--Select--</option>
                                                        <option value="mall" {{ $value->type == 'mall' ? 'selected' : ''}}>Mall</option>
                                                        <option value="shop" {{ $value->type == 'shop' ? 'selected' : ''}}>Shop</option>
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="type-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <textarea class="form-control ckeditor" id="description{{$value->id}}" name="desc">{{$value->description}}</textarea>
                                                </div>
                                            </div>
                                        <button type="button" class="btn btn-primary mr-2 editMallsSubmit" data-id="{{$value->id}}" id="editAreaSubmit">Submit</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- edit /.modal -->
                        @endforeach
                        @else
                        <tr>
                        <td colspan="11">No Records Found</td>
                        </tr>
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
<!-- //view-source:https://maps.googleapis.com/maps/api/js?key=AIzaSyBrZ7Gj6VQ4ReRytE4tQm0RFOFCQiMFl8U&libraries=places,geometry&callback=loadGoogleMap -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBrZ7Gj6VQ4ReRytE4tQm0RFOFCQiMFl8U&libraries=places,geometry"></script>
<style>
    .pac-container {
        z-index: 10000 !important;
    }
</style>  
<script>
  google.maps.event.addDomListener(window, 'load', initialize);
    function initialize() {
      var input = document.getElementById('location');
      var autocomplete = new google.maps.places.Autocomplete(input);
      autocomplete.addListener('place_changed', function () {
      var place = autocomplete.getPlace();
      // place variable will have all the information you are looking for.
      $('#lat').val(place.geometry['location'].lat());
      $('#long').val(place.geometry['location'].lng());
      $('#my-modal').modal('show');
    });
  }
</script>

<script>
   

$(document).ready(function(){
    
     $(".bannerclass").click(function(){
        var chk;
        var id = $(this).data('id');
        if($(this).is(':checked'))
        {
            chk = 1;   
        }
        else
        {
            chk = 0;
        }
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
        $.ajax({
            url: "{{ route('shopandmall.updatebanner') }}",
            method: 'post',
            data: {'chk':chk,'id':id},
            success: function(result){
                if(result.status == true)
                {
                    $('.box-header').html('<h4 class="mess" style="text-align: center; color: green;">'+result.msg+'</h4>');
                }
                else
                {
                    $('.box-header').html('<h4 class="mess" style="text-align: center; color: green;">'+result.msg+'</h4>');
                }
            }
        });
    });

    $(".edit").click(function(){
        var sid = $(this).data('id');
  
        var input = document.getElementById('location'+sid);
            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            // place variable will have all the information you are looking for.
              $('#lat'+sid).val(place.geometry['location'].lat());
              $('#long'+sid).val(place.geometry['location'].lng());
            $('#my-modal').modal('show');
        });      
    });

    setTimeout(function(){
           $("h4.mess").remove();
        }, 5000 ); // 5 secs

        $('.category_id').multiselect({
            columns: 1,
            placeholder: 'Select Category'
        });

        //CKEDITOR.replace( 'description' );
        $('.timepicker').datetimepicker({
            format: 'HH:mm:ss'
        }); 

    $(document).on('click','.editMallsSubmit',function(e){
       
        var id = $(this).data('id');
        var formData = new FormData($("#editShopsmallsform"+id)[0]);

        var message = CKEDITOR.instances['description'+id].getData();

        formData.append('description',message);
        $( '#image-error' ).html( "" );
            $( '#name-error'+id ).html( "" );
            $( '#openinghrs-error'+id ).html( "" );
            $( '#closinghrs-error'+id ).html( "" );
            $( '#contact-error'+id ).html( "" );
            $( '#propertyadmin-error'+id ).html( "" );
            $( '#category-error'+id ).html( "" );
            $( '#area-error'+id ).html( "" );
            $("#featured_mall-error"+id).html("");
            $( '#type-error'+id ).html("");

        var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('shopsmalls.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.area){
                        $( '#area-error'+id ).html( result.errors.area[0] );
                    }
                    if(result.errors.filter){
                        $( '#category-error' ).html( result.errors.filter[0] );
                    }
                    if(result.errors.closinghrs){
                        $( '#closinghrs-error'+id ).html( result.errors.closinghrs[0] );
                    }
                    if(result.errors.contact){
                        $( '#contact-error'+id ).html( result.errors.contact[0] );
                    }
                    if(result.errors.featured_mall){
                        $( '#featured_mall-error'+id ).html( result.errors.featured_mall[0] );
                    }
                    if(result.errors.image){
                        $( '#image-error'+id ).html( result.errors.image[0] );
                    }
                    if(result.errors.name){
                        $( '#name-error'+id ).html( result.errors.name[0] );
                    }  
                    if(result.errors.openinghrs){
                        $( '#openinghrs-error'+id ).html( result.errors.openinghrs[0] );
                    }   
                    if(result.errors.property_admin){
                        $( '#propertyadmin-error'+id ).html( result.errors.property_admin[0] );
                    }     
                    if(result.errors.layer){
                        $( '#type-error'+id ).html( result.errors.layer[0] );
                    }                
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editShopsandmalls'+id).modal('hide');
                        window.location.reload();
                    }, 3000);
                }
                else
                {
                    $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');
                }
                }
            });
        });

        $('#addMallsSubmit').click(function(e){
            var formData = new FormData($("#addMallsform")[0]);
            var message = CKEDITOR.instances['description'].getData();
            formData.append('description',message);
            $( '#image-error' ).html( "" );
            $( '#name-error' ).html( "" );
            $( '#openinghrs-error' ).html( "" );
            $( '#closinghrs-error' ).html( "" );
            $( '#contact-error' ).html( "" );
            $( '#propertyadmin-error' ).html( "" );
            $( '#category-error' ).html( "" );
            $( '#area-error' ).html( "" );
            $("#featured_mall-error").html("");
            $( '#type-error' ).html("");
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addShopsandMalls') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                    if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.area){
                        $( '#area-error' ).html( result.errors.area[0] );
                    }
                    if(result.errors.filter){
                        $( '#category-error' ).html( result.errors.filter[0] );
                    }
                    if(result.errors.closinghrs){
                        $( '#closinghrs-error' ).html( result.errors.closinghrs[0] );
                    }
                    if(result.errors.contact){
                        $( '#contact-error' ).html( result.errors.contact[0] );
                    }
                    if(result.errors.featured_mall){
                        $( '#featured_mall-error' ).html( result.errors.featured_mall[0] );
                    }
                    if(result.errors.image){
                        $( '#image-error' ).html( result.errors.image[0] );
                    }
                    if(result.errors.name){
                        $( '#name-error' ).html( result.errors.name[0] );
                    }  
                    if(result.errors.openinghrs){
                        $( '#openinghrs-error' ).html( result.errors.openinghrs[0] );
                    }   
                    if(result.errors.property_admin){
                        $( '#propertyadmin-error' ).html( result.errors.property_admin[0] );
                    } 
                    if(result.errors.layer){
                        $( '#type-error' ).html( result.errors.layer[0] );
                    }                   
                }
                if(result.status == true)
                {
                    var data = result.data.malls;
                    var propertyadmin =  result.data.propertyadmin;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addMallsform")[0].reset();
                        $('#addShopsandMalls').modal('hide');
                        window.location.reload();
                    }, 3000);
                    
                    var findnorecord = $('#mallstableData tr.norecord').length;
                    if(findnorecord > 0)
                    {
                        $('#mallstableData tr.norecord').remove();
                    }

                    var image = '';
                    if(data.image != null)
                    {
                        image = data.image;
                    }
                    if(data.created_at)
                    {
                        var cdate = date(data.created_at);
                    }
                    var deleteurl = '{{ route("shopsmalls.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);
                    var imageurl = "{{asset('public/upload/malls')}}";
                    var tr_str = "<tr>"+
                    "<td>"+data.unique_id+"</td>" +
                    "<td><img src="+imageurl+"/"+image+"></td>" +
                    "<td>"+data.name+"</td>" +
                    "<td>"+data.location+"</td>"+
                    "<td>"+data.openinghrs+"</td>" +
                    "<td>"+data.contact+"</td>" +
                    "<td>"+data.type+"</td>" +
                    "<td>"+propertyadmin.propertyadmin+"</td>" +
                    "<td>"+cdate+"</td>" +
                    "<td>"+data.created_by+"</td>" +
                    "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editShopsandmalls'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this Mall?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    $("#mallstableData tbody").prepend(tr_str);
                    var propertyadminarray = '<?php echo $property_admin ?>';                 
                    var categoryarray = '<?php echo $category ?>';                 
                    var areaarray = '<?php echo $area ?>';                 
                    var propertyadmin = category = area ='';

                    var featured_mall = '';
                    if(data.featured_mall == 'yes')
                    {
                        featured_mall = 'selected';
                    }
                    else if(data.featured_mall == 'no')
                    {
                        featured_mall = 'selected';
                    }

                    var type = '';
                    if(data.type == 'mall')
                    {
                        type = 'selected';
                    }
                    else if(data.type == 'shop')
                    {
                        type = 'selected';
                    }

                    var edithtml = '<div id="editShopsandmalls'+data.id+'" class="modal fade"><div class="modal-dialog  modal-xl" role="document"><div class="modal-content"><div class="modal-header"><h1 class="modal-title">Edit Malls</h1></div><div class="modal-body"><p class="statusMsg"></p><form name="editShopsmalls" id="editShopsmallsform'+data.id+'" role="form" method="POST" enctype= "multipart/form-data">@csrf<div class="row"><div class="form-group col-md-4"><label for="exampleSelectPhoto">Photo</label><input type="file" name="image" class="file-upload-default"><div class="input-group col-xs-12"><input type="text" value="'+data.image+'" class="form-control file-upload-info" disabled placeholder="Upload Image"><span class="input-group-append"><button class="file-upload-browse btn btn-primary" type="button">Upload</button></span></div></div><div class="form-group col-md-4"><label for="exampleInputName">Name</label><input type="text" required class="form-control" Re id="fullname" value="'+data.name+'" name="name" placeholder="Name"><input type="hidden" name="id" value="'+data.id+'"></div><div class="form-group col-md-4"><label for="exampleInputEmail1">Location</label><input type="email" required class="form-control" id="location" value="'+data.location+'" name="location" placeholder="location"><input id="autocomplete_search" name="autocomplete_search" type="text" class="form-control" placeholder="Search" /><input type="hidden" name="lat"><input type="hidden" name="long"></div></div><div class="row"><div class="form-group col-md-4"><label for="exampleInputPassword">Opening Hours</label><input type="text" class="form-control timepicker" value="'+data.openinghrs+'"  id="openinghrs" name="openinghrs" placeholder="Opening Houres"></div><div class="form-group col-md-4"><label for="exampleSelectGender">Closing Hours</label><input type="text" class="form-control timepicker" value="'+data.closinghrs+'" id="closinghrs" name="closinghrs" placeholder="Closing Hours"></div><div class="form-group col-md-4"><label for="exampleSelectGender">Contact Info</label><input type="text" class="form-control" id="contact" value="'+data.contact+'" name="contact" placeholder="Contact"></div></div><div class="row"><div class="form-group col-md-4"><label for="exampleInputRole">Property Admin</label><select class="form-control" id="property_admin_user_id" name="property_admin_user_id"><option value="">--Select--</option>';

                    $.each(JSON.parse(propertyadminarray), function (i, elem) { 
                    if(elem.id == data.property_admin_user_id)
                    {
                        propertyadmin = 'selected';
                    }
                    edithtml +='<option value="'+elem.id+'" '+propertyadmin+' >'+elem.name+'</option>';
                    });

                    edithtml +='</select></div>';
                    edithtml += '<div class="form-group col-md-4"><label for="exampleInputStatus">Category</label><select class="form-control category_id" multiple name="category_id[]" id="category_id">';

                    var categoryidarray = data.category_id.split(',');
                    
                    $.each(JSON.parse(categoryarray), function (i, elem) {
                        if(categoryidarray.indexOf(elem.id) != "-1")
                        {
                            console.log(1);
                            console.log(elem.id);
                            category = 'selected';
                        }
                    edithtml +='<option value="'+elem.id+'" '+category+'>'+elem.category_name+'</option>';
                    });

                    edithtml += '</select></div><div class="form-group col-md-4"><label for="exampleSelectPhoto">Area</label><select class="form-control " id="area_id" name="area_id"><option value="">--Select--</option>';

                    $('.category_id').multiselect({
                        columns: 1,
                        placeholder: 'Select Category'
                    });
                                        
                    $.each(JSON.parse(areaarray), function (i, elem) { 
                    if(elem.id == data.area_id)
                    {
                        area = 'selected';
                    }
                    edithtml +='<option value="'+elem.id+'" '+area+' >'+elem.area_name+'</option>';
                    });

                    edithtml +='</select></div></div><div class="row"><div class="form-group col-md-4"><label for="exampleInputRole">Featured Mall</label><select class="form-control " id="featured_mall" name="featured_mall"><option value="">--Select--</option><option value="yes" '+featured_mall+'>Yes</option><option value="no" '+featured_mall+'>No</option></select></div><div class="form-group col-md-4"><label for="exampleInputStatus">Type</label><select class="form-control" id="type" name="type"><option value="">--Select--</option><option value="mall" '+type+'>Mall</option><option value="shop" '+type+'>Shop</option></select></div></div><div class="row"><div class="form-group col-md-12"><textarea class="form-control ckeditor" id="description1" name="description">{{$value->description}}</textarea></div></div><button type="button" class="btn btn-primary mr-2 editMallsSubmit" data-id="'+data.id+'" id="editAreaSubmit">Submit</button><button type="button" class="btn btn-light" data-dismiss="modal">Close</button></form></div></div></div></div>';

                    //$("#mallstableData tbody").append(edithtml);
                    $("#addMallsform")[0].reset();
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
                url: "{{route('shopsmalls.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val()},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    
                    var findnorecord = $('#mallstableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#mallstableData tr.norecord').remove();
                        }
                    $("#mallstableData tbody").html(data);
                    $("#paging").hide();
                }
                else
                {
                    $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');
                }
                }
            });
    }); 
});
function fnExcelReport()
{
   var search = "";
    if($("#searchtext").val() != null || $("#searchtext").val() != "")
    {
        search = $("#searchtext").val();
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    }); 
    $.ajax({
        url: "{{route('shopmallexport')}}",
        method: 'get',
        data: {'search':search},
        success: function(result){
            $(result).table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "shopmall",
                filename: "shopmall" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls", //do not include extension
                fileext: ".xls" // file extension
              }); 
        }
    });
}
</script>
@endsection

<!-- Modal HTML Markup -->
<div id="addShopsandMalls" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add {{$title}}</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addMallsform" id="addMallsform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleSelectPhoto">Photo</label>
                            <input type="file" name="image" id="image" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="image-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Name</label>
                            <input type="text" required class="form-control"  id="name" name="name" placeholder="Name">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="name-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputEmail1">Location</label>
                            <input type="email" required class="form-control location" id="location" name="location" placeholder="location">
                            <input type="hidden" name="latitude" id="lat">
                            <input type="hidden" name="longitude" id="long">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputPassword">Opening Hours</label>
                            <input type="text" class="form-control timepicker" id="openinghrs" name="openinghrs" placeholder="Opening Houres">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="openinghrs-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleSelectGender">Closing Hours</label>
                            <input type="text" class="form-control timepicker" id="closinghrs" name="closinghrs" placeholder="Closing Hours">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="closinghrs-error"></strong>
                            </span>
                        </div>                 
                        <div class="form-group col-md-4">
                            <label for="exampleSelectGender">Contact Info</label>
                            <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="contact-error"></strong>
                            </span>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputRole">Property Admin</label>
                            <select class="form-control" id="property_admin" name="property_admin">
                            <option value="">--Select--</option>
                            @if(!empty($property_admin) && $property_admin->count() > 0)
                                @foreach($property_admin as $key => $pd)
                                    <option value="{{$pd->id}}">{{$pd->name}}</option>
                                @endforeach
                            @endif
                            </select>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="propertyadmin-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputStatus">Filter</label>
                            <select class="form-control category_id" multiple name="filter[]" id="filter">
                            <!-- <option value="">--Select--</option> -->
                            @if(!empty($category) && $category->count() > 0)
                                @foreach($category as $key => $cat)
                                <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                                @endforeach
                            @endif
                            </select>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="category-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleSelectPhoto">Area</label>
                            <select class="form-control" id="area" name="area">
                            <option value="">--Select--</option>
                            @if(!empty($area) && $area->count() > 0)
                                @foreach($area as $key => $avalue)
                                    <option value="{{$avalue->id}}">{{$avalue->area_name}}</option>
                                @endforeach
                            @endif
                            </select>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="area-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputRole">Featured Mall</label>
                            <select class="form-control" id="featured_mall" name="featured_mall">
                                <option value="">--Select--</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="featured_mall-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputStatus">Layer</label>
                            <select class="form-control" id="layer" name="layer">
                                <option value="">--Select--</option>
                                <option value="mall">Mall</option>
                                <option value="shop">Shop</option>
                            </select>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="type-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <textarea class="form-control ckeditor" id="description" name="desc"></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addMallsSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->