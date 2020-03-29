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
                    <a id="clear16" class="btn btn-secondary" href="{{route('products_variant',$id)}}" tabindex="" >CLEAR</a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addProductVariant" tabindex="">
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
                        <h4 class="mess" style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 class="mess" style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="productData">
                      <thead>
                        <tr>
                            <th>@sortablelink('id')</th>
                            <th>Image</th>
                            <th>@sortablelink('variant_name','Variant Name')</th>
                            <th>Price</th>
                            <th>Stock</th>
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
                            <td>
                                @if($value->variant_image!= null)
                                    <img src="{{asset('public/upload/products')}}{{'/'.$value->variant_image}}" alt="">
                                @else
    
                                @endif
                                </td>
                          <td>{{$value->variant_name}}</td>
                          <td>{{$value->price}}</td>
                          <td>{{$value->stock}}</td>
                          <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-target="#editProductVariant{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Product Variant?')" href="{{route('productvariant.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editProductVariant{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Product Variant</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addroductvariantform" id="editproductvarientform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                                @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Product Variant Image</label>
                                                    <input type="file" name="variant_image" class="file-upload-default">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" value="{{ $value->variant_image != null ? $value->variant_image : ''}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                        <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Name</label>
                                                    <input type="text" required class="form-control" id="name" value="{{$value->variant_name}}"  name="variant_name" placeholder="variant name">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <input type="hidden" name="pid" value="{{$id}}">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Price</label>
                                                    <input type="text" required class="form-control" id="price" name="price" value="{{$value->price}}" placeholder="Price">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="price-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">   
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Stock</label>
                                                    <input type="text" required class="form-control" id="stock" value="{{$value->stock}}" name="stock" placeholder="stock">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">size</label>
                                                    <select class="form-control"  name="size" id="size">
                                                    <option value="S">Small</option>
                                                    <option value="M">Medium</option>
                                                    <option value="L">Large</option>
                                                    <option value="XL">XL</option>
                                                    <option value="XXl">XXL</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editProductVariantSubmit" data-id="{{$value->id}}" id="editProductVariantSubmit">Save</button>
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
       setTimeout(function(){
           $("h4.mess").remove();
        }, 5000 );
    $(".vairant").click(function() {
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

    $('.editProductVariantSubmit').click(function(e){
        var id = $(this).data('id');
        var formData = new FormData($("#editproductvarientform"+id)[0]);
       
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('productvariant.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                    if(result.status == true)
                    {
                        $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                        setInterval(function(){ 
                            $('#editproductvarientform'+id).modal('hide');
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
   
    $('#addProductVariantSubmit').click(function(e){
        var formData = new FormData($("#addproductvariantform")[0]);
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addproductvariant') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result)
                {
                    if(result.status == true)
                    {
                        var data = result.data;
                        $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                        setInterval(function(){ 
                            $('.statusMsg').html('');
                            $("#addproductvariantform")[0].reset();
                            $('#addProductVariant').modal('hide');
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
                url: "{{route('productvariant.productsearch')}}",
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
                    
                    var variant_image = stock = category_id = status = '';
                    if(data.variant_image != null)
                    {
                        var imageurl = "{{asset('public/upload/products')}}";
                        variant_image = "<img src="+imageurl+"/"+data.variant_image+">"  ;
                    }
                    if(data.stock != null)
                    {
                        stock = data.stock;
                    }
                  
                    if(data.created_at)
                    {
                        var cdate = date(data.created_at);
                        //cdate = cdate.replace(':date', data.created_at);
                    }
                    var deleteurl = '{{ route("productvariant.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);

                    var tr_str = "<tr>"+
                    "<td>"+data.unique_id+"</td>" +
                    "<td>"+variant_image+"</td>" +
                    "<td>"+data.variant_name+"</td>" +
                    "<td>"+data.price+"</td>" +
                    "<td>"+data.stock+"</td>" +
                    "<td>"+cdate+"</td>" +
                    "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editProductVariant'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this Product Variant?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
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
<div id="addProductVariant" class="modal fade">
    <div class="modal-dialog  modal-lg" role="document">
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
                <form name="addproductvariantform" id="addproductvariantform" role="form" method="POST" enctype= "multipart/form-data">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="exampleSelectPhoto">Product Variant Image</label>
                        <input type="file" name="variant_image" class="file-upload-default">
                        <div class="input-group col-xs-12">
                            <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                            <span class="input-group-append">
                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleInputName">Variant Name</label>
                        <input type="text" required class="form-control" id="variant_name" name="variant_name" placeholder="Variant Name">
                        <input type="hidden" name="pid" value="{{$id}}">
                    </div>                    
                    <div class="form-group col-md-4">
                        <label for="exampleInputName">Stock</label>
                        <input type="text" required class="form-control" id="stock" name="stock" placeholder="stock">
                    </div> 
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="exampleInputName">Price</label>
                        <input type="text" required class="form-control" id="price" name="price" placeholder="Price">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="price-error"></strong>
                        </span>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleInputStatus">size</label>
                        <select class="form-control"  name="size" id="size">
                        <option value="S">Small</option>
                        <option value="M">Medium</option>
                        <option value="L">Large</option>
                        <option value="XL">XL</option>
                        <option value="XXl">XXL</option>
                        </select>
                    </div>
                </div>
                <button type="button" class="btn btn-primary mr-2" id="addProductVariantSubmit">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button> 
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
