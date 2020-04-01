
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
                    <a id="clear16" class="btn btn-secondary" href="{{route('eventbrands')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addEventBrand" tabindex="">ADD NEW</a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="export14" class="btn btn-secondary" onclick="fnExcelReport('event')" tabindex="">EXPORT</a>
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
                    <table class="table table-hover" id="eventbrandstableData">
                      <thead>
                        <tr>
                            <th>@sortablelink('id')</th>
                            <th>@sortablelink('Name')</th>
                            <th>@sortablelink('event_name','Event Name')</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                          <td>{{$value->unique_id}}</td>
                          <td>{{$value->brandname}}</td>
                          <td>{{$value->event_name}}</td>
                          <td>{{$value->status}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editEventBrands{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Brand?')" href="{{route('eventbrands.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editEventBrands{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Event Brand</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="editEventBrandform" id="editEventBrandsform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Brand Name</label>
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <select name="brand_id" id="brandname" class="form-control brand_id">
                                                        <option value=""> -- Select One --</option>
                                                        @if(!empty($brand_id) && $brand_id->count() > 0)
                                                            @foreach($brand_id as $key => $pd)
                                                                <option value="{{$pd->id}}" {{ $value->brand_id == $pd->id ? 'selected' : ''}} >{{$pd->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                     <span class="text-danger">
                                                        <strong class="brand_id-error"></strong>
                                                    </span>
                                                </div>   
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Event Name</label>
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <select name="common_id" id="commonname" class="form-control common_id">
                                                        <option value=""> -- Select One --</option>
                                                        @if(!empty($events) && $events->count() > 0)
                                                            @foreach ($events as $key => $pd)
                                                                  <option value="{{$pd->id}}" {{ $value->common_id == $pd->id ? 'selected' : ''}} >{{$pd->event_name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <span class="text-danger">
                                                        <strong class="common_id-error"></strong>
                                                    </span>
                                                    <input type="hidden" value="event" name="type">
                                                </div> 
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Status</label>
                                                    <select class="form-control status" id="status" name="status">
                                                        <option value="" selected="">Status</option>
                                                        <option value="Active" {{ $value->status == 'Active' ? 'selected' : ''}}>Active</option>
                                                        <option value="Inactive" {{ $value->status == 'Inactive' ? 'selected' : ''}}>Inactive</option>
                                                    </select>
                                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong class="status-error"></strong>
                                                    </span>
                                                </div> 
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editEventBrandsSubmit" data-id="{{$value->id}}" id="editEventBrandsSubmit">Submit</button>
                                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /edit.modal -->
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
   
$(document).ready(function(){
     setTimeout(function(){
           $("h4.mess").remove();
        }, 5000 );
    $(document).on('click','.editEventBrandsSubmit',function(e){
       
        var id = $(this).data('id');

        $( '.brand_id-error' ).html( "" );
        $( '.common_id-error' ).html( "" );
        $( '.status-error' ).html( "" ); 
        var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('eventbrands.update') }}",
                method: 'post',
                data: $("#editEventBrandsform"+id).serialize(),
                success: function(result){
                    if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.brand_id){
                        $( '.brand_id-error' ).html( result.errors.brand_id[0] );
                    }
                    if(result.errors.common_id){
                        $( '.common_id-error' ).html( result.errors.common_id[0] );
                    }                    
                    if(result.errors.status){
                        $( '.status-error' ).html( result.errors.status[0] );
                    }                    
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editMallBrands'+id).modal('hide');
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
    $('#addEventBrandSubmit').click(function(e){
            
            $( '#brand_id-error' ).html( "" );
            $( '#common_id-error' ).html( "" );
            $( '#status-error' ).html( "" );    

            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addEventBrand') }}",
                method: 'post',
                data: $("#addEventBrandform").serialize(),
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.brand_id){
                            $( '#brand_id-error' ).html( result.errors.brand_id[0] );
                        }
                    if(result.errors.common_id){
                            $( '#common_id-error' ).html( result.errors.common_id[0] );
                        }                  
                    if(result.errors.status){
                            $( '#status-error' ).html( result.errors.status[0] );
                        }                  
                }
                if(result.status == true)
                {
                    var data = result.data;
                    // var propertyadmin =  result.data.propertyadmin;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addEventBrandform")[0].reset();
                        $('#addEventBrand').modal('hide');
                        window.location.reload();
                    }, 3000);

                    $("#addeventform")[0].reset();
                     window.location.reload();
                    
                    $("#addEventBrandform")[0].reset();
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
                url: "{{route('eventbrands.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val(),'type' : 'event'},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    
                    var findnorecord = $('#eventbrandstableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#eventbrandstableData tr.norecord').remove();
                    }
                    
                    $("#eventbrandstableData tbody").html(data);
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

function fnExcelReport(type)
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
        url: "{{route('eventbrandexport')}}",
        method: 'get',
        data: {'search':search,'type':type},
        success: function(result){
            $(result).table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "eventbrand",
                filename: "eventbrand" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls", //do not include extension
                fileext: ".xls" // file extension
              }); 
        }
    });
}
</script>
@endsection

<!-- Modal HTML Markup -->
<div id="addEventBrand" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add EventBrand</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addEventBrandform" id="addEventBrandform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputStatus">Brand Name</label>
                            <select name="brand_id" id="brandname" class="form-control">
                                <option value=""> -- Select One --</option>
                                @foreach ($brand_id as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                                <strong id="brand_id-error"></strong>
                            </span>
                            <input type="hidden" value="event" name="type">
                        </div>   
                        <div class="form-group col-md-4">
                            <label for="exampleInputStatus">Event Name</label>
                            <select name="common_id" id="commonname" class="form-control">
                                <option value=""> -- Select One --</option>
                                @foreach ($events as $common)
                                    <option value="{{ $common->id }}">{{ $common->event_name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                                <strong id="common_id-error"></strong>
                            </span>
                        </div>   
                        <div class="form-group col-md-4">
                            <label for="exampleInputStatus">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="" selected="">Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                            <span class="text-danger">
                                <strong id="status-error"></strong>
                            </span>
                        </div> 
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addEventBrandSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->c