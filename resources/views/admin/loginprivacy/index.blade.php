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
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addPrivacy" tabindex="">ADD NEW</a>
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
                    <table class="table table-hover" id="loginPrivacyeData">
                      <thead>
                        <tr>
                            <th>@sortablelink('Title')</th>
                            <th>@sortablelink('Description')</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                            <td>{{$value->title}}</td>
                            <td>{{$value->value}}</td>
                            <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editPrivacy{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a>
                                <a class="delete" onclick="return confirm('Are you sure you want to delete this Privacy?')" href="{{route('privacy.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editPrivacy{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Privacy</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="editPrivacy" id="editPrivacyform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="exampleInputName">Title</label>
                                                    <input type="text" required class="form-control title"  id="title" name="title" value="{{$value->title}}" placeholder="title">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="title-error"></strong>
                                                    </span>
                                                </div>
                                                {{-- <div class="form-group col-md-6">
                                                    <label for="exampleInputName">Description</label>
                                                    <input type="text" required class="form-control ckeditor"  id="value{{$value->id}}" name="value" value="{{$value->value}}">
                                                    <span class="text-danger">
                                                        <strong class="value-error"></strong>
                                                    </span>
                                                </div> --}}
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-12"> 
                                                    <textarea class="description ckeditor" id="description{{$value->id}}" name="description">{{$value->value}}</textarea>
                                                    <span class="text-danger">
                                                        <strong class="description-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                        <button type="button" class="btn btn-primary mr-2 editPrivacySubmit" data-id="{{$value->id}}" id="editPrivacySubmit">Submit</button>
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
    setTimeout(function(){
            $("h4.mess").remove();
        }, 5000 ); 
    
    $(document).on('click','.editPrivacySubmit',function(e){
       
        var id = $(this).data('id');
        var formData = new FormData($("#editPrivacyform"+id)[0]);
            $( '.title-error' ).html( "" );
            $( '.value-error' ).html( "" );

            var message = CKEDITOR.instances['description'+id].getData();
            formData.append('value',message);
            
        var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('loginprivacy.update') }}",
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
                    if(result.errors.description){
                        $( '.description-error' ).html( result.errors.description[0] );
                    }                  
                }
               
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editPrivacy'+id).modal('hide');
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
        
$('#addPrivacySubmit').click(function(e){
        var formData = new FormData($("#addPrivacyform")[0]);
        $( '#title-error' ).html( "" );
        $( '#value-error' ).html( "" );
        var message = CKEDITOR.instances['description'].getData();

        formData.append('value',message);
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addLoginPrivacy') }}",
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
                    if(result.errors.description){
                        $( '#description-error' ).html( result.errors.description[0] );
                    }
                }
                if(result.status == true)
                {
                    var data = result.data;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $('#addPrivacy').modal('hide');
                         window.location.reload(); 
                    }, 3000);
                    
                    var findnorecord = $('#loginPrivacyeData tr.norecord').length;
                    if(findnorecord > 0)
                    {
                        $('#loginPrivacyeData tr.norecord').remove();
                    }
                   var deleteurl = '{{ route("loginprivacy.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);
                    var tr_str = "<tr>"+

                    "<td>"+data.title+"</td>" +
                    "<td>"+data.value+"</td>" +
                    "</tr>";
                    console.log(tr_str);
                    $("#loginPrivacyeData tbody").prepend(tr_str);
                    $("#addPrivacyform")[0].reset();
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
<div id="addPrivacy" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Privacy</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addPrivacy" id="addPrivacyform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="exampleInputName"> Title </label>
                            <input type="text" required class="form-control" Re id="title" name="title" placeholder="Title">
                            <span class="text-danger">
                                <strong id="title-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12"> 
                            <textarea class="description ckeditor" id="description" name="description"></textarea>
                            <span class="text-danger">
                                <strong id="description-error"></strong>
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addPrivacySubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->