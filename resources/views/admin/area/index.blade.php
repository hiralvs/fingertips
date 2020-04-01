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
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addArea" tabindex="" style="">
                        ADD NEW
                    </a>
                        <!-- <button type="button" class="btn btn-outline-inverse-info btn-icon-text">
                            Print
                            <i class="mdi mdi-printer btn-icon-append"></i>                          
                        </button> -->
                </div>               
            </div>
        </div>
    </div>
    <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title" style="float:left">{{$title}}</h4>
                  <!-- <select name="per_page" id="per_page" style="float:right;">
                  <option value="10">10</option>
                  <option value="20">20</option>
                  <option value="30">30</option>
                  </select> -->
                  <div class="box-header ">
                        @if (session()->has('success'))
                        <h4 class="mess" style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 class="mess" style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="areatableData">
                      <thead>
                        <tr>
                          <th>@sortablelink('area_name','Area Name')</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                          <td>{{$value->area_name}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-target="#editArea{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Area?')" href="{{route('area.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editArea{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Area</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="editArea" id="editAreaform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="exampleInputName">Area Name</label>
                                                <input type="text" class="form-control" required id="area_name" value="{{$value->area_name}}" name="area_name" placeholder="Name">
                                                <span class="text-danger">
                                                    <strong class="area_name-error"></strong>
                                                </span>
                                                <input type="hidden" name="id" value="{{$value->id}}">
                                            </div>                                       
                                        </div>
                                        <button type="button" class="btn btn-primary mr-2 editAreaSubmit" data-id="{{$value->id}}" id="editAreaSubmit">Submit</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- edit /.modal -->
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

<script>

$(document).ready(function(){
    setTimeout(function(){
           $("h4.mess").remove();
        }, 5000 ); // 5 secs
    $(document).on('click','.editAreaSubmit',function(e){
        var id = $(this).data('id');
             $( '.area_name-error' ).html( "" );
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('area.update') }}",
                method: 'post',
                data: $("#editAreaform"+id).serialize(),
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.area_name){
                        $( '.area_name-error' ).html( result.errors.area_name[0] );
                    }
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editUser'+id).modal('hide');
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

        $('#addAreaSubmit').click(function(e){
            $( '#area_name-error' ).html( "" );
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addArea') }}",
                method: 'post',
                data: $("#addAreaform").serialize(),
                success: function(result){
                 if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.area_name){
                        $( '#area_name-error' ).html( result.errors.area_name[0] );
                    }
                }
                if(result.status == true)
                {
                    var data = result.data;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $('#addArea').modal('hide');
                        $("#addAreaform")[0].reset();
                        window.location.reload();
                    }, 3000);
                    
                    var findnorecord = $('#areatableData tr.norecord').length;
                    if(findnorecord > 0)
                    {
                        $('#areatableData tr.norecord').remove();
                    }
                   
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
<div id="addArea" class="modal fade">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Area</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addAreaform" id="addAreaform" role="form" method="POST">
                    @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="exampleInputName">Area Name</label>
                        <input type="text" required class="form-control" id="area_name" name="area_name" placeholder="Name">
                        <span class="text-danger">
                            <strong id="area_name-error"></strong>
                        </span>
                    </div>               
                </div>
                <button type="button" class="btn btn-primary mr-2" id="addAreaSubmit">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->