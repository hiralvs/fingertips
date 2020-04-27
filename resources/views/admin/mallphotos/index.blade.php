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
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addPhotos" tabindex="">ADD NEW</a>
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
                        <h4 class="mess"  style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 class="mess"  style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="notificationtableData">
                      <thead>
                        <tr>
                            <th>@sortablelink('Photo Id')</th>
                            <th>@sortablelink('Photo')</th>
                            <th>@sortablelink('mallname','Shops and Malls')</th>
                            <th>@sortablelink('created_at','Created On')</th>
                            <th>@sortablelink('Created By')</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                            <td>{{$value->unique_id}}</td>
                            <td><img src="{{asset('public/upload/photos/')}}/{{$value->image_name}}" alt=""></td>
                            <td>{{$value->mallname}}</td>
                            <td>{{date("d F Y",strtotime($value->created_at))}}</td> 
                            <td>{{$value->created_by}}</td>
                            <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editPhotos{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a>
                                <a class="delete" onclick="return confirm('Are you sure you want to delete this Photos?')" href="{{route('mallphotos.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editPhotos{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Photos</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addPhotos" id="editPhotosform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Photo</label>
                                                    <input type="file" name="image_name" class="file-upload-default">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" value="{{$value->image_name}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                        <span class="input-group-append">
                                                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Malls and Shops</label>
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <select name="mallname" id="mallname" class="form-control common_id">
                                                        <option value=""> -- Select One --</option>
                                                        @if(!empty($common_id) && $common_id->count() > 0)
                                                            @foreach ($common_id as $key => $pd)
                                                                  <option value="{{$pd->id}}" {{ $value->common_id == $pd->id ? 'selected' : ''}} >{{$pd->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <input type="hidden" value="malls" name="type">

                                                    <span class="text-danger">
                                                        <strong class="common_id-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                        <button type="button" class="btn btn-primary mr-2 editPhotosSubmit" data-id="{{$value->id}}" id="editAreaSubmit">Submit</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- edit /.modal -->
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
    setTimeout(function(){
           $("h4.mess").remove();
        }, 5000 );
    $(document).on('click','.editPhotosSubmit',function(e){
       
        var id = $(this).data('id');
        var formData = new FormData($("#editPhotosform"+id)[0]);
            $( '.common_id-error' ).html( "" ); 

        var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('mallphotos.update') }}",
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
                        $('#editPhotos'+id).modal('hide');
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
    $('#addPhotosSubmit').click(function(e){
            var formData = new FormData($("#addPhotosform")[0]);
            $( '#image_name-error' ).html( "" );
            $( '#common_id-error' ).html( "" );

            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addMallPhotos') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.image_name){
                        $( '#image_name-error' ).html( result.errors.image_name[0] );
                    }
                    if(result.errors.mallname){
                        $( '#common_id-error' ).html( result.errors.mallname[0] );
                    }
                }
                if(result.status == true)
                {
                    var data = result.data.photos;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addPhotosform")[0].reset();
                        $('#addPhotos').modal('hide');
                        window.location.reload();
                    }, 3000);
                    
                    $("#addPhotosform")[0].reset();
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
<div id="addPhotos" class="modal fade">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Photos</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addPhotosform" id="addPhotosform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="exampleSelectPhoto">Photo</label>
                            <input type="file" name="image_name" id="image_name" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                            <span class="text-danger">
                                <strong id="image_name-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleInputStatus">Shops and Malls</label>
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
                    <button type="button" class="btn btn-primary mr-2" id="addPhotosSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->