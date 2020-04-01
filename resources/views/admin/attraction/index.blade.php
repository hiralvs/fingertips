
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
                    <a id="clear16" class="btn btn-secondary" href="{{route('attractions')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addAttractions" tabindex="">ADD NEW</a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="export14" class="btn btn-secondary" href="{{route('user.csv')}}" tabindex="">EXPORT</a>
                </div>             
            </div>
        </div>
    </div>
    <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title" style="float:left">{{$title}}</h4>
                  <div class="box-header ">
                        @if (session()->has('success'))
                        <h4 class="mess" style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 class="mess" style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="attractionstableData">
                      <thead>
                        <tr>
                            <th>@sortablelink('id')</th>
                            <th>Image</th>
                            <th>@sortablelink('Name')</th>
                            <th>@sortablelink('location')</th>
                            <th>@sortablelink('Property Admin')</th>
                            <th>@sortablelink('Booking Allowed')</th>
                            <th>@sortablelink('Cost')</th>
                            <th>@sortablelink('description','Description')</th>
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
                          <td><img src="{{asset('public/upload/attractions/')}}/{{$value->attraction_image}}" alt=""></td>
                          <td>{{$value->attraction_name}}</td>
                          <td>{{$value->location}}</td>
                          <td>{{$value->property_admin_user_id}}</td>
                          <td>{{$value->booking_allowed}}</td>
                          <td>{{$value->cost}}</td>
                          <td>{{$value->description}}</td>
                          <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                          <td>{{$value->created_by}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editAttractions{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Attraction?')" href="{{route('attraction.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editAttractions{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Attractions</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="editShopsmalls" id="editAttractionsform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Photo</label>
                                                    <input type="file" name="attraction_image" class="file-upload-default">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" value="{{$value->attraction_image}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                        <span class="input-group-append">
                                                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Name</label>
                                                    <input type="text" required class="form-control attraction_name" Re id="attraction_name" value="{{$value->attraction_name}}" name="attraction_name" placeholder="Name">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
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
                                                    <input type="text" class="form-control opening_time" value="{{$value->opening_time}}"  id="openinghrs" name="opening_time" placeholder="Opening Houres">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectGender">Closing Hours</label>
                                                    <input type="text" class="form-control closing_time" value="{{$value->closing_time}}" id="closinghrs" name="closing_time" placeholder="Closing Hours">
                                                </div>                 
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectGender">Contact Info</label>
                                                    <input type="text" class="form-control contact" id="contact" value="{{$value->contact}}" name="contact" placeholder="Contact">
                                                </div> 
                                            </div>
                                            <div class="row">
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
                                            </div>
                                            <div class="row">                                            
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputRole">Featured Mall</label>
                                                    <select class="form-control featured_mall" id="featured_mall" name="featured_mall">
                                                        <option value="">--Select--</option>
                                                        <option value="yes" {{ $value->featured_mall == 'yes' ? 'selected' : ''}}>Yes</option>
                                                        <option value="no" {{ $value->featured_mall == 'no' ? 'selected' : ''}}>No</option>
                                                    </select>
                                                    <span class="text-danger">
                                                        <strong class="featured_mall-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputRole">Booking Allowed</label>
                                                    <select class="form-control booking_allowed" id="booking_allowed" name="booking_allowed">
                                                        <option value="">--Select--</option>
                                                        <option value="yes" {{ $value->booking_allowed == 'yes' ? 'selected' : ''}}>Yes</option>
                                                        <option value="no" {{ $value->booking_allowed == 'no' ? 'selected' : ''}}>No</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Cost</label>
                                                    <input type="text" required class="form-control cost" Re id="cost" value="{{$value->cost}}" name="cost" placeholder="cost">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="cost-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <textarea class="form-control ckeditor" id="description{{$value->id}}" name="desc">{{$value->description}}</textarea>
                                                </div>
                                            </div>
                                        <button type="button" class="btn btn-primary mr-2 editAttractionsSubmit" data-id="{{$value->id}}" id="editAreaSubmit">Submit</button>
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
        
    $(document).on('click','.editAttractionsSubmit',function(e){
       
        var id = $(this).data('id');
        var formData = new FormData($("#editAttractionsform"+id)[0]);
       
        var message = CKEDITOR.instances['description'+id].getData();
            $( '.attraction_image-error' ).html( "" );
            $( '.property_admin_user_id-error' ).html( "" );
            $( '.category_id-error' ).html( "" );
            $( '.area_id-error' ).html( "" );
            $( '.featured_mall-error' ).html( "" );
            $( '.cost-error' ).html( "" );
        formData.append('description',message);
        var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('attraction.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.attraction_image){
                        $( '.attraction_image-error' ).html( result.errors.attraction_image[0] );
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
                    if(result.errors.featured_mall){
                        $( '.featured_mall-error' ).html( result.errors.featured_mall[0] );
                    }                    
                    if(result.errors.cost){
                        $( '.cost-error' ).html( result.errors.cost[0] );
                    }                    
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editAttractions'+id).modal('hide');
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
    $('#addAttractionSubmit').click(function(e){
            var formData = new FormData($("#addAttractionsform")[0]);
            var message = CKEDITOR.instances['description'].getData();
            $( '#attraction_image-error' ).html( "" );
            $( '#property_admin_user_id-error' ).html( "" );
            $( '#category_id-error' ).html( "" );
            $( '#area_id-error' ).html( "" );
            $( '#featured_mall-error' ).html( "" );
            $( '#cost-error' ).html( "" );

            formData.append('description',message);
                
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addAttractions') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.attraction_image){
                        $( '#attraction_image-error' ).html( result.errors.attraction_image[0] );
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
                    if(result.errors.featured_mall){
                        $( '#featured_mall-error' ).html( result.errors.featured_mall[0] );
                    }                    
                    if(result.errors.cost){
                        $( '#cost-error' ).html( result.errors.cost[0] );
                    }                    
                }
                if(result.status == true)
                {
                    var data = result.data.attractions;
                    var propertyadmin =  result.data.propertyadmin;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addAttractionsform")[0].reset();
                        $('#addAttractions').modal('hide');
                        window.location.reload();
                    }, 3000);
                    
                    $("#addAttractionsform")[0].reset();
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
                url: "{{route('attraction.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val()},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    
                    var findnorecord = $('#attractionstableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#attractionstableData tr.norecord').remove();
                        }
                    
                    var attraction_image = '';
                    if(data.attraction_image != null)
                    {
                        attraction_image = data.attraction_image;
                    }
                    if(data.created_at)
                    {
                        var cdate = date(data.created_at);
                    }
                    var deleteurl = '{{ route("attraction.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);
                    var imageurl = "{{asset('public/upload/attractions')}}";
                    var tr_str = "<tr>"+
                    "<td>"+data.unique_id+"</td>" +
                    "<td><img src="+imageurl+"/"+attraction_image+"></td>" +
                    "<td>"+data.attraction_name+"</td>" +
                    "<td>"+data.location+"</td>"+
                    "<td>"+data.property_admin_user_id+"</td>" +
                    "<td>"+data.booking_allowed+"</td>" +
                    "<td>"+data.cost+"</td>" +
                    "<td>"+data.description+"</td>" +
                    "<td>"+data.created_at+"</td>" +
                    "<td>"+data.created_by+"</td>" +
                    "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editAttractions'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this Attractions?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    console.log(tr_str);
                    $("#attractionstableData tbody").html(tr_str);
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

</script>
@endsection

<!-- Modal HTML Markup -->
<div id="addAttractions" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Attractions</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addAttractionsform" id="addAttractionsform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleSelectPhoto">Photo</label>
                            <input type="file" name="attraction_image" id="attraction_image" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                                <span class="text-danger">
                                    <strong id="attraction_image-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Name</label>
                            <input type="text" required class="form-control"  id="attraction_name" name="attraction_name" placeholder="Name">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputEmail1">Location</label>
                            <input type="email" required class="form-control" id="location" name="location" placeholder="location">
                            <input type="hidden" name="latitude" id="lat">
                            <input type="hidden" name="longitude" id="long">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputPassword">Opening Time</label>
                            <input type="text" class="form-control timepicker" id="opening_time" name="opening_time" placeholder="Opening Time">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleSelectGender">Closing Time</label>
                            <input type="text" class="form-control timepicker" id="closing_time" name="closing_time" placeholder="Closing Time">
                        </div>                 
                        <div class="form-group col-md-4">
                            <label for="exampleSelectGender">Contact Info</label>
                            <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact">
                        </div> 
                    </div>
                    <div class="row">
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
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputRole">Featured Mall</label>
                            <select class="form-control" id="featured_mall" name="featured_mall">
                                <option value="">--Select--</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <span class="text-danger">
                                <strong id="featured_mall-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputRole">Booking Allowed</label>
                            <select class="form-control" id="booking_allowed" name="booking_allowed">
                                <option value="">--Select--</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleSelectGender"> Cost </label>
                            <input type="text" class="form-control" id="cost" name="cost" placeholder="Cost">
                            <span class="text-danger">
                                <strong id="cost-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <textarea class="form-control ckeditor" id="description" name="desc"></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addAttractionSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->