
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
                                        <h1 class="modal-title">Edit Malls</h1>
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
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Name</label>
                                                    <input type="text" required class="form-control" Re id="fullname" value="{{$value->name}}" name="name" placeholder="Name">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputEmail1">Location</label>
                                                    <input type="email" required class="form-control" id="location{{$value->id}}" value="{{$value->location}}" name="location" placeholder="location">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputPassword">Opening Hours</label>
                                                    <input type="text" class="form-control timepicker" value="{{$value->openinghrs}}"  id="openinghrs" name="openinghrs" placeholder="Opening Houres">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectGender">Closing Hours</label>
                                                    <input type="text" class="form-control timepicker" value="{{$value->closinghrs}}" id="closinghrs" name="closinghrs" placeholder="Closing Hours">
                                                </div>                 
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectGender">Contact Info</label>
                                                    <input type="text" class="form-control" id="contact" value="{{$value->contact}}" name="contact" placeholder="Contact">
                                                </div> 
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputRole">Property Admin</label>
                                                    <select class="form-control" id="property_admin_user_id" name="property_admin_user_id">
                                                    <option value="">--Select--</option>
                                                    @if(!empty($property_admin) && $property_admin->count() > 0)
                                                        @foreach($property_admin as $key => $pd)
                                                            <option value="{{$pd->id}}" {{ $value->property_admin_user_id == $pd->id ? 'selected' : ''}} >{{$pd->name}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
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
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Area</label>
                                                    <select class="form-control " id="area_id" name="area_id">
                                                    <option value="">--Select--</option>
                                                    @if(!empty($area) && $area->count() > 0)
                                                        @foreach($area as $key => $avalue)
                                                            <option value="{{$avalue->id}}" {{ $value->area_id == $avalue->id ? 'selected' : ''}}>{{$avalue->area_name}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
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
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Type</label>
                                                    <select class="form-control" id="type" name="type">
                                                        <option value="">--Select--</option>
                                                        <option value="mall" {{ $value->type == 'mall' ? 'selected' : ''}}>Mall</option>
                                                        <option value="shop" {{ $value->type == 'shop' ? 'selected' : ''}}>Shop</option>
                                                    </select>
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
      // place variable will have all the information you are looking for.
    //   $('#lat').val(place.geometry['location'].lat());
    //   $('#long').val(place.geometry['location'].lng());
      $('#my-modal').modal('show');
    });
  }
</script>

<script>
   

$(document).ready(function(){
  
    $(".edit").click(function(){
        var sid = $(this).data('id');
  
        var input = document.getElementById('location'+sid);
            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            // place variable will have all the information you are looking for.
            //   $('#lat').val(place.geometry['location'].lat());
            //   $('#long').val(place.geometry['location'].lng());
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
                        var cdate = "<?php echo date("d F Y",strtotime($value->created_at)) ?>";
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
                    
                        var image = '';
                    if(data.image != null)
                    {
                        image = data.image;
                    }
                    if(data.created_at)
                    {
                        var cdate = "<?php echo date("d F Y",strtotime($value->created_at)) ?>";
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
                    "<td>"+data.propertyadmin+"</td>" +
                    "<td>"+cdate+"</td>" +
                    "<td>"+data.created_by+"</td>" +
                    "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editShopsandmalls'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this Mall?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    $("#mallstableData tbody").html(tr_str);
                    $("#paging").hide();
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
});
</script>
@endsection

<!-- Modal HTML Markup -->
<div id="addShopsandMalls" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Malls</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addMallsform" id="addMallsform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleSelectPhoto">Photo</label>
                            <input type="file" name="image" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Name</label>
                            <input type="text" required class="form-control"  id="fullname" name="name" placeholder="Name">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputEmail1">Location</label>
                            <input type="email" required class="form-control location" id="location" name="location" placeholder="location">
                    <!-- <input type="hidden" name="lat">
                    <input type="hidden" name="long"> -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputPassword">Opening Hours</label>
                            <input type="text" class="form-control timepicker" id="openinghrs" name="openinghrs" placeholder="Opening Houres">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleSelectGender">Closing Hours</label>
                            <input type="text" class="form-control timepicker" id="closinghrs" name="closinghrs" placeholder="Closing Hours">
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
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputStatus">Category</label>
                            <select class="form-control category_id" multiple name="category_id[]" id="category_id">
                            <!-- <option value="">--Select--</option> -->
                            @if(!empty($category) && $category->count() > 0)
                                @foreach($category as $key => $cat)
                                <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                                @endforeach
                            @endif
                            </select>
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