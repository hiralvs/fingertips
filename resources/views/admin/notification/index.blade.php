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
                    <a id="clear16" class="btn btn-secondary" href="{{route('notifications')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addNotifications" tabindex="">SEND NEW</a>
                </div>
                {{-- <div class="pr-1 mb-3 mb-xl-0">
                    <a id="export14" class="btn btn-secondary" href="{{route('user.csv')}}" tabindex="">EXPORT</a>
                </div> --}}
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
                        <h4 class="mess"  style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 class="mess"  style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="notificationtableData">
                      <thead>
                        <tr>
                            <th>@sortablelink('Title')</th>
                            <th>@sortablelink('Description')</th>
                            <th>@sortablelink('Created By')</th>
                            <th>@sortablelink('created_at','Created On')</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                            <td>{{$value->title}}</td>
                            <td>{{$value->description}}</td>
                            <td>{{$value->created_by}}</td>
                            <td>{{date("d F Y",strtotime($value->created_at))}}</td> 
                            <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editNotification{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a>
                                <a class="delete" onclick="return confirm('Are you sure you want to delete this Notification?')" href="{{route('notifications.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editNotification{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Notification</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="editShopsmalls" id="editNotificationform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="exampleInputName">Title</label>
                                                    <input type="text" required class="form-control title"  id="title" name="title" value="{{$value->title}}" placeholder="title">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="title-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="exampleInputName">Description</label>
                                                    <input type="text" required class="form-control description"  id="description" name="description" value="{{$value->description}}" placeholder="description">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="description-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus type">Type</label>
                                                    <select class="form-control type" name="type" data-id="{{$value->id}}">
                                                        <option value="">Select</option>
                                                        <option value="inbound" {{ $value->type == 'inbound' ? 'selected' : ''}}>Inbound</option>
                                                        <option value="outbound" {{ $value->type == 'outbound' ? 'selected' : ''}}>Outbound</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4 url" id="url{{$value->id}}" style="display:{{ $value->type == 'outbound' ? 'block' : 'none'}}">
                                                    <label for="exampleInputName">URL</label>
                                                    <input type="text" value="{{ $value->url != null ? $value->url : ''}}" required class="form-control url" name="url">
                                                </div>
                                                <div class="form-group col-md-4 ema" id="ema{{$value->id}}" style="display:{{ $value->type == 'inbound' ? 'block' : 'none'}}">
                                                    <label for="exampleInputStatus">EMA</label>
                                                    <select class="form-control ema" name="ema">
                                                        <option value="">EMA</option>
                                                        <option value="event" {{ $value->ema == 'event' ? 'selected' : ''}}>Event</option>
                                                        <option value="mall" {{ $value->ema == 'mall' ? 'selected' : ''}}>Mall</option>
                                                        <option value="attraction" {{ $value->ema == 'attraction' ? 'selected' : ''}}>Attraction</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4 inboundtext" id="inboundtext{{$value->id}}" style="display:{{ $value->type == 'inbound' ? 'block' : 'none'}}">
                                                    <label for="exampleInputName url">EMA Text</label>
                                                    <input type="text" class="form-control" id="inboundtext" name="inboundtext" placeholder="Inboundtext" value="{{$value->inboundtext}}">
                                                </div> 
                                            </div>
                                        <button type="button" class="btn btn-primary mr-2 editNotificationsSubmit" data-id="{{$value->id}}" id="editAreaSubmit">Submit</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- edit /.modal -->
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5">No records found</td>
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
    
    $(document).on('click','.editNotificationsSubmit',function(e){
       
        var id = $(this).data('id');
        var formData = new FormData($("#editNotificationform"+id)[0]);
            $( '.title-error' ).html( "" );
            $( '.description-error' ).html( "" );
        var id = $(this).data('id');
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('notifications.update') }}",
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
                    if(result.errors.description){
                        $( '.description-error' ).html( result.errors.description[0] );
                    }                  
                }
               
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('#editNotifications'+id).modal('hide');
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
        
    $('#addNotificationSubmit').click(function(e){
            var formData = new FormData($("#addNotificationsform")[0]);
            $( '#title-error' ).html( "" );
            $( '#description-error' ).html( "" );

            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addNotifications') }}",
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
                    if(result.errors.description){
                        $( '#description-error' ).html( result.errors.description[0] );
                    }                  
                }
                if(result.status == true)
                {
                    var data = result.data.notifications;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addNotificationsform")[0].reset();
                        $('#addNotifications').modal('hide');
                        window.location.reload();
                    }, 3000);
                    
                    $("#addNotificationsform")[0].reset();
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
                url: "{{route('notifications.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val()},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                                        
                    var findnorecord = $('#notificationtableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#notificationtableData tr.norecord').remove();
                        }
                    
                    if(data.created_at)
                    {
                        var cdate = date(data.created_at);
                    }
                    var deleteurl = '{{ route("notifications.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);
                    var tr_str = "<tr>"+
                    "<td>"+data.title+"</td>" +
                    "<td>"+data.description+"</td>"+
                    "<td>"+data.created_by+"</td>" +
                    "<td>"+data.created_at+"</td>" +
                    "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editNotification'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this Notification?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    console.log(tr_str);
                    $("#notificationtableData tbody").html(tr_str);
                    $("#paging").hide();
                }
                else
                {
                    $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');
                }
                }
            });
        });
    $("#type").change(function () {
            var id = $(this).data('id');
            if ($(this).val() == 'inbound') {
                $("#ema").show();
                $("#inboundtext").show();
                $("#url").hide();
            }else if ($(this).val() == 'outbound'){
                $("#ema").hide();
                $("#inboundtext").hide();
                $("#url").show();
            } 
        });
    });
    $(".type").change(function () {
            var id = $(this).data('id');

            // alert($(this).val());
            if ($(this).val() == 'inbound') {
                $("#ema"+id).show();
                $("#inboundtext"+id).show();
                $("#url"+id).hide();
            }else if ($(this).val() == 'outbound'){
                $("#ema"+id).hide();
                $("#inboundtext"+id).hide();
                $("#url"+id).show();
            }
        });
</script>
@endsection

<!-- Modal HTML Markup -->
<div id="addNotifications" class="modal fade">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Notification</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addNotificationsform" id="addNotificationsform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="exampleInputName">Title</label>
                            <input type="text" required class="form-control"  id="title" name="title" placeholder="title">
                            <span class="text-danger">
                                <strong id="title-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleInputName">Description</label>
                            <input type="text" required class="form-control"  id="description" name="description" placeholder="description">
                            <span class="text-danger">
                                <strong id="description-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputStatus type">Type</label>
                            <select class="form-control type" id="type" name="type">
                                <option value="">--Select--</option>
                                <option value="inbound">Inbound</option>
                                <option value="outbound">Outbound</option>
                            </select>
                            <span class="text-danger">
                                <strong id="type-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4 ema" id="ema" style="display:none;">
                            <label for="exampleInputStatus ema">EMA</label>
                            <select class="form-control" id="ema" name="ema">
                                <option value="" selected="">EMA</option>
                                <option value="0">Event</option>
                                <option value="1">Mall</option>
                                <option value="2">Attraction</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 inboundtext" id="inboundtext" style="display:none;">
                            <label for="exampleInputName url">EMA Text</label>
                            <input type="text" class="form-control" id="inboundtext" name="inboundtext" placeholder="Inboundtext">
                        </div> 
                        <div class="form-group col-md-4 url" id="url" style="display:none;">
                            <label for="exampleInputName url">URL</label>
                            <input type="text" class="form-control" id="url" name="url" placeholder="url">
                        </div> 
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addNotificationSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->