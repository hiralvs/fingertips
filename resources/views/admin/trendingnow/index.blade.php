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
                    <a id="clear16" class="btn btn-secondary" href="{{route('trending')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addTrending" tabindex="">ADD NEW</a>
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
                    <table class="table table-hover" id="trendingtableData">
                      <thead>
                        <tr>
                            <th>@sortablelink('Id')</th>
                            <th>Image</th>
                            <th>@sortablelink('title','Title')</th>
                            <th>@sortablelink('link','URL')</th>
                            <th>@sortablelink('status','Status')</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                            <td>{{$value->unique_id}}</td>
                            <td><img src="{{asset('public/upload/trending/')}}/{{$value->image}}" alt=""></td>
                            <td>{{$value->title}}</td>
                            <td>{{$value->link}}</td>
                            <td> @if($value->status == '1') 
                                    Inactive
                                @else
                                    Active
                                    @endif
                                </td>
                            <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editTrending{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a>
                                <a class="delete" onclick="return confirm('Are you sure you want to delete this Trending?')" href="{{route('trending.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editTrending{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Trending</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addTrending" id="editTrendform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Photo</label>
                                                    <input type="file" name="image" class="file-upload-default">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" value="{{$value->image}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                        <input type="hidden" name="id" value="{{$value->id}}">
                                                        <span class="input-group-append">
                                                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                        </span>
                                                    </div>
                                                </div> 
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Title</label>
                                                    <input type="text" required class="form-control title" Re id="title" value="{{$value->title}}" name="title" placeholder="Name">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong class="title-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4 url" id="link{{$value->id}}">
                                                    <label for="exampleInputName">URL</label>
                                                    <input type="text" value="{{ $value->link != null ? $value->link : ''}}" required class="form-control link" name="link">
                                                    <span class="text-danger">
                                                        <strong class="link-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputRole">Status</label>
                                                    <select class="form-control status" id="status" name="status">
                                                        <option value="">--Select--</option>
                                                        <option value="0" {{ $value->status == '0' ? 'selected' : ''}}>Active</option>
                                                        <option value="1" {{ $value->status == '1' ? 'selected' : ''}}>Inactive</option>
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong class="status-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                        <button type="button" class="btn btn-primary mr-2 editTrendingSubmit" data-id="{{$value->id}}" id="editTrendingSubmit">Submit</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- edit /.modal -->
                        @endforeach
                        @else
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
    $('.editTrendingSubmit').click(function(e){
        var id = $(this).data('id');
        var formData = new FormData($("#editTrendform"+id)[0]);
            $( '.title-error' ).html( "" );
            $( '.link-error' ).html( "" );
            $( '.status-error' ).html( "" );
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('trending.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.title){
                        $( '.title-error' ).html( result.errors.title[0] );
                    }
                    if(result.errors.link){
                        $( '.link-error' ).html( result.errors.link[0] );
                    }
                    if(result.errors.status){
                        $( '.status-error' ).html( result.errors.status[0] );
                    }
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setInterval(function(){ 
                        $('#editTrending'+id).modal('hide');
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
        $('#addTrendingSubmit').click(function(e){
            var formData = new FormData($("#addTrendingform")[0]);                
            $( '#image-error' ).html( "" );
            $( '#title-error' ).html( "" );
            $( '#link-error' ).html( "" );
            $( '#status-error' ).html( "" );
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addTrending') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.image){
                        $( '#image-error' ).html( result.errors.image[0] );
                    }
                    if(result.errors.title){
                        $( '#title-error' ).html( result.errors.title[0] );
                    }
                    if(result.errors.link){
                        $( '#link-error' ).html( result.errors.link[0] );
                    }
                    if(result.errors.status){
                        $( '#status-error' ).html( result.errors.status[0] );
                    }
                }
                if(result.status == true)
                {
                    var data = result.data.trending_now;
                    // var propertyadmin =  result.data.propertyadmin;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addTrendingform")[0].reset();
                        // $('#addRewardSetting').modal('hide');
                        window.location.reload();
                    }, 3000);
                    
                    $("#addTrendingform")[0].reset();
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
                url: "{{route('trending.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val()},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    
                    var findnorecord = $('#trendingtableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#trendingtableData tr.norecord').remove();
                        }
                    $("#trendingtableData tbody").html(data);
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
        url: "{{route('trendingexport')}}",
        method: 'get',
        data: {'search':search},
        success: function(result){
            $(result).table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "trending",
                filename: "trending" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls", //do not include extension
                fileext: ".xls" // file extension
              }); 
        }
    });
}
</script>
@endsection
<!-- Modal HTML Markup -->
<div id="addTrending" class="modal fade">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Trending</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addTrendingform" id="addTrendingform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleSelectPhoto">Photo</label>
                            <input type="file" name="image" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" id="image" disabled placeholder="Upload Image">
                                <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                                <span class="text-danger">
                                    <strong id="image-error"></strong>
                                </span>
                        </div> 
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Title</label>
                            <input type="text" required class="form-control"  id="title" name="title" placeholder="title">
                            <span class="text-danger">
                                <strong id="title-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4" id="url">
                            <label for="exampleInputName url">URL</label>
                            <input type="text" class="form-control" id="link" name="link" placeholder="url">
                            {{-- <input type="hidden" class="form-control" id="type" name="type" value='url'> --}}
                            <span class="text-danger">
                                <strong id="link-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4 status">
                            <label for="exampleInputStatus status">Status</label>
                            <select class="form-control type" id="status" name="status">
                                <option value="" selected="">Status</option>
                                <option value="0">Active</option>
                                <option value="1">Inactive</option>
                            </select>
                            <span class="text-danger">
                                <strong id="status-error"></strong>
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addTrendingSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->