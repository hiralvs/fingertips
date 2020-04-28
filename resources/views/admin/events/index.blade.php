
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
                    <a id="clear16" class="btn btn-secondary" href="{{route('event')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addEvents" tabindex="">ADD NEW</a>
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
                    <table class="table table-hover" id="eventstableData">
                      <thead>
                        <tr>
                            <th>@sortablelink('id')</th>
                            <th>Image</th>
                            <th>@sortablelink('Name')</th>
                            <th>@sortablelink('location')</th>
                            <th>@sortablelink('event_start_date','Opening Date')</th>
                            <th>@sortablelink('start_time','Starting Time')</th>
                            <th>@sortablelink('description','Description')</th>
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
                          <td><img src="{{asset('public/upload/events/')}}/{{$value->event_image}}" alt=""></td>
                          <td>{{$value->event_name}}</td>
                          <td>{{$value->location}}</td>
                          <td>{{$value->event_start_date}}</td>
                          <td>{{$value->start_time}}</td>
                          <td>{{$value->description}}</td>
                          <td>@if($value->set_as_banner == 1)
                                <input type="checkbox" name="banner" class="bannerclass" checked id="banner" data-id="{{$value->id}}" value="{{$value->set_as_banner}}">
                            @else
                                <input type="checkbox" name="banner"  class="bannerclass" id="banner" data-id="{{$value->id}}" value="{{$value->set_as_banner}}">
                            @endif
                            </td>
                          <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                          <td>{{$value->created_by}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editEvents{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Events?')" href="{{route('events.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editEvents{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Events</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="editShopsmalls" id="editEventsform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Photo</label>
                                                    <input type="file" name="event_image" class="file-upload-default">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" value="{{$value->event_image}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                        <span class="input-group-append">
                                                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Name</label>
                                                    <input type="text" required class="form-control event_name" Re id="fullname" value="{{$value->event_name}}" name="event_name" placeholder="Name">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="event_name-error"></strong>
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
                                                    <label for="exampleInputPassword">Event Start Date</label>
                                                    <input type="date" class="form-control event_start_date" value="{{$value->event_start_date}}"  id="openingdate" name="event_start_date" placeholder="Opening Date">
                                                    <span class="text-danger">
                                                        <strong class="event_start_date-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputPassword">Opening Hours</label>
                                                    <input type="text" class="form-control start_time" value="{{$value->start_time}}"  id="openinghrs" name="start_time" placeholder="Opening Houres">
                                                    <span class="text-danger">
                                                        <strong class="start_time-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectGender">Event End Date</label>
                                                    <input type="date" class="form-control event_end_date" value="{{$value->event_end_date}}" id="closinghrs" name="event_end_date" placeholder="Closing Date">
                                                    <span class="text-danger">
                                                        <strong class="event_end_date-error"></strong>
                                                    </span>
                                                </div>                 
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectGender">Closing Hours</label>
                                                    <input type="text" class="form-control end_time" value="{{$value->end_time}}" id="closinghrs" name="end_time" placeholder="Closing Hours">
                                                    <span class="text-danger">
                                                        <strong class="end_time-error"></strong>
                                                    </span>
                                                </div>                 
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectGender">Contact Info</label>
                                                    <input type="text" class="form-control contact" id="contact" value="{{$value->contact}}" name="contact" placeholder="Contact">
                                                    <span class="text-danger">
                                                        <strong class="contact-error"></strong>
                                                    </span>
                                                </div> 
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputRole">Property Admin</label>
                                                    <select class="form-control property_admin_user_id" id="property_admin_user_id" name="property_admin_user_id">
                                                    <option value="">--Select--</option>
                                                    @if(!empty($property_admin) && $property_admin->count() > 0)
                                                        @foreach($property_admin as $key => $pd)
                                                            <option value="{{$pd->id}}" {{ $value->property_admin_user_id == $pd->id ? 'selected' : ''}} >{{$pd->name}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
                                                    <span class="text-danger">
                                                        <strong class="property_admin_user_id-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Category</label>
                                                    <select class="form-control category_id" multiple name="category_id[]" id="category_id">
                                                    <!-- <option value="">--Select--</option> -->
                                                    @if(!empty($category) && $category->count() > 0)
                                                        @foreach($category as $key => $cat)
                                                        <option value="{{$cat->id}}"  {{ in_array($cat->id,explode(",",$value->category_id))  ? 'selected' : ''}}>{{$cat->category_name}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
                                                    <span class="text-danger">
                                                        <strong class="category_id-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Area</label>
                                                    <select class="form-control area_id " id="area_id" name="area_id">
                                                    <option value="">--Select--</option>
                                                    @if(!empty($area) && $area->count() > 0)
                                                        @foreach($area as $key => $avalue)
                                                            <option value="{{$avalue->id}}" {{ $value->area_id == $avalue->id ? 'selected' : ''}}>{{$avalue->area_name}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
                                                    <span class="text-danger">
                                                        <strong class="area_id-error"></strong>
                                                    </span>
                                                </div>                                                
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputRole">Featured Events</label>
                                                    <select class="form-control featured_event" id="featured_event" name="featured_event">
                                                        <option value="">--Select--</option>
                                                        <option value="yes" {{ $value->featured_event == 'yes' ? 'selected' : ''}}>Yes</option>
                                                        <option value="no" {{ $value->featured_event == 'no' ? 'selected' : ''}}>No</option>
                                                    </select>
                                                    <span class="text-danger">
                                                        <strong class="featured_event-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <textarea class="form-control ckeditor" id="description{{$value->id}}" name="desc">{{$value->description}}</textarea>
                                                </div>
                                            </div>
                                        <button type="button" class="btn btn-primary mr-2 editEventsSubmit" data-id="{{$value->id}}" id="editAreaSubmit">Submit</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- edit /.modal -->
                        @endforeach
                        @else
                        <tr>
                        <td colspan="3">No Records Found</td>
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
        $('#lat').val(place.geometry['location'].lat());
      $('#long').val(place.geometry['location'].lng());
      $('#my-modal').modal('show');
    });
  }
</script>
<script>

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
            url: "{{ route('events.updatebanner') }}",
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
        
    $(document).on('click','.editEventsSubmit',function(e){
       
        var id = $(this).data('id');
        var formData = new FormData($("#editEventsform"+id)[0]);

        $( '.event_name-error' ).html( "" );
        $( '.event_start_date-error' ).html( "" );
        $( '.start_time-error' ).html( "" );
        $( '.event_end_date-error' ).html( "" );
        $( '.end_time-error' ).html( "" );
        $( '.contact-error' ).html( "" );
        $( '.property_admin_user_id-error' ).html( "" );
        $( '.category_id-error' ).html( "" );
        $( '.area_id-error' ).html( "" );
        $( '.featured_event-error' ).html( "" );
        
        var message = CKEDITOR.instances['description'+id].getData();

        formData.append('description',message);
        var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('events.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                    if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.event_name){
                        $( '.event_name-error' ).html( result.errors.event_name[0] );
                    }
                    if(result.errors.event_start_date){
                        $( '.event_start_date-error' ).html( result.errors.event_start_date[0] );
                    }
                    if(result.errors.start_time){
                        $( '.start_time-error' ).html( result.errors.start_time[0] );
                    }
                    if(result.errors.event_end_date){
                        $( '.event_end_date-error' ).html( result.errors.event_end_date[0] );
                    }
                    if(result.errors.end_time){
                        $( '.end_time-error' ).html( result.errors.end_time[0] );
                    }
                    if(result.errors.contact){
                        $( '.contact-error' ).html( result.errors.contact[0] );
                    }
                    if(result.errors.property_admin_user_id){
                        $( '.property_admin_user_id-error' ).html( result.errors.property_admin_user_id[0] );
                    }
                    if(result.errors.category_id){
                        $( '.category_id-error' ).html( result.errors.category_id[0] );
                    }
                    if(result.errors.area_id){
                        $( '.area_id-error' ).html( result.errors.area_id[0] );
                    }
                    if(result.errors.featured_event){
                        $( '.featured_event-error' ).html( result.errors.featured_event[0] );
                    }                    
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editEvents'+id).modal('hide');
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
    $('#addEventsSubmit').click(function(e){
            var formData = new FormData($("#addEventsform")[0]);
            var message = CKEDITOR.instances['description'].getData();
            $( '#event_image-error' ).html( "" );
            $( '#event_name-error' ).html( "" );
            $( '#event_start_date-error' ).html( "" );
            $( '#start_time-error' ).html( "" );
            $( '#event_end_date-error' ).html( "" );
            $( '#end_time-error' ).html( "" );
            $( '#contact-error' ).html( "" );
            $( '#property_admin_user_id-error' ).html( "" );
            $( '#category_id-error' ).html( "" );
            $( '#area_id-error' ).html( "" );
            $( '#featured_event-error' ).html( "" );

            formData.append('description',message);
                
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addEvents') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.event_image){
                        $( '#event_image-error' ).html( result.errors.event_image[0] );
                    }
                    if(result.errors.event_name){
                        $( '#event_name-error' ).html( result.errors.event_name[0] );
                    }
                    if(result.errors.event_start_date){
                        $( '#event_start_date-error' ).html( result.errors.event_start_date[0] );
                    }
                    if(result.errors.start_time){
                        $( '#start_time-error' ).html( result.errors.start_time[0] );
                    }
                    if(result.errors.event_end_date){
                        $( '#event_end_date-error' ).html( result.errors.event_end_date[0] );
                    }
                    if(result.errors.end_time){
                        $( '#end_time-error' ).html( result.errors.end_time[0] );
                    }
                    if(result.errors.contact){
                        $( '#contact-error' ).html( result.errors.contact[0] );
                    }
                    if(result.errors.property_admin_user_id){
                        $( '#property_admin_user_id-error' ).html( result.errors.property_admin_user_id[0] );
                    }
                    if(result.errors.category_id){
                        $( '#category_id-error' ).html( result.errors.category_id[0] );
                    }
                    if(result.errors.area_id){
                        $( '#area_id-error' ).html( result.errors.area_id[0] );
                    }
                    if(result.errors.featured_event){
                        $( '#featured_event-error' ).html( result.errors.featured_event[0] );
                    }                    
                }
                if(result.status == true)
                {
                    var data = result.data.events;
                    var propertyadmin =  result.data.propertyadmin;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addEventsform")[0].reset();
                        $('#addEvents').modal('hide');
                        window.location.reload();
                    }, 3000);
                    
                    $("#addEventsform")[0].reset();
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
                url: "{{route('events.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val()},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;                    
                    
                    var findnorecord = $('#eventstableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#eventstableData tr.norecord').remove();
                        }
                    
                    $("#eventstableData tbody").html(data);
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
        url: "{{route('eventexport')}}",
        method: 'get',
        data: {'search':search},
        success: function(result){
            $(result).table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "events",
                filename: "events" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls", //do not include extension
                fileext: ".xls" // file extension
              }); 
        }
    });
}
</script>
</script>
@endsection

<!-- Modal HTML Markup -->
<div id="addEvents" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Events</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addEventsform" id="addEventsform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleSelectPhoto">Photo</label>
                            <input type="file" name="event_image" id="event_image" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                                <span class="text-danger">
                                    <strong id="event_image-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Name</label>
                            <input type="text" required class="form-control"  id="event_name" name="event_name" placeholder="Name">
                            <span class="text-danger">
                                <strong id="event_name-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputEmail1">Location</label>
                            <input type="email" required class="form-control" id="location" name="location" placeholder="location">
                            <span class="text-danger">
                                <strong id="location-error"></strong>
                            </span>       
                            <input type="hidden" name="latitude" id="lat">
                            <input type="hidden" name="longitude" id="long">                    
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputPassword">Event Start Date</label>
                            <input type="date" class="form-control datepicker" id="event_start_date" name="event_start_date" placeholder="Opening Date">
                            <span class="text-danger">
                                <strong id="event_start_date-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputPassword">Opening Hours</label>
                            <input type="text" class="form-control timepicker" id="start_time" name="start_time" placeholder="Opening Houres">
                            <span class="text-danger">
                                <strong id="start_time-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputPassword">Event End Date</label>
                            <input type="date" class="form-control datepicker" id="event_end_date" name="event_end_date" placeholder="Closing Date">
                            <span class="text-danger">
                                <strong id="event_end_date-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleSelectGender">Closing Hours</label>
                            <input type="text" class="form-control timepicker" id="end_time" name="end_time" placeholder="Closing Hours">
                            <span class="text-danger">
                                <strong id="end_time-error"></strong>
                            </span>
                        </div>                 
                        <div class="form-group col-md-4">
                            <label for="exampleSelectGender">Contact Info</label>
                            <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact">
                            <span class="text-danger">
                                <strong id="contact-error"></strong>
                            </span>
                        </div> 
                        <div class="form-group col-md-4">
                            <label for="exampleInputRole">Property Admin</label>
                            <select class="form-control" id="property_admin_user_id" name="property_admin_user_id">
                            <option value="">--Select--</option>
                            @if(!empty($property_admin) && $property_admin->count() > 0)
                                @foreach($property_admin as $key => $pd)
                                    <option value="{{$pd->id}}">{{$pd->name}}</option>
                                @endforeach
                            @endif
                            </select>
                            <span class="text-danger">
                                <strong id="property_admin_user_id-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputStatus">Category</label>
                            <select class="form-control category_id" multiple name="category_id[]" id="category_id">
                            
                            @if(!empty($category) && $category->count() > 0)
                                @foreach($category as $key => $cat)
                                <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                                @endforeach
                            @endif
                            </select>
                            <span class="text-danger">
                                <strong id="category_id-error"></strong>
                            </span>
                        </div>
                         <div class="form-group col-md-4">
                            <label for="exampleSelectPhoto">Area</label>
                            <select class="form-control" id="area_id" name="area_id">
                            <option value="">--Select--</option>
                            @if(!empty($area) && $area->count() > 0)
                                @foreach($area as $key => $avalue)
                                    <option value="{{$avalue->id}}">{{$avalue->area_name}}</option>
                                @endforeach
                            @endif
                            </select>
                            <span class="text-danger">
                                <strong id="area_id-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputRole">Featured Events</label>
                            <select class="form-control" id="featured_event" name="featured_event">
                                <option value="">--Select--</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <span class="text-danger">
                                <strong id="featured_event-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <textarea class="form-control ckeditor" id="description" name="desc"></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addEventsSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->