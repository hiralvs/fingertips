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
                    <a id="clear16" class="btn btn-secondary" href="{{route('products')}}" tabindex="" >CLEAR</a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addProduct" tabindex="">
                        ADD NEW
                    </a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                <a id="export14" class="btn btn-secondary" onclick="fnExcelReport()" tabindex="">
                        EXPORT
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
                    <table class="table table-hover" id="productData">
                      <thead>
                        <tr>
                            <th>@sortablelink('id')</th>
                            <th>@sortablelink('sku_id','Sku Id')</th>
                            <th>Image</th>
                            <th>@sortablelink('name')</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>@sortablelink('created_at','Created on')</th>
                            <!-- <th>@sortablelink('created_at','Created on',['filter' => 'active, visible'], ['class' => 'btn btn-block', 'rel' => 'nofollow'])</th> -->
                            <th>Action</th>
                            <th>Variant</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                            <td>{{$value->unique_id}}</td>
                            <td>{{$value->sku_id}}</td>
                            <td>
                                @if($value->product_image!= null)
                                    <img src="{{asset('public/upload/products')}}{{'/'.$value->product_image}}" alt="">
                                @else
    
                                @endif
                                </td>
                          <td>{{$value->name}}</td>
                          <td>{{$value->category_name}}</td>
                          <td>{{$value->price}}</td>
                          <td>{{$value->stock}}</td>
                          
                          <td> @if($value->status == 'inactive') 
                                Inactive
                            @elseif($value->status == 'active')
                                Active
                            @else
                                Pending
                            @endif
                            </td>
                          <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-target="#editProduct{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Product?')" href="{{route('product.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                          <td>
                            <label class="toggle-switch">
                                <input type="checkbox" data-id="{{ $value->id }}"  name="vairant" class="vairant" value="{{ $value->productvariantcount >= 1 ? '1' : '0' }}" {{ $value->productvariantcount >= 1 ? 'checked' : '' }}>
                                <span class="toggle-slider round"></span>
                              </label>
                              <a href="{{route('products_variant', $value->id)}}" style="{{ $value->productvariantcount >= 1 ? 'display: block;' : 'display: none' }}"  id="variantLink{{ $value->id }}" >variant</a>
                          </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editProduct{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Product</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addproductform" id="editproductform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                                @csrf
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputName">SKU ID</label>
                                                    <input type="text" required class="form-control" id="skuid" value="{{$value->sku_id}}"  name="skuid" placeholder="SKU ID">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="skuid-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="exampleSelectPhoto">Product Logo</label>
                                                    <input type="file" name="product_image" class="file-upload-default">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" value="{{ $value->product_image != null ? $value->product_image : ''}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                        <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputName">Name</label>
                                                    <input type="text" required class="form-control" id="name" value="{{$value->name}}"  name="name" placeholder="Name">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="name-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputStatus">Category</label>
                                                    <select name="category[]" id="category" multiple class="form-control category_id">
                                                        <option value=""> -- Select One --</option>
                                                        @foreach ($category as $cat)
                                                            <option value="{{ $cat->id }}"  {{ in_array($cat->id,explode(",",$value->category_id))  ? 'selected' : ''}}>{{ $cat->category_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="category-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputStatus">Brands</label>
                                                    <select class="form-control"  name="brand" id="brand">
                                                        @if(!empty($brands) && $brands->count() > 0)
                                                            @foreach($brands as $key => $brandval)
                                                            <option value="{{$brandval->id}}" {{ $value->brand_id ==$brandval->id ? 'selected' : ''}}>{{$brandval->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="brand-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputName">Price</label>
                                                    <input type="text" required class="form-control" id="price" name="price" value="{{$value->price}}" placeholder="Price">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="price-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputName">Stock</label>
                                                    <input type="text" required class="form-control" id="stock" value="{{$value->stock}}" name="stock" placeholder="stock">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputName">Size and Fit</label>
                                                    <input type="text" required class="form-control" value="{{$value->sizefit}}" id="sizeandfit" name="sizeandfit" placeholder="sizeandfit">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputName">Type</label>
                                                    <input type="text" required class="form-control" id="type" value="{{$value->type}}"  name="type" placeholder="type">
                                                </div> 
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputStatus">Color</label>
                                                    <input type="text" required class="form-control" id="color" name="color" value="{{$value->color}}" placeholder="color">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="exampleInputStatus">Status</label>
                                                    <select class="form-control" id="status" name="status">
                                                        <option value="" >--Select--</option>
                                                        <option value="pending"  {{ $value->status =='pending' ? 'selected' : ''}}>Pending</option>
                                                        <option value="active" {{ $value->status =='active' ? 'selected' : ''}}>Active</option>
                                                        <option value="inactive" {{ $value->status =='inactive' ? 'selected' : ''}}>Inactive</option>
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="status-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-12"> 
                                                    <textarea class="description ckeditor" id="description{{$value->id}}" name="desc">{{$value->description}}</textarea>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editProductSubmit" data-id="{{$value->id}}" id="editProductSubmit">Save</button>
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
<script src="{{asset('public/js/file-upload.js')}}" ></script>
<script>
$(document).ready(function(){
    
    $(document).on("click",".vairant",function() {
        var status = $(this).prop('checked') == true ? 1 : 0; 
        var pid = $(this).data('id'); 
        if(status == 1)
        {
            $("#variantLink"+pid).css("display","block");
        }
        else if(status == 0)
        {
            $("#variantLink"+pid).css("display","none");
        }
    });
    $('.category_id').multiselect({
            columns: 1,
            placeholder: 'Select Category'
        });
    $('.editProductSubmit').click(function(e){
        var id = $(this).data('id');
        var formData = new FormData($("#editproductform"+id)[0]);
        var message = CKEDITOR.instances['description'+id].getData();

        formData.append('description',message);
        $( '#skuid-error'+id ).html( "" );
        $( '#brand-error'+id ).html( "" );
        $( '#category-error'+id ).html( "" );
        $( '#name-error'+id ).html( "" );
        $( '#price-error'+id ).html( "" );
        $( '#status-error'+id ).html( "" );
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('product.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                    if(result.errors) {
                        $(".statusMsg").hide();
                        if(result.errors.skuid){
                            $( '#skuid-error'+id ).html( result.errors.skuid[0] );
                        }
                        if(result.errors.brand){
                            $( '#brand-error'+id).html( result.errors.brand[0] );
                        }
                        if(result.errors.category){
                            $( '#category-error'+id ).html( result.errors.category[0] );
                        }
                        if(result.errors.name){
                            $( '#name-error'+id).html( result.errors.name[0] );
                        }
                        if(result.errors.price){
                            $( '#price-error'+id).html( result.errors.price[0] );
                        }
                        if(result.errors.status){
                            $( '#status-error'+id).html( result.errors.status[0] );
                        }
                    }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setInterval(function(){ 
                        $('#editProduct'+id).modal('hide');
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
   
    $('#addProductSubmit').click(function(e){
        var formData = new FormData($("#addproductform")[0]);
        var message = CKEDITOR.instances['desc'].getData();
        $( '#skuid-error' ).html( "" );
        $( '#brand-error' ).html( "" );
        $( '#category-error' ).html( "" );
        $( '#name-error' ).html( "" );
        $( '#price-error' ).html( "" );
        $( '#status-error' ).html( "" );
        

            formData.append('description',message);
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addproduct') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result)
                {
                    if(result.errors) {
                        $(".statusMsg").hide();
                        if(result.errors.skuid){
                            $( '#skuid-error' ).html( result.errors.skuid[0] );
                        }
                        if(result.errors.brand){
                            $( '#brand-error' ).html( result.errors.brand[0] );
                        }
                        if(result.errors.category){
                            $( '#category-error' ).html( result.errors.category[0] );
                        }
                        if(result.errors.name){
                            $( '#name-error' ).html( result.errors.name[0] );
                        }
                        
                        if(result.errors.price){
                            $( '#price-error' ).html( result.errors.price[0] );
                        }
                        if(result.errors.status){
                            $( '#status-error' ).html( result.errors.status[0] );
                        }
                    }
                    if(result.status == true)
                    {
                        var data = result.data;
                        $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                        setInterval(function(){ 
                            $('.statusMsg').html('');
                            $("#addproductform")[0].reset();
                            $('#addProduct').modal('hide');
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
        $(document).on('click','#search',function(){ 
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });       
        $.ajax({
                url: "{{route('product.productsearch')}}",
                method: 'post',
                data: {'search':$("#searchtext").val()},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    
                    var findnorecord = $('#productData tbody tr.norecord').length;
                    if(findnorecord > 0){
                        $('#productData tbody tr.norecord').remove();
                        }
                     $("#productData tbody").html(data);
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
        url: "{{route('productexport')}}",
        method: 'get',
        data: {'search':search},
        success: function(result){
            $(result).table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "product",
                filename: "product" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls", //do not include extension
                fileext: ".xls" // file extension
              }); 
        }
    });
}
</script>
@endsection

<!-- Modal HTML Markup -->
<div id="addProduct" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Product</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
            @if(count($errors) > 0)
                @foreach ($errors->all() as $error)
                    <p class='alert alert-danger'>{{ $error }}</p>
                @endforeach
            @endif
                <form name="addproductform" id="addproductform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    {{-- @foreach($errors as $error)
                        <li>{{$error}}</li>
                    @endforeach  --}}
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="exampleInputName">SKU ID</label>
                        <input type="text" required class="form-control" id="skuid" name="skuid" placeholder="SKU ID">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="skuid-error"></strong>
                        </span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleSelectPhoto">Product Logo</label>
                        <input type="file" name="product_image" class="file-upload-default">
                        <div class="input-group col-xs-12">
                            <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                            <span class="input-group-append">
                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleInputName">Name</label>
                        <input type="text" required class="form-control" id="name" name="name" placeholder="Name">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="name-error"></strong>
                        </span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleInputStatus">Category</label>
                        <select class="form-control category_id" multiple name="category[]" id="category">
                            <!-- <option value="">--Select--</option> -->
                            @if(!empty($category) && $category->count() > 0)
                                @foreach($category as $key => $cat)
                                <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                                @endforeach
                            @endif
                        </select>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="category-error"></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="exampleInputStatus">Brands</label>
                        <select class="form-control"  name="brand" id="brand">
                            @if(!empty($brands) && $brands->count() > 0)
                                @foreach($brands as $key => $brandval)
                                <option value="{{$brandval->id}}">{{$brandval->name}}</option>
                                @endforeach
                            @endif
                        </select>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="brand-error"></strong>
                        </span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleInputName">Price</label>
                        <input type="text" required class="form-control" id="price" name="price" placeholder="Price">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="price-error"></strong>
                        </span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleInputName">Stock</label>
                        <input type="text" required class="form-control" id="stock" name="stock" placeholder="stock">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleInputName">Size and Fit</label>
                        <input type="text" required class="form-control" id="sizeandfit" name="sizeandfit" placeholder="sizeandfit">
                    </div>
                </div>
                <div class="row">  
                    <div class="form-group col-md-3">
                    <label for="exampleInputName">Type</label>
                        <input type="text" required class="form-control" id="type" name="type" placeholder="type">
                    </div> 
                    <div class="form-group col-md-3">
                        <label for="exampleInputStatus">Color</label>
                        <input type="text" required class="form-control" id="color" name="color" placeholder="color">
                    </div>
                    <div class="form-group col-md-3">
                    <label for="exampleInputStatus">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="" >--Select--</option>
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="status-error"></strong>
                        </span>
                    </div>
                  
                </div>
                <div class="form-group col-md-12"> 
                    <textarea class="description ckeditor" id="desc" name="desc"></textarea>
                </div>
                <button type="button" class="btn btn-primary mr-2" id="addProductSubmit">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button> 
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
