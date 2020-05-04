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
                <div class="pr-1 mb-3">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addShopsandMalls" tabindex="">
                        ADD NEW
                    </a>
                </div>           
            </div>
        </div>
    </div>
    <div class="tab">
        <button class="tablinks active mall"  onclick="openCity(event, 'mall')">Mall</button>
        <button class="tablinks shop" onclick="openCity(event, 'shop')">Shops</button>
    </div>
    <div id="mall" class="tabcontent">
        <div class="row">
            <div class="col-sm-12">
                <div class="d-flex align-items-center justify-content-md-end">
                    <div class="pr-1 mb-3 mb-xl-0">
                        <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text" id="search">
                                    <i class="mdi mdi-magnify"></i>
                                </span>
                                </div>
                                <input type="text" class="form-control" placeholder="search" id="mallsearchtext" aria-label="search" aria-describedby="search">
                        </div>
                    </div>
                    <div class="pr-1 mb-3 mb-xl-0">
                        <a id="mallsearch" class="btn btn-primary" onclick="searchfun('mall')"  tabindex="" style="">FILTER</a>
                    </div>
                    <div class="pr-1 mb-3 mb-xl-0">
                        <a id="clear16" class="btn btn-secondary" href="{{route('malls')}}" tabindex="" >
                            CLEAR
                        </a>
                    </div>
                    <div class="pr-1 mb-3 mb-xl-0">
                        <a id="export14" class="btn btn-secondary" onclick="fnExcelReport('mall')"  tabindex="">
                            EXPORT
                        </a>
                    </div>                
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                    <h4 class="card-title" style="float:left">{{$title}}-Mall</h4>
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
                        <table class="table table-hover" id="malltableData">
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
                            @if(!empty($malldata) && $malldata->count() > 0)
                                @foreach($malldata as $key => $value)
                            <tr>

                        <td>{{$value->unique_id}}</td>
                        <td>
                            @if($value->image!= null)
                                <img src="{{asset('public/upload/malls')}}{{'/'.$value->image}}" alt="">
                            @else

                            @endif
                        </td>
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

                            <!-- Edit Modal HTML Markup -->
                            <div id="editShopsandmalls{{$value->id}}" class="modal fade">
                                <div class="modal-dialog  modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title">Edit ShopsandMalls</h1>
                                        </div>
                                        <div class="modal-body">
                                        <p class="statusMsg"></p>
                                            <form name="addMallsform" id="editShopsmallsform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
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
                            <td colspan="9">No Records Found</td>
                            </tr>
                            @endif


                        </tbody>
                        </table>

                    </div>
                        <div id="paging">
                        {{ $malldata->appends(['type' => 'mall'])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!--admin tabcontent ends -->
    <div id="shop" class="tabcontent">
        <div class="row">
            <div class="col-sm-12">
                <div class="d-flex align-items-center justify-content-md-end">
                    <div class="pr-1 mb-3 mb-xl-0">
                        <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text" id="search">
                                    <i class="mdi mdi-magnify"></i>
                                </span>
                                </div>
                                <input type="text" class="form-control" placeholder="search" id="shopsearchtext" aria-label="search" aria-describedby="search">
                        </div>
                    </div>
                    <div class="pr-1 mb-3 mb-xl-0">
                        <a id="shopsearch" class="btn btn-primary" onclick="searchfun('shop')"  tabindex="" style="">FILTER</a>
                    </div>
                    <div class="pr-1 mb-3 mb-xl-0">
                        <a id="clear16" class="btn btn-secondary" href="{{route('malls')}}" tabindex="" >
                            CLEAR
                        </a>
                    </div>
                    <div class="pr-1 mb-3 mb-xl-0">
                        <a id="export14" class="btn btn-secondary" onclick="fnExcelReport('shop')"  tabindex="">
                            EXPORT
                        </a>
                    </div>                
                </div>
            </div>
        </div>
        @include('admin/malls/shop')
    </div> <!--customer tabcontent ends -->
        <script src="{{asset('public/js/file-upload.js')}}" ></script>
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
    function fnExcelReport(type)
    {
        var search = "";
        if(type == 'mall')
        {
            search = $("#mallsearchtext").val();
        }
        if(type == 'shop')
        {
            search = $("#shopsearchtext").val();
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        }); 
        $.ajax({
            url: "{{route('shopmallexport')}}",
            method: 'get',
            data: {'search':search,'type':type},
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
    function searchfun(type)
    {
        var search = "";
        if(type == 'mall')
        {
            search = $("#mallsearchtext").val();
        }
        if(type == 'shop')
        {
            search = $("#shopsearchtext").val();
        }
    $('.statusMsg').html('');
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });       
            $.ajax({
                    url: "{{route('shopsmalls.search')}}",
                    method: 'post',
                    data: {'search':search,'type':type},
                    success: function(result){
                        if(result.status == true)
                        {
                            var data = result.data;                    
                            
                            var findnorecord = $('#'+type+'tableData tr.norecord').length;
                            if(findnorecord > 0){
                                $('#'+type+'tableData tr.norecord').remove();
                                }
                            $('#'+type+'tableData tbody').html(data);
                            $(".tabcontent").css('display','none');
                            document.getElementById(type).style.display = "block";
                            $(".tablinks").removeClass("active");

                            $("."+type).addClass("active");

                            $("#"+type+"paging").hide();
                        }
                        else
                        {
                            $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');
                        }
                    }
                });
    }

    $(document).ready(function(){

    setTimeout(function(){
        $("h4.mess").remove();
    }, 5000 ); 

    $(".tablinks").removeClass("active");
    var type = "<?php echo $type; ?>";
    $(".tabcontent").css('display','none');
    document.getElementById(type).style.display = "block";
    $("."+type).addClass("active");

    setTimeout(function() {
            $('.box-header').slideUp();
        }, 5000);

    document.getElementById(type).style.display = "block";

    
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
                    var data = result.data;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editShopsandmalls'+id).modal('hide');
                        var url = window.location.href;

                        if (url.indexOf("?") > -1) 
                        {
                            url = url.substring(0, url.indexOf('?'));
                        }
                        window.location.href = url + "?type="+data.type;
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
                { $(".statusMsg").show();
                    var data = result.data;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addMallsform")[0].reset();
                        $('#addShopsandMalls').modal('hide');
                        var url = window.location.href;
                        if (window.location.href.indexOf("?") > -1) 
                        {                            
                            url = url.substring(0, url.indexOf('?'));
                        }
                        window.location.href = url + "?type="+data.type;
                    }, 3000);                    
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
<div id="addShopsandMalls" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add{{$title}}</h1>
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