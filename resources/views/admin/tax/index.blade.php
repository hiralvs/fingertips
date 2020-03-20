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
                    <a id="addnew15" class="waves-effect waves-light btn btn_box_shadow btn element" data-toggle="modal" data-target="#addTax" tabindex="" style="">
                        ADD NEW
                    </a>
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
                    <table class="table table-hover" id="taxtableData">
                      <thead>
                        <tr>
                          <th>@sortablelink('Tax Percentage')</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                            <td>{{$value->value}}</td>
                            <td><a class="edit open_modal" data-toggle="modal" data-target="#editTax{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Tax?')" href="{{route('tax.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                         <!-- Edit Modal HTML Markup -->
                        <div id="editTax{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Tax</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addbrandform" id="edittaxform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                                @csrf
                                            <div class="row">
                                                <div class="form-group col-md-6 title">
                                                    <label for="exampleInputName"> Tax Percentage </label>
                                                    <input type="text" class="form-control value" required id="value" value="{{$value->value}}" name="value" placeholder="Title">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="value-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editTaxSubmit" data-id="{{$value->id}}" id="editPrivacySubmit">Submit</button>
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

    $('.editTaxSubmit').click(function(e){
        var id = $(this).data('id');
        var formData = new FormData($("#edittaxform"+id)[0]);
        $( '.value-error' ).html( "" );
            var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('tax.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.value){
                        $( '.value-error' ).html( result.errors.value[0] );
                    }
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setInterval(function(){ 
                        $('#editTax'+id).modal('hide');
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

$('#addTaxSubmit').click(function(e){
        var formData = new FormData($("#addTaxform")[0]);
        
        $( '#value-error' ).html( "" );
        // var message = CKEDITOR.instances['value'].getData();

        // formData.append('value',message);
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addTax') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.value){
                        $( '#value-error' ).html( result.errors.value[0] );
                    }
                }
                if(result.status == true)
                {
                    var data = result.data;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $('#addTax').modal('hide');
                         window.location.reload(); 
                    }, 3000);
                    
                    var findnorecord = $('#taxtableData tr.norecord').length;
                    if(findnorecord > 0)
                    {
                        $('#taxtableData tr.norecord').remove();
                    }
                   var deleteurl = '{{ route("tax.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);
                    var tr_str = "<tr>"+
                    "<td>"+data.value+"</td>" +
                    "</tr>";
                    console.log(tr_str);
                    $("#taxtableData tbody").prepend(tr_str);
                    $("#addTaxform")[0].reset();
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
<div id="addTax" class="modal fade">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Tax</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addTaxform" id="addTaxform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="exampleInputName">Tax Percentage</label>
                            <input type="text" required class="form-control" Re id="value" name="Value" placeholder="Tax Percentage">
                            <span class="text-danger">
                                <strong id="value-error"></strong>
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addTaxSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
