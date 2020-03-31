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
                    <a id="clear16" class="btn btn-secondary" href="{{route('eventhighlights')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addEventdHighlights" tabindex="">ADD NEW</a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="export14" class="btn btn-secondary" onclick="fnExcelReport('event')"  tabindex="">EXPORT</a>
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
                        <h4 class="mess" style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 class="mess" style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="highlightstableData">
                      <thead>
                        <tr>
                            <th>@sortablelink('Id')</th>
                            <th>Image</th>
                            <th>@sortablelink('Title')</th>
                            <th>@sortablelink('event_name','Events')</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                          <td>{{$value->unique_id}}</td>
                          <td>@if($value->image!= null)
                                    <img src="{{asset('public/upload/highlights/')}}{{'/'.$value->image}}" alt="">
                                @else
    
                                @endif</td>
                          <td>{{$value->title}}</td>
                          <td>{{$value->event_name}}</td>
                          <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editEventHighlights{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                          <a class="delete" onclick="return confirm('Are you sure you want to delete this Event Highlights?')" href="{{route('eventhighlights.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                    <!-- Edit Modal HTML Markup -->
                        <div id="editEventHighlights{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Highlights</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addEventdHighlights" id="editEventHighlightsform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Photo</label>
                                                    <input type="file" name="image" class="file-upload-default">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" value="{{$value->image}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                        <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Title</label>
                                                    <input type="text" required class="form-control" Re id="title" value="{{$value->title}}" name="title" placeholder="Title">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="title-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Event Name</label>
                                                    <select name="eventname" id="eventname" class="form-control common_id">
                                                        <option value=""> -- Select One --</option>
                                                        @foreach ($common_id as $common)
                                                            <option value="{{ $common->id }}" {{ $value->common_id == $common->id ? 'selected' : ''}}>{{ $common->event_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        <strong class="eventname-error"></strong>
                                                    </span>
                                                    <input type="hidden" value="event" name="type">
                                                </div> 
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputPassword">Start Date</label>
                                                    <input type="date" class="form-control datepicker" value="{{$value->start_date}}" id="start_date" name="start_date" placeholder="Start Date">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputPassword">Start Time</label>
                                                    <input type="text" class="form-control timepicker" value="{{$value->start_time}}" id="start_time" name="start_time" placeholder="Start Time">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectGender">End Date</label>
                                                    <input type="date" class="form-control datepicker" value="{{$value->end_date}}" id="end_date" name="end_date" placeholder="End Date">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputPassword">End Time</label>
                                                    <input type="text" class="form-control timepicker" value="{{$value->end_time}}" id="end_time" name="end_time" placeholder="End Time">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <textarea class="form-control ckeditor" id="description{{$value->id}}" name="desc">{{$value->description}}</textarea>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editEventHighlightsSubmit" data-id="{{$value->id}}" id="editAreaSubmit">Submit</button>
                                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /edit.modal -->

                        @endforeach @else
                        <tr>
                        <td colspan="6">No Records Found</td>
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

         $('.timepicker').datetimepicker({
            format: 'HH:mm:ss'
        });
        
        $(document).on('click','.editEventHighlightsSubmit',function(e){
       
        var id = $(this).data('id');
        var formData = new FormData($("#editEventHighlightsform"+id)[0]);

            $( '.title-error' ).html( "" ); 
            $( '.eventname-error' ).html( "" );    
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
                url: "{{ route('eventhighlights.update') }}",
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
                    if(result.errors.eventname){
                        $( '.eventname-error' ).html( result.errors.eventname[0] );
                    }
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editEventHighlights'+id).modal('hide');
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

    $('#addEventHighlightsSubmit').click(function(e){
            var formData = new FormData($("#addHighlightsform")[0]);
            var message = CKEDITOR.instances['description'].getData();
            formData.append('description',message);
            $( '#title-error' ).html( "" );   
            $( '#eventname-error' ).html( "" );    

            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addHighlights') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                if(result.errors) 
                {
                    $(".statusMsg").hide(); 
                    if(result.errors.title){
                            $( '#title-error' ).html( result.errors.title[0] );
                        }
                    if(result.errors.eventname){
                            $( '#eventname-error' ).html( result.errors.eventname[0] );
                        }
                }
                if(result.status == true)
                {
                    var data = result.data;
                    // var propertyadmin =  result.data.propertyadmin;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addHighlightsform")[0].reset();
                        $('#addHighlights').modal('hide');
                        window.location.reload();
                    }, 3000);

                    $("#addHighlightsform")[0].reset();
                     window.location.reload();
                    
                    // $("#addMallBrandform")[0].reset();
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
                url: "{{route('eventhighlights.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val(),'type':'event'},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    
                    var findnorecord = $('#highlightstableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#highlightstableData tr.norecord').remove();
                    }
                    $("#highlightstableData tbody").html(data);
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
function fnExcelReport(type)
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
        url: "{{route('eventhighlights.export')}}",
        method: 'get',
        data: {'search':search,'type':type},
        success: function(result){
            $(result).table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "eventhightlight",
                filename: "eventhightlight" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls", //do not include extension
                fileext: ".xls" // file extension
              }); 
        }
    });
}
</script>
@endsection

<!-- Modal HTML Markup -->
<div id="addEventdHighlights" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Highlights</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addHighlightsform" id="addHighlightsform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleSelectPhoto">Photo</label>
                            <input type="file" name="image" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Title</label>
                            <input type="text" required class="form-control" Re id="title" name="title" placeholder="Title">
                            <span class="text-danger">
                                <strong id="title-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputStatus">Event Name</label>
                            <select name="eventname" id="eventname" class="form-control">
                                <option value=""> -- Select One --</option>
                                @foreach ($common_id as $common)
                                    <option value="{{ $common->id }}">{{ $common->event_name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" value="event" name="type">
                             <span class="text-danger">
                                <strong id="eventname-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputPassword">Start Date</label>
                            <input type="date" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputPassword">Start Time</label>
                            <input type="text" class="form-control timepicker" id="start_time" name="start_time" placeholder="Start Time">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleSelectGender">End Date</label>
                            <input type="date" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputPassword">End Time</label>
                            <input type="text" class="form-control timepicker" id="end_time" name="end_time" placeholder="End Time">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <textarea class="form-control ckeditor" id="description" name="description"></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addEventHighlightsSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->