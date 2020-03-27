
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
                    <a id="clear16" class="btn btn-secondary" href="{{route('mallslider')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addMallSlider" tabindex="">ADD NEW</a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="export14" class="btn btn-secondary" onclick="fnExcelReport()" tabindex="">EXPORT</a>
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
                        <h4 class="mess" style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 class="mess" style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="slidertableData">
                      <thead>
                        <tr>
                            <th>@sortablelink('slider_image_name','Slider Image')</th>
                            <th>@sortablelink('image_id','Slider Image id')</th>
                            <th>@sortablelink('created_at','Created On')</th>
                            <th>@sortablelink('mallname','Shops and Malls')</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                          <td><img src="{{asset('public/upload/sliders/')}}/{{$value->slider_image_name}}" alt=""></td>
                          <td>{{$value->image_id}}</td>
                          <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                          <td>{{$value->created_by}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editMallSlider{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Sldier?')" href="{{route('mallslider.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editMallSlider{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit MallSlider</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addMallBrandform" id="editMallSliderform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Slider Image</label>
                                                    <input type="file" name="image" class="file-upload-default">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" value="{{$value->slider_image_name}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                        <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                        </span>
                                                    </div>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="image-error{{$value->id}}"></strong>
                                                    </span>
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <input type="hidden" value="malls" name="type">
                                                </div>   
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Malls and Shops</label>
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <select name="common_id" id="commonname" class="form-control common_id">
                                                        <option value=""> -- Select One --</option>
                                                        @if(!empty($malls) && $malls->count() > 0)
                                                            @foreach ($malls as $key => $pd)
                                                                  <option value="{{$pd->id}}" {{ $value->common_id == $pd->id ? 'selected' : ''}} >{{$pd->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <span class="text-danger">
                                                        <strong class="common_id-error"></strong>
                                                    </span>
                                                </div> 
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editMallSliderSubmit" data-id="{{$value->id}}" id="editSliderSubmit">Submit</button>
                                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /edit.modal -->
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
<!-- //view-source:https://maps.googleapis.com/maps/api/js?key=AIzaSyBrZ7Gj6VQ4ReRytE4tQm0RFOFCQiMFl8U&libraries=places,geometry&callback=loadGoogleMap -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBrZ7Gj6VQ4ReRytE4tQm0RFOFCQiMFl8U&libraries=places,geometry"></script>
<style>
    .pac-container {
        z-index: 10000 !important;
    }
</style>  

<script>
   
$(document).ready(function(){
     setTimeout(function(){
           $("h4.mess").remove();
        }, 5000 );
    $(document).on('click','.editMallSliderSubmit',function(e){
       
        var id = $(this).data('id');
        var formData = new FormData($("#editMallSliderform"+id)[0]);

            $( '.common_id-error' ).html( "" );
            $( '.image-error' ).html( "" ); 
        
        // var message = CKEDITOR.instances['description'+id].getData();

        // formData.append('description',message);
        var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('mallslider.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                    if(result.errors) {
                    $(".statusMsg").hide();
                    console.log(result.errors.common_id[0]);
                    if(result.errors.common_id){
                        $( '.common_id-error' ).html( result.errors.common_id[0] );
                    }                    
                    if(result.errors.status){
                        $( '.image-error' ).html( result.errors.status[0] );
                    }                    
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editMallSlider'+id).modal('hide');
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
    $('#addMallSliderSubmit').click(function(e){
            var formData = new FormData($("#addMallSliderform")[0]);
            
            $( '#common_id-error' ).html( "" );
            $( '#image-error' ).html( "" );    

            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addMallSlider') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){ alert('sdf');
                console.log(result);
                if(result.errors) {
                    $(".statusMsg").hide();
                                        console.log(result.errors);
 alert(result.errors.common_id);
                    if(result.errors.common_id){
                            $( '#common_id-error' ).html( result.errors.common_id[0] );
                        }                  
                    if(result.errors.status){
                            $( '#image-error' ).html( result.errors.status[0] );
                        }                  
                }
                if(result.status == true)
                {
                    var data = result.data;
                    // var propertyadmin =  result.data.propertyadmin;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addMallSliderform")[0].reset();
                        $('#addMallSlider').modal('hide');
                        window.location.reload();
                    }, 3000);

                    $("#addSliderform")[0].reset();
                     window.location.reload();
                    
                    $("#addMallSliderform")[0].reset();
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
                url: "{{route('mallslider.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val(),'type' : 'malls'},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    
                    var findnorecord = $('#sliderstableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#sliderstableData tr.norecord').remove();
                    }
                    
                    var deleteurl = '{{ route("mallslider.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);
                    var tr_str = "<tr>"+
                    "<td>"+data.unique_id+"</td>" +
                    "<td>"+data.slidername+"</td>" +
                    "<td>"+data.mallname+"</td>" +
                    "<td>"+data.status+"</td>" +
                    "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editMallSlider'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this Slider?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    $("#brandstableData tbody").html(tr_str);
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
    var tT = new XMLSerializer().serializeToString(document.querySelector('#brandstableData')); //Serialised table
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
<div id="addMallSlider" class="modal fade">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add MallSlider</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addMallSliderform" id="addMallSliderform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="exampleSelectPhoto">Slider Image</label>
                            <input type="file" name="image" id="image" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="image-error"></strong>
                            </span>
                            <input type="hidden" value="malls" name="type">
                        </div>   
                        <div class="form-group col-md-6">
                            <label for="exampleInputStatus">Malls and Shops</label>
                            <select name="mallname" id="mallname" class="form-control">
                                <option value=""> -- Select One --</option>
                                @foreach ($malls as $common)
                                    <option value="{{ $common->id }}">{{ $common->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                                <strong id="common_id-error"></strong>
                            </span>
                        </div>  
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addMallSliderSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->