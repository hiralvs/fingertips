
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
                    <a id="clear16" class="btn btn-secondary" href="{{route('attractionmapimage')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addAttractionMapImage" tabindex="">ADD NEW</a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="export14" class="btn btn-secondary" onclick="fnExcelReport()" tabindex="">EXPORT</a>
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
                    <table class="table table-hover" id="attractionmapimagetableData">
                      <thead>
                        <tr>
                            <th>Attraction Image</th>
                            <th>@sortablelink('Attractiom Image Id')</th>
                            <th>@sortablelink('Attraction Name')</th>
                            <th>@sortablelink('Created On')</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                          <td><img src="{{asset('public/upload/mall_image/')}}/{{$value->map_image_name}}" alt=""></td>
                          <td>{{$value->unique_id}}</td>
                          <td>{{$value->attraction_name}}</td>
                          <td>{{$value->created_at}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editAttractionMapImage{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Attraction Map Image?')" href="{{route('attractionmapimage.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                    <!-- Edit Modal HTML Markup -->
                        <div id="editAttractionMapImage{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit AttractionMapImage</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                    @csrf
                                    <form name="addAttractionMapImage" id="editAttractionMapImageform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
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
                                                    <label for="exampleInputStatus">Attraction Name</label>
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <select name="common_id" id="commonname" class="form-control common_id">
                                                        <option value=""> -- Select One --</option>
                                                        @foreach ($common_id as $common)
                                                            <option value="{{ $common->id }}" {{ $value->common_id == $common->id ? 'selected' : ''}}>{{ $common->attraction_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" value="attraction" name="type">
                                                </div> 
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editAttractionMapImageSubmit" data-id="{{$value->id}}" id="editAreaSubmit">Submit</button>
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
       $(document).on('click','.editAttractionMapImageSubmit',function(e){
       
        var id = $(this).data('id');
        var formData = new FormData($("#editAttractionMapImageform"+id)[0]);           
            $( '.common_id-error' ).html( "" ); 

            var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('attractionmapimage.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                    if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.attractionname){
                        $( '.common_id-error' ).html( result.errors.attractionname[0] );
                    }
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editAttractionMapImage'+id).modal('hide');
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
            $('#addAttractionMapImageSubmit').click(function(e){
            var formData = new FormData($("#addAttractionMapImageform")[0]);
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
                    if(result.errors.title){
                            $( '#title-error' ).html( result.errors.title[0] );
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

                    $("#addAttractionMapImageform")[0].reset();
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
                url: "{{route('attractionmapimage.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val(),'type' : 'attraction'},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    var findnorecord = $('#attractionmapimagetableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#attractionmapimagetableData tr.norecord').remove();
                    }
                    var deleteurl = '{{ route("attractionmapimage.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);
                    var tr_str = "<tr>"+
                    "<td>"+data.map_image_name+"</td>" +
                    "<td>"+data.unique_id+"</td>" +
                    "<td>"+data.common_id+"</td>" +
                    "<td>"+data.created_at+"</td>" +
                    "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editAttractionMapImage'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this Attraction Map?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    $("#attractionmapimagetableData tbody").html(tr_str);
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
<div id="addAttractionMapImage" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Attraction Map Image form</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addAttractionMapImageform" id="addAttractionMapImageform" role="form" method="POST" enctype= "multipart/form-data">
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
                            <label for="exampleInputStatus">Attraction Name</label>
                            <select name="common_id" id="commonname" class="form-control">
                                <option value=""> -- Select One --</option>
                                @foreach ($common_id as $common)
                                    <option value="{{ $common->id }}">{{ $common->attraction_name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" value="attraction" name="type">
                        </div> 
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addAttractionMapImageSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->c