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
                    <a id="clear16" class="btn btn-secondary" href="{{route('directory')}}" tabindex="" >CLEAR</a>
                </div> 
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addDirectory" tabindex="">ADD NEW</a>
                </div>
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="export14" class="btn btn-secondary" href="{{route('user.csv')}}" tabindex="">EXPORT</a>
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
                    <table class="table table-hover" id="directorytableData">
                      <thead>
                        <tr>
                            <th>@sortablelink('Id')</th>
                            <th>@sortablelink('Name')</th>
                            <th>@sortablelink('Category')</th>
                            <th>@sortablelink('Floor Name')</th>
                            <th>@sortablelink('Unit Number')</th>
                            <th>@sortablelink('Contact Number')</th>
                            <th>@sortablelink('Opening Hour')</th>
                            <th>@sortablelink('Description')</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                            <td>{{$value->unique_id}}</td>
                            <td>{{$value->name}}</td>
                            <td>{{$value->category_name }}</td>
                            <td>{{$value->floorname}}</td>
                            <td>{{$value->unit_number}}</td>
                            <td>{{$value->contact}}</td>
                            <td>{{$value->openinghrs}}</td>
                            <td>{{$value->description}}</td>
                            <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editDirectory{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a>
                                <a class="delete" onclick="return confirm('Are you sure you want to delete this Directory?')" href="{{route('directory.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                            </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editDirectory{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Trending</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addSponsor" id="editDirectoryform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Directory Name</label>
                                                    <input type="text" class="form-control name" required id="fullname" value="{{$value->name}}" name="name" placeholder="Category Name">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="name-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Category</label>
                                                    <select name="category_id" id="category_name" class="form-control category_id">
                                                        <option value=""> -- Select One --</option>
                                                        @foreach ($category_name as $cat)
                                                            <option value="{{ $cat->id }}"  {{ $value->category_id ==$cat->id ? 'selected' : ''}}>{{ $cat->category_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                            <strong class="category_id-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4 ">
                                                    <label for="exampleInputName"> Floor </label>
                                                    <select name="floor" id="floor" class="form-control floor">
                                                        <option value=""> -- Select One --</option>
                                                        @foreach ($floor as $values)
                                                            <option value="{{$values->id}}" {{$value->floor == $values->id ? 'selected' : ''}}>{{ $values->value}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="exampleInputName">Unit Number</label>
                                                <input type="text" required class="form-control unit_number"  id="unit_number" value="{{$value->unit_number}}" name="unit_number">
                                                <input type="hidden" name="id" value="{{$value->id}}">
                                                <span class="text-danger">
                                                    <strong id="unit_number-error"></strong>
                                                </span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="exampleSelectGender">Contact Number</label>
                                                <input type="text" class="form-control contact" id="contact" value="{{$value->contact}}" name="contact" placeholder="Contact">
                                                <span class="text-danger">
                                                    <strong class="contact-error"></strong>
                                                </span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="exampleInputPassword">Opening Hours</label>
                                                <input type="text" class="form-control openinghrs" value="{{$value->openinghrs}}"  id="openinghrs" name="openinghrs" placeholder="Opening Houres">
                                                <span class="text-danger">
                                                    <strong class="openinghrs-error"></strong>
                                                </span>
                                            </div>                                            
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="exampleInputPassword">Closing Hours</label>
                                                <input type="text" class="form-control closinghrs" value="{{$value->closinghrs}}"  id="closinghrs" name="closinghrs" placeholder="Closing Houres">
                                                <span class="text-danger">
                                                    <strong class="closinghrs-error"></strong>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <textarea class="form-control ckeditor" id="description{{$value->id}}" name="desc">{{$value->description}}</textarea>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary mr-2 editDirectorySubmit" data-id="{{$value->id}}" id="editDirectorySubmit">Submit</button>
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

        setTimeout(function(){
           $("h4.mess").remove();
        }, 5000 ); // 5 secs

        // $('.category_id').multiselect({
        //     columns: 1,
        //     placeholder: 'Select Category'
        // });

        //CKEDITOR.replace( 'description' );
        $('.timepicker').datetimepicker({
            format: 'HH:mm:ss'
        });

    $('.editDirectorySubmit').click(function(e){
        var id = $(this).data('id');
        var formData = new FormData($("#editDirectoryform"+id)[0]);
        var message = CKEDITOR.instances['description'+id].getData();

        formData.append('description',message);

        // $( '.title-error' ).html( "" );
            // $( '.url-error' ).html( "" );
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('directory.update') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                // if(result.errors) {
                //     $(".statusMsg").hide();
                //     if(result.errors.title){
                //         $( '.title-error' ).html( result.errors.title[0] );
                //     }
                //     if(result.errors.url){
                //         $( '.url-error' ).html( result.errors.url[0] );
                //     }
                // }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setInterval(function(){ 
                        $('#editDirectory'+id).modal('hide');
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
        $('#addDirectorySubmit').click(function(e){
            var formData = new FormData($("#addDirectoryform")[0]);       
            var message = CKEDITOR.instances['description'].getData();
            console.log(message);         
            // $( '#image-error' ).html( "" );
            // $( '#title-error' ).html( "" );
            // $( '#url-error' ).html( "" );
            formData.append('description',message);
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addDirectory') }}",
                method: 'post',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(result){
                // if(result.errors) {
                //     $(".statusMsg").hide();
                //     if(result.errors.image){
                //         $( '#image-error' ).html( result.errors.image[0] );
                //     }
                //     if(result.errors.title){
                //         $( '#title-error' ).html( result.errors.title[0] );
                //     }
                //     if(result.errors.url){
                //         $( '#url-error' ).html( result.errors.url[0] );
                //     }
                // }
                if(result.status == true)
                {
                    var data = result.data.sponsors;
                    // var propertyadmin =  result.data.propertyadmin;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addDirectoryform")[0].reset();
                        $('#addDirectory').modal('hide');
                        window.location.reload();
                    }, 3000);
                    
                    $("#addDirectoryform")[0].reset();
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
                url: "{{route('directory.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val()},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;

                    var findnorecord = $('#directorytableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#directorytableData tr.norecord').remove();
                        }

                    var deleteurl = '{{ route("directory.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);
                    var tr_str = "<tr>"+
                    "<td>"+data.unique_id+"</td>" +
                    "<td>"+data.name+"</td>" +
                    "<td>"+data.category_id+"</td>"+
                    "<td>"+data.floor+"</td>" +
                    "<td>"+data.unit_number+"</td>" +
                    "<td>"+data.contact+"</td>" +
                    "<td>"+data.openinghrs+"</td>" +
                    "<td>"+data.description+"</td>" +
                    "<td><a class='edit open_modal' data-toggle='modal' data-target="+'#editDirectory'+data.id+"><i class='mdi mdi-table-edit'></i></a><a class='delete' onclick='return confirm('Are you sure you want to delete this Directory?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    console.log(tr_str);
                    $("#directorytableData tbody").html(tr_str);
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
</script>
@endsection
<!-- Modal HTML Markup -->
<div id="addDirectory" class="modal fade">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Directory</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addDirectoryform" id="addDirectoryform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Directory Name</label>
                            <input type="text" required class="form-control"  id="name" name="name" placeholder="Directory Name">
                            <span class="text-danger">
                                <strong id="name-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputStatus">Category</label>
                            <select name="category_id" id="category_name" class="form-control">
                                <option value=""> -- Select One --</option>
                                @foreach ($category_name as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name}}</option>
                                @endforeach 
                            </select>
                            <span class="text-danger">
                                <strong id="category_name-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Floor</label>
                            <select name="floor" id="floor" class="form-control">
                                <option value=""> -- Select One --</option>
                                @foreach ($floor as $floor)
                                    <option value="{{ $floor->value }}"  {{ (isset($floor->id) || old('id'))? "":"selected" }}>{{ $floor->value }}</option>
                                @endforeach 
                            </select>
                            <span class="text-danger">
                                <strong id="floor-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Unit Number</label>
                            <input type="text" required class="form-control"  id="unit_number" name="unit_number" placeholder="Unit Number">
                            <span class="text-danger">
                                <strong id="unit_number-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputPassword">Contact</label>
                            <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact">
                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="contact-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputPassword">Opening Hours</label>
                            <input type="text" class="form-control timepicker" id="openinghrs" name="openinghrs" placeholder="Opening Houres">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="text-danger">
                                <strong id="openinghrs-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleSelectGender">Closing Hours</label>
                            <input type="text" class="form-control timepicker" id="closinghrs" name="closinghrs" placeholder="Closing Hours">
                            <span class="text-danger">
                                <strong id="closinghrs-error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <textarea class="form-control ckeditor" id="description" name="description"></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addDirectorySubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->  