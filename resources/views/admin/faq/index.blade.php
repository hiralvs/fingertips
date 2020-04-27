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
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addFaq" tabindex="" style="">
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
                  <h4 class="card-title" style="float:left">{{$title}}</h4>
                  <div class="box-header ">
                        @if (session()->has('success'))
                        <h4 class="mess"  style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 class="mess"  style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="privacyData">
                      <thead>
                        <tr>
                            <th>@sortablelink('unique_id','Id')</th>
                            <th>@sortablelink('category')</th>
                            <th>@sortablelink('title')</th>
                            <th>@sortablelink('description')</th>
                            <th>@sortablelink('created_by', 'Created By')</th>
                            <th>@sortablelink('created_at','Created At')</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>   
                            <td>{{$value->unique_id}}</td>
                            <td>{{$value->category}}</td>
                            <td>{{$value->title}}</td>
                            <td>{{$value->description}}</td>
                            <td>{{$value->created_by}}</td>
                            <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-target="#editFaq{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Faq?')" href="{{route('faq.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editFaq{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Brand</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addfaqform" id="editfaqform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                                @csrf
                                            <div class="row">
                                                <div class="form-group col-md-6 title">
                                                    <label for="exampleInputName"> Category </label>
                                                    <input type="text" class="form-control " required id="category" value="{{$value->category}}" name="category" placeholder="Category">
                                                    <span class="text-danger">
                                                        <strong class="category-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-6 title">
                                                    <label for="exampleInputName"> Title </label>
                                                    <input type="text" class="form-control " required id="title" value="{{$value->title}}" name="title" placeholder="Title">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="title-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-12"> 
                                                     <textarea class="form-control ckeditor" id="description{{$value->id}}" name="desc">{{$value->description}}</textarea>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editFaqSubmit" data-id="{{$value->id}}" id="editFaqSubmit">Submit</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- edit /.modal -->
                        
                        @endforeach
                       
                        @else
                        <tr>
                        <td colspan="10">No Records Found</td>
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

<script>
$(document).ready(function(){
    setTimeout(function(){
            $("h4.mess").remove();
        }, 5000 ); 
    $('.editFaqSubmit').click(function(e){
        var id = $(this).data('id');
        var formData = new FormData($("#editfaqform"+id)[0]);
        $( '.title-error' ).html( "" );
        $( '.category-error' ).html( "" );
        var message = CKEDITOR.instances['description'+id].getData();
         formData.append('description',message);
            var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('faq.update') }}",
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

                    if(result.errors.category){
                        $( '.category-error' ).html( result.errors.category[0] );
                    }
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setInterval(function(){ 
                        $('#editFaq'+id).modal('hide');
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

$('#addFaqSubmit').click(function(e){
        var formData = new FormData($("#addFaqform")[0]);
        $( '#title-error' ).html( "" );
        $( '#category-error' ).html( "" );
        var message = CKEDITOR.instances['desc'].getData();

        formData.append('description',message);
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addFaq') }}",
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
                    if(result.errors.category)
                    {
                        $( '#category-error' ).html( result.errors.category[0] );
                    }
                }
                if(result.status == true)
                {
                    var data = result.data;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addFaqform")[0].reset();
                        $('#addFaq').modal('hide');
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
    }); 
</script>
@endsection
<!-- Modal HTML Markup -->
<div id="addFaq" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Faq</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addFaq" id="addFaqform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="exampleInputName"> Category </label>
                            <input type="text" required class="form-control" id="category" name="category" placeholder="Category">
                            <span class="text-danger">
                                <strong id="category-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleInputName"> Title </label>
                            <input type="text" required class="form-control" id="title" name="title" placeholder="Title">
                            <span class="text-danger">
                                <strong id="title-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12"> 
                            <textarea class="description ckeditor" id="desc" name="desc"></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addFaqSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
