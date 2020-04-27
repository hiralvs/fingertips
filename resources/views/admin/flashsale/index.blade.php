
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
                    <a id="clear16" class="btn btn-secondary" href="{{route('flashsale')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addFlashsale" tabindex="">ADD NEW</a>
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
                  <div class="box-header ">
                        @if (session()->has('success'))
                        <h4 class="mess" style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 class="mess" style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="flashsalestableData">
                      <thead>
                        <tr>
                            <th>@sortablelink('id')</th>
                            <th>@sortablelink('name')</th>
                            <th>@sortablelink('brand_name','Brand Name')</th>
                            <th>@sortablelink('start_date','Start Date')</th>
                            <th>@sortablelink('start_time','Start Time')</th>
                            <th>@sortablelink('end_date','End Date')</th>
                            <th>@sortablelink('end_time','End Time')</th>
                            <th>@sortablelink('discount_percentage','Discount Percentage')</th>
                            <th>@sortablelink('created_at','Created at')</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                          <td>{{$value->unique_id}}</td>
                          <td>{{$value->name}}</td>
                          <td>{{$value->brandname}}</td>
                          <td>{{$value->start_date}}</td>
                          <td>{{$value->start_time}}</td>
                          <td>{{$value->end_date}}</td>
                          <td>{{$value->end_time}}</td>
                          <td>{{$value->discount_percentage}}</td>
                          <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editFlashsales{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Flash Sale?')" href="{{route('flashsale.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editFlashsales{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit {{$title}}</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="editFlashsales" id="editFlashsalesform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label for="exampleSelectPhoto">Photo</label>
                                                    <input type="file" name="image" class="file-upload-default">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" value="{{$value->image}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                        <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <label for="exampleInputEmail1">Product</label>
                                                    <select class="form-control" id="product" name="product">
                                                    <option value="">--Select--</option>
                                                    @if(!empty($product) && $product->count() > 0)
                                                        @foreach($product as $key => $pd)
                                                            <option value="{{$pd->id}}" {{ $value->product_id == $pd->id? 'selected' : ''}}>{{$pd->name}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="product-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>                        
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputName">Discount Percentage</label>
                                                    <input type="text" required class="form-control"  id="discount_percentage" name="discount_percentage" placeholder="Discount Percentage" value="{{$value->discount_percentage}}">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="discount_percentage-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputRole">Quantity</label>
                                                    <input type="text" class="form-control" id="quantity" value="{{$value->quantity}}" name="quantity" placeholder="Quantity">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputPassword">Start Date</label>
                                                    <input type="date" class="form-control" id="start_date" name="start_date" placeholder="Start Date" value="{{$value->start_date}}">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="start_date-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="exampleSelectGender">Start Time</label>
                                                    <input type="text" class="form-control timepicker" id="start_time" value="{{$value->start_time}}" name="start_time" placeholder="Start time">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="start_time-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>                 
                                                <div class="form-group col-md-3">
                                                    <label for="exampleSelectGender">End Date</label>
                                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{$value->end_date}}" placeholder="End Date">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="end_date-error{{$value->id}}"></strong>
                                                    </span>
                                                </div> 
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputRole">End Time</label>
                                                    <input type="text" value="{{$value->end_time}}" class="form-control timepicker" id="end_time" name="end_time" placeholder="End time">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="end_time-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                        <button type="button" class="btn btn-primary mr-2 editFlashsaleSubmit" data-id="{{$value->id}}" id="editFlashsaleSubmit">Submit</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- edit /.modal -->
                        @endforeach
                        @else
                        <tr>
                        <td colspan="10">No Records Found</td>
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
<style>
    .pac-container {
        z-index: 10000 !important;
    }
</style>

<script>
   

$(document).ready(function(){
    setTimeout(function(){
       $("h4.mess").remove();
    }, 5000 ); // 5 secs

    $('.timepicker').datetimepicker({
        format: 'HH:mm:ss'
    }); 

    $(document).on('click','.editFlashsaleSubmit',function(e){
       
        var id = $(this).data('id');
        var formData = new FormData($("#editFlashsalesform"+id)[0]);

        $( '#product-error'+id).html( "" );
        $( '#discount_percentage-error'+id ).html( "" );
        $( '#start_date-error'+id ).html( "" );
        $( '#start_time-error'+id ).html( "" );
        $( '#end_date-error'+id ).html( "" );
        $( '#end_time-error'+id ).html( "" );

        var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('flashsale.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.product){
                        $( '#product-error'+id ).html( result.errors.product[0] );
                    }
                    if(result.errors.discount_percentage){
                        $( '#discount_percentage-error' ).html( result.errors.discount_percentage[0] );
                    }
                    if(result.errors.start_date){
                        $( '#start_date-error'+id ).html( result.errors.start_date[0] );
                    }
                    if(result.errors.start_time){
                        $( '#start_time-error'+id ).html( result.errors.start_time[0] );
                    }
                    if(result.errors.end_date){
                        $( '#end_date-error'+id ).html( result.errors.end_date[0] );
                    }
                    if(result.errors.end_time){
                        $( '#end_time-error'+id ).html( result.errors.end_time[0] );
                    }           
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editFlashsales'+id).modal('hide');
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

        $('#addFlashsaleSubmit').click(function(e){
            var formData = new FormData($("#addFlashsaleform")[0]);
            $( '#product-error' ).html( "" );
            $( '#discount_percentage-error' ).html( "" );
            $( '#start_date-error' ).html( "" );
            $( '#start_time-error' ).html( "" );
            $( '#end_date-error' ).html( "" );
            $( '#end_time-error' ).html( "" );
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addFlashsale') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                    if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.product){
                        $( '#product-error' ).html( result.errors.product[0] );
                    }
                    if(result.errors.discount_percentage){
                        $( '#discount_percentage-error' ).html( result.errors.discount_percentage[0] );
                    }
                    if(result.errors.start_date){
                        $( '#start_date-error' ).html( result.errors.start_date[0] );
                    }
                    if(result.errors.start_time){
                        $( '#start_time-error' ).html( result.errors.start_time[0] );
                    }
                    if(result.errors.end_date){
                        $( '#end_date-error' ).html( result.errors.end_date[0] );
                    }
                    if(result.errors.end_time){
                        $( '#end_time-error' ).html( result.errors.end_time[0] );
                    }
                    if(result.errors.product){
                        $( '#product-error' ).html( result.errors.product[0] );
                    }             
                }
                if(result.status == true)
                {
                    var data = result.data.malls;
                    var propertyadmin =  result.data.propertyadmin;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addFlashsaleform")[0].reset();
                        $('#addFlashsale').modal('hide');
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

        $(document).on('click','#search',function(){ 
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });       
        $.ajax({
                url: "{{route('flashsale.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val()},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    
                    var findnorecord = $('#flashsalestableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#flashsalestableData tr.norecord').remove();
                        }
                    $("#flashsalestableData tbody").html(data);
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
        url: "{{route('flashsaleexport')}}",
        method: 'get',
        data: {'search':search},
        success: function(result){
            $(result).table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "flashsales",
                filename: "flashsales" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls", //do not include extension
                fileext: ".xls" // file extension
              }); 
        }
    });
}
</script>
@endsection

<!-- Modal HTML Markup -->
<div id="addFlashsale" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add {{$title}}</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addFlashsaleform" id="addFlashsaleform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="exampleSelectPhoto">Photo</label>
                            <input type="file" name="image" id="image" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="exampleInputEmail1">Product</label>
                            <select class="form-control" id="product" name="product">
                            <option value="">--Select--</option>
                            @if(!empty($product) && $product->count() > 0)
                                @foreach($product as $key => $pd)
                                    <option value="{{$pd->id}}">{{$pd->name}}</option>
                                @endforeach
                            @endif
                            </select>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="product-error"></strong>
                            </span>
                        </div>                        
                        <div class="form-group col-md-3">
                            <label for="exampleInputName">Discount Percentage</label>
                            <input type="text" required class="form-control"  id="discount_percentage" name="discount_percentage" placeholder="Discount Percentage">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="discount_percentage-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="exampleInputRole">Quantity</label>
                            <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Quantity">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="exampleInputPassword">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" placeholder="Start Date">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="start_date-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="exampleSelectGender">Start Time</label>
                            <input type="text" class="form-control timepicker" id="start_time" name="start_time" placeholder="Start time">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="start_time-error"></strong>
                            </span>
                        </div>                 
                        <div class="form-group col-md-3">
                            <label for="exampleSelectGender">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" placeholder="End Date">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="end_date-error"></strong>
                            </span>
                        </div> 
                        <div class="form-group col-md-3">
                            <label for="exampleInputRole">End Time</label>
                            <input type="text" class="form-control timepicker" id="end_time" name="end_time" placeholder="End time">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="end_time-error"></strong>
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addFlashsaleSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->