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
                    <a id="addnew15" class="waves-effect waves-light btn btn_box_shadow btn element" data-toggle="modal" data-target="#addCategory" tabindex="" style="">
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
                        <h4 style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="categorytableData">
                      <thead>
                        <tr>
                          <th>@sortablelink('category_name','Category')</th>
                          <th>Photo</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                          <td>{{$value->category_name}}</td>
                          <td>
                            @if($value->cat_image!= null)
                                <img src="{{asset('public/upload/category')}}{{'/'.$value->cat_image}}" alt="">
                            @else

                            @endif
                            </td>
                          <td><a class="edit open_modal" data-toggle="modal" data-target="#editCategory{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Category?')" href="{{route('category.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editCategory{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Category</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="editCategory" id="editcategoryform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="exampleInputName">Category Name</label>
                                                <input type="text" class="form-control" required id="category_name" value="{{$value->category_name}}" name="category_name" placeholder="Name">
                                                <input type="hidden" name="id" value="{{$value->id}}">
                                                <input type="hidden" name="type" value="product">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="exampleSelectPhoto">Photo</label>
                                                <input type="file" name="cat_image" class="file-upload-default">
                                                <div class="input-group col-xs-12">
                                                    <input type="text" value="{{ $value->cat_image != null ? $value->cat_image : ''}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                    <span class="input-group-append">
                                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                    </span>
                                                </div>
                                            </div>                                        
                                        </div>
                                        <button type="button" class="btn btn-primary mr-2 editCategorySubmit" data-id="{{$value->id}}" id="editCategorySubmit">Submit</button>
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
    $(document).on('click','.editCategorySubmit',function(e){
        var id = $(this).data('id');
        var formData = new FormData($("#editcategoryform"+id)[0]);
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('category.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
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

        $('#addCategorySubmit').click(function(e){
        var formData = new FormData($("#addCategoryform")[0]);
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addCategory') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $('#addCategory').modal('hide');
                    }, 3000);
                    
                    var findnorecord = $('#categorytableData tr.norecord').length;
                    if(findnorecord > 0)
                    {
                        $('#categorytableData tr.norecord').remove();
                    }
                    
                    var catgorypic = '';
                    if(data.cat_image != null)
                    {
                        catgorypic = data.cat_image;
                    }
                    var deleteurl = '{{ route("category.delete", ":id") }}';
                    var imageurl = "{{asset('public/upload/category/')}}";
                    deleteurl = deleteurl.replace(':id', data.id);
                    var tr_str = "<tr>"+
                    "<td>"+data.category_name+"</td>" +
                    "<td><img src="+imageurl+"/"+catgorypic+"></td>" +
                    "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editCategory'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this Category?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    $("#categorytableData tbody").prepend(tr_str);
                    $("#categorytableData tbody").append('<div id="editCategory'+data.id+'" class="modal fade"><div class="modal-dialog  modal-xl" role="document"><div class="modal-content"><div class="modal-header"><h1 class="modal-title">Edit Category</h1></div><div class="modal-body"><p class="statusMsg"></p><form name="editCategory" id="editcategoryform'+data.id+'" role="form" method="POST" enctype= "multipart/form-data">@csrf<div class="row"><div class="form-group col-md-4"><label for="exampleInputName">Category Name</label><input type="text" class="form-control" required id="category_name" value="'+data.category_name+'" name="category_name" placeholder="Name"><input type="hidden" name="id" value="'+data.id+'"><input type="hidden" name="type" value="product"></div><div class="form-group col-md-4"><label for="exampleSelectPhoto">Photo</label><input type="file" name="cat_image" class="file-upload-default"><div class="input-group col-xs-12"><input type="text" value="'+catgorypic+'" class="form-control file-upload-info" disabled placeholder="Upload Image"><span class="input-group-append"><button class="file-upload-browse btn btn-primary" type="button">Upload</button></span></div></div></div><button type="button" class="btn btn-primary mr-2 editCategorySubmit" data-id="'+data.id+'" id="editCategorySubmit">Submit</button><button type="button" class="btn btn-light" data-dismiss="modal">Close</button></form></div></div></div></div>');
                    $("#addCategoryform")[0].reset();
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
<div id="addCategory" class="modal fade">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Category</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addCategoryform" id="addCategoryform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="exampleInputName">Category Name</label>
                        <input type="text" required class="form-control" id="category_name" name="category_name" placeholder="Name">
                        <input type="hidden" class="form-control" id="type" name="type" value='product'>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="exampleSelectPhoto">Photo</label>
                        <input type="file" name="cat_image" class="file-upload-default">
                        <div class="input-group col-xs-12">
                            <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                            <span class="input-group-append">
                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                            </span>
                        </div>
                    </div>                
                </div>
                <button type="button" class="btn btn-primary mr-2" id="addCategorySubmit">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->