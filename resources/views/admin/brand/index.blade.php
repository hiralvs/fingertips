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
                    <a id="clear16" class="btn btn-secondary" href="{{route('brand')}}" tabindex="" >CLEAR</a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addBrand" tabindex="">
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
                    <table class="table table-hover" id="brandData">
                      <thead>
                        <tr>
                          <th>Brand Logo</th>
                          <th>@sortablelink('id')</th>
                          <th>@sortablelink('name')</th>
                          <th>No Of Products</th>
                          <th>Category</th>
                          <th>Total Earnings</th>
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
                              <td>
                                @if($value->brand_image!= null)
                                    <img src="{{asset('public/upload/brands')}}{{'/'.$value->brand_image}}" alt="">
                                @else
    
                                @endif
                                </td>
                          <td>{{$value->unique_id}}</td>
                          <td>{{$value->name}}</td>
                          <td>{{$value->product_count}}</td>
                          <td>{{$value->category_id}}</td>
                          <td>{{$value->commission}}</td>
                          
                          <td> @if($value->status == '1') 
                                Inactive
                            @else
                                Active
                                @endif
                            </td>
                          <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-target="#editBrand{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Brand?')" href="{{route('brand.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editBrand{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Brand</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addbrandform" id="editbrandform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                                @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Brand Logo</label>
                                                    <input type="file" class="file-upload-default" name="brand_image">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" value="{{ $value->brand_image != null ? $value->brand_image : ''}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                        <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Name</label>
                                                    <input type="text" class="form-control name" required id="fullname" value="{{$value->name}}" name="name" placeholder="Name">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="name-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Brand Merchant</label>
                                                    <select name="grand_merchant_user_id" id="grand_merchant_user_id" class="form-control">
                                                        <option value=""> -- Select One --</option>
                                                        @foreach ($grand_merchant_user_id as $brand)
                                                            <option value="{{ $brand->id }}"  {{ (isset($brand->id) || old('id'))? "selected":"" }}>{{ $brand->name }}</option>
                                                        @endforeach 
                                                    </select>
                                                </div>
                                            </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Category</label>
                                                    <select class="form-control category_id" multiple name="category_id[]" id="category_id">
                                                    <!-- <option value="">--Select--</option> -->
                                                    @if(!empty($category) && $category->count() > 0)
                                                        @foreach($category as $key => $cat)
                                                        <option value="{{$cat->id}}"  {{ in_array($cat->id,explode(",",$value->category_id))  ? 'selected' : ''}}>{{$cat->category_name}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
                                                    <span class="text-danger">
                                                        <strong class="category_id-error"></strong>
                                                    </span>
                                                </div>
                                            <div class="form-group col-md-4">
                                                <label for="exampleInputStatus">Status</label>
                                                <select class="form-control status" id="status" name="status">
                                                    <option value="" selected="">Status</option>
                                                    <option value="0" {{ $value->status == '0' ? 'selected' : ''}}>Active</option>
                                                    <option value="1" {{ $value->status == '1' ? 'selected' : ''}}>Inactive</option>
                                                </select>
                                                <span class="text-danger">
                                                        <strong class="status-error"></strong>
                                                </span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="exampleInputFinertip">Fingertips</label>
                                                <input type="tel" class="form-control commission" required id="commission" value="{{$value->commission}}" name="commission" placeholder="Name">
                                                <span class="text-danger">
                                                    <strong class="commission-error"></strong>
                                                </span>
                                            </div>
                                            <div class="form-group col-md-12"> 
                                                <textarea class="description ckeditor" id="description{{$value->id}}" name="description">{{$value->description}}</textarea>
                                            </div>
                                        </div>
                                            <button type="button" class="btn btn-primary mr-2 editBrandSubmit" data-id="{{$value->id}}" id="addBrandSubmit">Save</button>
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

    $('.editBrandSubmit').click(function(e){
        var id = $(this).data('id');
        var formData = new FormData($("#editbrandform"+id)[0]);
        var message = CKEDITOR.instances['description'+id].getData();
        $('.name-error' ).html( "" );
        $('.category_id-error' ).html( "" );
        $('.status-error' ).html( "" );
        $('.commission-error' ).html( "" );
        formData.append('description',message);
        var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('brand.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.name){
                        $( '.name-error' ).html( result.errors.name[0] );
                    }
                    if(result.errors.category_id){
                        $( '.category_id-error' ).html( result.errors.category_id[0] );
                    }
                    if(result.errors.status){
                        $( '.status-error' ).html( result.errors.status[0] );
                    }
                    if(result.errors.commission){
                        $( '.commission-error' ).html( result.errors.commission[0] );
                    }
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editBrand'+id).modal('hide');
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
    $('#addBrandSubmit').click(function(e){
        var formData = new FormData($("#addbrandform")[0]);
        var message = CKEDITOR.instances['desc'].getData();
        console.log(message);
        $( '#name-error' ).html( "" );
        $( '#category_id-error' ).html( "" );
        $( '#status-error' ).html( "" );
        $( '#commission-error' ).html( "" );

            formData.append('description',message);
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addbrand') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) {
                    $(".statusMsg").hide();
                    if(result.errors.name){
                        $( '#name-error' ).html( result.errors.name[0] );
                    }
                    if(result.errors.category_id){
                        $( '#category_id-error' ).html( result.errors.category_id[0] );
                    }
                    if(result.errors.status){
                        $( '#status-error' ).html( result.errors.status[0] );
                    }
                    if(result.errors.commission){
                        $( '#commission-error' ).html( result.errors.commission[0] );
                    }
                }
                if(result.status == true)
                {
                    var data = result.data;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setInterval(function(){ 
                         $('.statusMsg').html('');
                        $('#addBrand').modal('hide');
                        //  $('#done-message').addClass('hide');
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
                url: "{{route('brand.brandsearch')}}",
                method: 'post',
                data: {'search':$("#searchtext").val()},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    
                    var findnorecord = $('#brandData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#brandData tr.norecord').remove();
                        }
                    
                    var brand_image = commission = status = '';
                    if(data.brand_image != null)
                    {
                        brand_image = data.brand_image;
                    }
                    if(data.commission != null)
                    {
                        commission = data.commission;
                    }
                    if(data.status == 0)
                    {
                        status = 'Active';
                    }
                    else
                    {
                        status = 'Inactive';
                    }
                    if(data.created_at)
                    {
                        var cdate = "<?php echo date("d F Y",strtotime(":date")) ?>";
                        cdate = cdate.replace(':date', data.created_at);
                        //var cdate = "<?php //echo date("d F Y",strtotime($value->created_at)) ?>";
                    }
                    var deleteurl = '{{ route("brand.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);
                    var tr_str = "<tr>"+
                    "<td>"+brand_image+"</td>" +
                    "<td>"+data.unique_id+"</td>" +
                    "<td>"+data.name+"</td>" +
                    "<td>"+data.noofproducts+"</td>" +
                    "<td>"+category_id+"</td>" +
                    // "<td>"+noofpresence+"</td>" +
                    "<td>"+data.commission+"</td>" +
                    "<td>"+status+"</td>" +
                    "<td>"+cdate+"</td>" +
                    "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editUser'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this User?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    console.log(tr_str);
                    $("#brandData tbody").html(tr_str);
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
    var tT = new XMLSerializer().serializeToString(document.querySelector('#brandData')); //Serialised table
    var tF = 'brand.xls'; //Filename
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
<div id="addBrand" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Brand</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
            @if(count($errors) > 0)
                @foreach ($errors->all() as $error)
                    <p class='alert alert-danger'>{{ $error }}</p>
                @endforeach
            @endif
                <form name="addbrandform" id="addbrandform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    {{-- @foreach($errors as $error)
                        <li>{{$error}}</li>
                    @endforeach  --}}
                <div class="row">
                   <div class="form-group col-md-4">
                        <label for="exampleSelectPhoto">Brand Logo</label>
                        <input type="file" class="form-control" id="photo" name="brand_image" placeholder="Photo">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleInputName">Name</label>
                        <input type="text" required class="form-control" Re id="name" name="name" placeholder="Name">
                        <span class="text-danger">
                            <strong id="name-error"></strong>
                        </span>
                    </div>
                    {{-- <div>{{ $errors->first('name') }}</div> --}}
                    
                    <div class="form-group col-md-4">
                        <label for="exampleInputStatus">Brand Merchant</label>
                        <select name="grand_merchant_user_id" id="grand_merchant_user_id" class="form-control">
                            <option value="" selected="">Select One</option>
                            @foreach ($grand_merchant_user_id as $brand)
                                <option value="{{ $brand->id }}"  {{ (isset($brand->id) || old('id'))? "":"selected" }}>{{ $brand->name }}</option>
                            @endforeach 
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                            <label for="exampleInputStatus">Category</label>
                            <select class="form-control category_id" multiple name="category_id[]" id="category_id">
                            
                            @if(!empty($category) && $category->count() > 0)
                                @foreach($category as $key => $cat)
                                <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                                @endforeach
                            @endif
                            </select>
                            <span class="text-danger">
                                <strong id="category_id-error"></strong>
                            </span>
                        </div>
                    <div class="form-group col-md-4">
                        <label for="exampleInputStatus">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="" selected="">Status</option>
                            <option value="0">Active</option>
                            <option value="1">Inactive</option>
                        </select>
                        <span class="text-danger">
                            <strong id="status-error"></strong>
                        </span>
                    </div>

                    
                    <div class="form-group col-md-4">
                        {{-- <label for="exampleFingertipsCommissione">Fingertips Commission </label> --}}
                        <label for="exampleFingertipsCommissione">Fingertips Commission</label>
                        <input style="" id="commission" type="tel" class="form-control input numberonly" name="commission" maxlength="" value="">
                         {{-- <input type="text" required class="form-control" Re id="fullname" name="name" placeholder="Name"> --}}
                        <span class="text-danger">
                            <strong id="commission-error"></strong>
                        </span>
                    </div>				
                </div>
                <div class="form-group col-md-12"> 
                    <textarea class="description ckeditor" id="desc" name="desc"></textarea>
                </div>
                <button type="button" class="btn btn-primary mr-2" id="addBrandSubmit">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button> 
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
