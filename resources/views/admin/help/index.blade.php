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
                </div>   
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addHelp" tabindex="">ADD NEW</a>
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
                        <h4 style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="helpData">
                      <thead>
                        <tr>
                          <th>@sortablelink('Address')</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                            <td>{{$value->address}}</td>
                            <td><a class="edit open_modal" data-toggle="modal" data-target="#editHelp{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                            <a class="delete" onclick="return confirm('Are you sure you want to delete this Help?')" href="{{route('help.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editHelp{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Help</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addbrandform" id="edithelpform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                                @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Address</label>
                                                    <input type="text" required class="form-control address" Re id="address" value="{{$value->address}}" name="address">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="address-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Mobile</label>
                                                    <input type="text" required class="form-control contact" Re id="contact" value="{{$value->contact}}" name="contact">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="contact-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Email</label>
                                                    <input type="text" required class="form-control email" Re id="email" value="{{$value->email}}" name="email">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="email-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Website</label>
                                                    <input type="text" required class="form-control url" Re id="url" value="{{$value->url}}" name="url">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="url-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editHelpSubmit" data-id="{{$value->id}}" id="editFloorSubmit">Submit</button>
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

    $('.editHelpSubmit').click(function(e){
        $( '.address-error' ).html( "" );
        $( '.contact-error' ).html( "" );
        $( '.email-error' ).html( "" );
        $( '.url-error' ).html( "" );
        var id = $(this).data('id');
        var formData = new FormData($("#edithelpform"+id)[0]);
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('help.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.address){
                        $( '.address-error' ).html( result.errors.address[0] );
                    }                   
                    if(result.errors.contact){
                        $( '.contact-error' ).html( result.errors.contact[0] );
                    }                   
                    if(result.errors.email){
                        $( '.email-error' ).html( result.errors.email[0] );
                    }                   
                    if(result.errors.url){
                        $( '.url-error' ).html( result.errors.url[0] );
                    }                   
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setInterval(function(){ 
                        $('#edithelp'+id).modal('hide');
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
        $('#addHelpSubmit').click(function(e){
            $( '#address-error' ).html( "" );
            $( '#contact-error' ).html( "" );
            $( '#email-error' ).html( "" );
            $( '#url-error' ).html( "" );
            var id = $(this).data('id');
            var formData = new FormData($("#addHelpform")[0]);
           
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addHelp') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.address){
                        $( '#address-error' ).html( result.errors.address[0] );
                    }                   
                    if(result.errors.contact){
                        $( '#contact-error' ).html( result.errors.contact[0] );
                    }                   
                    if(result.errors.email){
                        $( '#email-error' ).html( result.errors.email[0] );
                    }                   
                    if(result.errors.url){
                        $( '#url-error' ).html( result.errors.url[0] );
                    }                   
                }
                if(result.status == true)
                {
                    var data = result.data.floor;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addHelpform")[0].reset();
                        $('#addHelp').modal('hide');
                        window.location.reload();
                    }, 3000);
                    
                    $("#addHelpform")[0].reset();
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
<div id="addHelp" class="modal fade">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Floor Setting</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addHelpform" id="addHelpform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Address</label>
                            <input type="text" required class="form-control" Re id="address" name="address" placeholder="Address">
                            <span class="text-danger">
                                <strong id="address-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Mobile</label>
                            <input type="text" required class="form-control" Re id="contact" name="contact" placeholder="Mobile">
                            <span class="text-danger">
                                <strong id="contact-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Email</label>
                            <input type="text" required class="form-control" Re id="email" name="email" placeholder="Email">
                            <span class="text-danger">
                                <strong id="email-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Website</label>
                            <input type="text" required class="form-control" Re id="url" name="url" placeholder="Website">
                            <span class="text-danger">
                                <strong id="url-error"></strong>
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addHelpSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
