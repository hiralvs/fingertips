
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
                    <a id="clear16" class="btn btn-secondary" href="{{route('mallmapimage')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addMallmapimage" tabindex="">ADD NEW</a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="export14" class="btn btn-secondary" onclick="fnExcelReport('malls')" tabindex="">EXPORT</a>
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
                    <table class="table table-hover" id="mallmapimagetableData">
                      <thead>
                        <tr>
                            <th>Mall Map Image</th>
                             <th>@sortablelink('unique_id','Map Image Id')</th>
                            <th>@sortablelink('mallname','Mall Name')</th>
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
                          <td>{{$value->mallname}}</td>
                          <td>{{$value->created_at}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editMallMapImage{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Mall Image?')" href="{{route('mallmapimage.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                    <!-- Edit Modal HTML Markup -->
                        <div id="editMallMapImage{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit MallImage</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                    @csrf
                                    <form name="addMallMapImage" id="editMallMapImageform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
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
                                                    <label for="exampleInputStatus">Mall and Shop Name</label>
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <select name="mallname" id="mallname" class="form-control common_id">
                                                        <option value=""> -- Select One --</option>
                                                        @foreach ($common_id as $common)
                                                            <option value="{{ $common->id }}" {{ $value->common_id == $common->id ? 'selected' : ''}}>{{ $common->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" value="malls" name="type">
                                                </div> 
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editMallMapImageSubmit" data-id="{{$value->id}}" id="editAreaSubmit">Submit</button>
                                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /edit.modal -->
                        @endforeach
                        @else
                        <tr>
                        <td colspan="5">No Records Found</td>
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
       $(document).on('click','.editMallMapImageSubmit',function(e){
       
        var id = $(this).data('id');
        var formData = new FormData($("#editMallMapImageform"+id)[0]);           
            // $( '.common_id-error' ).html( "" ); 

            var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('mallmapimage.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                    if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.mallname){
                        $( '.common_id-error' ).html( result.errors.mallname[0] );
                    }
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editMallMapImage'+id).modal('hide');
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
            $('#addMallMapImageSubmit').click(function(e){
            var formData = new FormData($("#addMallMapform")[0]);
            $( '#common_id-error' ).html( "" ); 
            $( '#map_image_name-error' ).html( "" );             
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addMallmapimage') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide(); 
                    if(result.errors.mallname){
                            $( '#common_id-error' ).html( result.errors.mallname[0] );
                        }
                    if(result.errors.map_image_name)
                    {
                        $( '#map_image_name-error' ).html( result.errors.map_image_name[0] ); 
                    }
                }
                if(result.status == true)
                {
                    var data = result.data;
                    // var propertyadmin =  result.data.propertyadmin;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addMallmapimageform")[0].reset();
                        $('#addMallmapimage').modal('hide');
                        window.location.reload();
                    }, 3000);

                    $("#addMallMapform")[0].reset();
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
                url: "{{route('mallmapimage.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val(),'type' : 'malls'},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    var findnorecord = $('#mallmapimagetableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#mallmapimagetableData tr.norecord').remove();
                    }
                    
                    $("#mallmapimagetableData tbody").html(data);
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
        url: "{{route('mallmapimageexport')}}",
        method: 'get',
        data: {'search':search,'type':type},
        success: function(result){
            $(result).table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "mallmapimage",
                filename: "mallmapimage" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls", //do not include extension
                fileext: ".xls" // file extension
              }); 
        }
    });
}
</script>       
@endsection

<!-- Modal HTML Markup -->
<div id="addMallmapimage" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Map Image</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addMallMapform" id="addMallMapform" role="form" method="POST" enctype= "multipart/form-data">
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
                            <span class="text-danger">
                                <strong id="map_image_name-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputStatus">Mall and Shop Name</label>
                            <select name="mallname" id="mallname" class="form-control">
                                <option value=""> -- Select One --</option>
                                @foreach ($common_id as $common)
                                    <option value="{{ $common->id }}">{{ $common->name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" value="malls" name="type">
                             <span class="text-danger">
                                <strong id="common_id-error"></strong>
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addMallMapImageSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->