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
                    <a id="clear16" class="btn btn-secondary" href="{{route('sponsors')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addSponsors" tabindex="">ADD NEW</a>
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
                  <h4 class="card-title" style="float:left">{{$title ?? ''}}</h4>
                  <div class="box-header ">
                        @if (session()->has('success'))
                        <h4 style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="sponsorstableData">
                      <thead>
                        <tr>
                            <th>@sortablelink('Id')</th>
                            <th>Image</th>
                            <th>@sortablelink('Title')</th>
                            <th>@sortablelink('URL')</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                            <td>{{$value->id}}</td>
                            <td><img src="{{asset('public/upload/sponsors/')}}/{{$value->image}}" alt=""></td>
                            <td>{{$value->title}}</td>
                            <td>{{$value->url}}</td>
                            <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editSponsor{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a>
                                <a class="delete" onclick="return confirm('Are you sure you want to delete this Sponsers?')" href="{{route('sponsors.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                                
                            </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editSponsor{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Trending</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addSponsor" id="editSponsorform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
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
                                                <div class="form-group col-md-4 url" id="url{{$value->id}}">
                                                    <label for="exampleInputName">Link</label>
                                                    <input type="text" value="{{ $value->url != null ? $value->url : ''}}" required class="form-control url" name="url">
                                                    <span class="text-danger">
                                                        <strong class="url-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                        <button type="button" class="btn btn-primary mr-2 editSponsorSubmit" data-id="{{$value->id}}" id="editSponsorSubmit">Submit</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- edit /.modal -->
                        @endforeach
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
    $('.editSponsorSubmit').click(function(e){
        var id = $(this).data('id');
        var formData = new FormData($("#editSponsorform"+id)[0]);
            $( '.title-error' ).html( "" );
            $( '.url-error' ).html( "" );
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('sponsors.update') }}",
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
                    if(result.errors.url){
                        $( '.url-error' ).html( result.errors.url[0] );
                    }
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setInterval(function(){ 
                        $('#editSponsor'+id).modal('hide');
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
        $('#addSponsorsSubmit').click(function(e){
            var formData = new FormData($("#addSponsorsform")[0]);                
            $( '#image-error' ).html( "" );
            $( '#title-error' ).html( "" );
            $( '#url-error' ).html( "" );
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addSponsors') }}",
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
                    if(result.errors.url){
                        $( '#url-error' ).html( result.errors.url[0] );
                    }
                }
                if(result.status == true)
                {
                    var data = result.data.sponsors;
                    // var propertyadmin =  result.data.propertyadmin;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addSponsorsform")[0].reset();
                        // $('#addRewardSetting').modal('hide');
                        window.location.reload();
                    }, 3000);
                    
                    $("#addSponsorsform")[0].reset();
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
                url: "{{route('sponsors.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val()},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    
                    var findnorecord = $('#sponsorstableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#sponsorstableData tr.norecord').remove();
                        }
                    
                    var id = image = title = link = '';
                    if(data.id != null)
                    {
                        id = data.id;
                    }
                    if(data.image != null)
                    {
                        image = data.image;
                    }
                    if(data.title != null)
                    {
                        title = data.title;
                    }
                    if(data.url != null)
                    {
                        url = data.url;
                    }
                    var deleteurl = '{{ route("sponsors.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);
                    var tr_str = "<tr>"+
                    "<td>"+id+"</td>" +
                    "<td>"+image+"</td>" +
                    "<td>"+title+"</td>" +
                    "<td>"+url+"</td>" +
                    // "<td>"+cdate+"</td>" +
                    "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editUser'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this User?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    console.log(tr_str);
                    $("#sponsorstableData tbody").html(tr_str);
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
<div id="addSponsors" class="modal fade">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Trending</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addSponsorsform" id="addSponsorsform" role="form" method="POST" enctype= "multipart/form-data">
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
                            <input type="text" class="form-control" id="url" name="url" placeholder="url">
                            {{-- <input type="hidden" class="form-control" id="type" name="type" value='url'> --}}
                            <span class="text-danger">
                                <strong id="url-error"></strong>
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addSponsorsSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->  