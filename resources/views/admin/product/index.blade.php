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
                    <!-- <a id="export14" class="waves-effect waves-light btn btn_box_shadow exportAccount element" href="{{route('export_excel.excel')}}" tabindex=""     style="background-color:#454d56 !important;"> -->
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
                            <th>@sortablelink('sku_id')</th>
                            <th>Image</th>
                            <th>@sortablelink('name')</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>@sortablelink('created_at','Created on')</th>
                            <!-- <th>@sortablelink('created_at','Created on',['filter' => 'active, visible'], ['class' => 'btn btn-block', 'rel' => 'nofollow'])</th> -->
                            <th>Action</th>
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
                        function fnExcelReport()
                        {

                        var tT = new XMLSerializer().serializeToString(document.querySelector('.table-responsive')); //Serialised table
                            var tF = 'report.xls'; //Filename
                            var tB = new Blob([tT]); //Blub

                            if(window.navigator.msSaveOrOpenBlob){
                                //Store Blob in IE
                                window.navigator.msSaveOrOpenBlob(tB, tF)
                            }
                            else{
                                //Store Blob in others
                                var tA = document.body.appendChild(document.createElement('a'));
                                tA.href = URL.createObjectURL(tB);
                                tA.download = tF;
                                tA.style.display = 'none';
                                tA.click();
                                tA.parentNode.removeChild(tA)
                            }
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
                    
                    var product_image = stock = category_id = status = '';
                    if(data.product_image != null)
                    {
                        var imageurl = "{{asset('public/upload/products')}}";
                        product_image = "<img src="+imageurl+"/"+data.product_image+">"  ;
                    }
                    if(data.stock != null)
                    {
                        stock = data.stock;
                    }
                    if(data.category_id != null)
                    {
                        category_id = data.category_id;
                    }
                    if(data.status == 'active')
                    {
                        status = 'Active';
                    }
                    else if(data.status == 'pending')
                    {
                        status = 'Pending';
                    }
                    else
                    {
                        status = 'Inactive';
                    }
                    if(data.created_at)
                    {
                        var cdate = date(data.created_at);
                        //cdate = cdate.replace(':date', data.created_at);
                    }
                    var deleteurl = '{{ route("product.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);

                    var tr_str = "<tr>"+
                    "<td>"+data.unique_id+"</td>" +
                    "<td>"+data.sku_id+"</td>" +
                    "<td>"+product_image+"</td>" +
                    "<td>"+data.name+"</td>" +
                    "<td>"+data.category_name+"</td>" +
                    "<td>"+data.price+"</td>" +
                    "<td>"+data.stock+"</td>" +
                    "<td>"+status+"</td>" +
                    "<td>"+cdate+"</td>" +
                    "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editProduct'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this Product?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    $("#productData tbody").html(tr_str);
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
    $('thead tr th').last().remove();
    var tT = new XMLSerializer().serializeToString(document.querySelector('#productData')); //Serialised table
    var tF = 'report.xls'; //Filename
    var tB = new Blob([tT]); //Blub
    if(window.navigator.msSaveOrOpenBlob){
        //Store Blob in IE
        window.navigator.msSaveOrOpenBlob(tB, tF)
    }
    else{
        //Store Blob in others
        var tA = document.body.appendChild(document.createElement('a'));
        tA.href = URL.createObjectURL(tB);
        tA.download = tF;
        tA.style.display = 'none';
        tA.click();
        tA.parentNode.removeChild(tA)
    }

    $('thead tr').last().append('<th>Action</th>');
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
