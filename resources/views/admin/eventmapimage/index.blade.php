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
                    <a id="clear16" class="btn btn-secondary" href="{{route('eventmapimage')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addEventMapImage" tabindex="">ADD NEW</a>
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
                  <h4 class="card-title" style="float:left">{{$title ?? ''}}</h4>
                  <div class="box-header ">
                        @if (session()->has('success'))
                        <h4 class="mess" style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 class="mess" style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="mapimagetableData">
                      <thead>
                        <tr>
                            <th>Event Map Image</th>
                            <th>@sortablelink('unique_id','Map Image Id')</th>
                            <th>@sortablelink('event_name','Event Name')</th>
                            <th>@sortablelink('created_at','Created On')</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                          <td><img src="{{asset('public/upload/mall_image/')}}/{{$value->map_image_name}}" alt=""></td>
                          <td>{{$value->unique_id}}</td>
                          <td>{{$value->event_name}}</td>
                          <td>{{$value->created_at}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editMapImage{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Event MapImage?')" href="{{route('eventmapimage.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                    <!-- Edit Modal HTML Markup -->
                        <div id="editMapImage{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit EventMapImage</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                    @csrf
                                    <form name="addEventMapImage" id="editEventMapImageform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Photo</label>
                                                    <input type="file" name="map_image_name" class="file-upload-default">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" value="{{$value->map_image_name}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                        <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Event Name</label>
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <select name="eventname" id="eventname" class="form-control common_id">
                                                        <option value=""> -- Select One --</option>
                                                        @foreach ($common_id as $common)
                                                            <option value="{{ $common->id }}" {{ $value->common_id == $common->id ? 'selected' : ''}}>{{ $common->event_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" value="event" name="type">
                                                </div> 
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editEventMapImageSubmit" data-id="{{$value->id}}" id="editAreaSubmit">Submit</button>
                                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /edit.modal -->

                        @endforeach @else
                        <tr>
                        <td colspan="6">No Records Found</td>
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
<script>
   
$(document).ready(function(){
           
    $(document).on('click','.editEventMapImageSubmit',function(e){
       
        var id = $(this).data('id');
        var formData = new FormData($("#editEventMapImageform"+id)[0]);           
            $( '.common_id-error' ).html( "" ); 

            var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('eventmapimage.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                    if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.eventname){
                        $( '.common_id-error' ).html( result.errors.eventname[0] );
                    }
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editMapImage'+id).modal('hide');
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
    $('#addEventMapImageSubmit').click(function(e){
            var formData = new FormData($("#addMapImageform")[0]);
            $( '#common_id-error' ).html( "" ); 
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addEventmapimage') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide(); 
                    if(result.errors.eventname){
                            $( '#common_id-error' ).html( result.errors.eventname[0] );
                        }
                    }
                if(result.status == true)
                {
                    var data = result.data;
                    // var propertyadmin =  result.data.propertyadmin;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addEventmapimageform")[0].reset();
                        $('#addEventmapimage').modal('hide');
                        window.location.reload();
                    }, 3000);

                    $("#addMapImageform")[0].reset();
                     window.location.reload();
                    
                    // $("#addMallBrandform")[0].reset();
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
                url: "{{route('eventmapimage.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val(),'type' : 'event'},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;         
                    var findnorecord = $('#mapimagetableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#mapimagetableData tr.norecord').remove();
                    }
                     $("#mapimagetableData tbody").html(data);
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
        url: "{{route('eventmapimageexport')}}",
        method: 'get',
        data: {'search':search,'type':type},
        success: function(result){
            $(result).table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "eventmapimage",
                filename: "eventmapimage" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls", //do not include extension
                fileext: ".xls" // file extension
              }); 
        }
    });
}
</script>
@endsection

<!-- Modal HTML Markup -->
<div id="addEventMapImage" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Map Image</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addMapImageform" id="addMapImageform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleSelectPhoto">Photo</label>
                            <input type="file" name="map_image_name" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputStatus">Event Name</label>
                            <select name="eventname" id="eventname" class="form-control">
                                <option value=""> -- Select One --</option>
                                @foreach ($common_id as $common)
                                    <option value="{{ $common->id }}">{{ $common->event_name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                                <strong id="common_id-error"></strong>
                            </span>
                            <input type="hidden" value="event" name="type">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addEventMapImageSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->