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
                    {{-- <div class="input-group">
                        <div class="input-group-prepend">
                        <span class="input-group-text" id="search">
                            <i class="mdi mdi-magnify"></i>
                        </span>
                        </div>
                        <input type="text" class="form-control" placeholder="search" id="searchtext" aria-label="search" aria-describedby="search">
                    </div> --}}
                </div>   
                <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addRewardSetting" tabindex="">ADD NEW</a>
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
                        <h4 style="text-align: center; color: green;">{{ session('success') }}</h4>
                        @endif
                        @if (session()->has('error'))
                        <h4 style="text-align: center; color: red;">{{ session('error') }}</h4>
                        @endif
                    </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="rewardstableData">
                      <thead>
                        <tr>
                          <th>@sortablelink('Type')</th>
                          <th>@sortablelink('Points')</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                             <td>{{$value->title}}</td>
                             <td>{{$value->value}}</td>
                            <td><a class="edit open_modal" data-toggle="modal" data-target="#editRewardSetting{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                            <a class="delete" onclick="return confirm('Are you sure you want to delete this Reward Points Setting?')" href="{{route('rewardsetting.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editRewardSetting{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Reward Setting</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addbrandform" id="editrewardsettingform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                                @csrf
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputRole">Type</label>
                                                    <select class="form-control title" id="title" name="title">                                         
                                                        <option value="">Type</option>
                                                        <option value="Referral Bonus" {{ $value->title == 'Referral Bonus' ? 'selected' : ''}}>Referral Bonus</option>
                                                        <option value="1SGD" {{ $value->title == '1SGD' ? 'selected' : ''}}>1SGD</option>
                                                        <option value="Signup" {{ $value->title == 'Signup' ? 'selected' : ''}}>Signup</option>
                                                    </select>
                                                    <span class="text-danger">
                                                        <strong class="title-error"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-6 title">
                                                    <label for="exampleInputName"> Points </label>
                                                    <input type="text" class="form-control points" required id="points" value="{{$value->value}}" name="points" placeholder="Title">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="point-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editRewardSettingSubmit" data-id="{{$value->id}}" id="editRewardSettingSubmit">Submit</button>
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

    $('#editRewardSettingSubmit').click(function(e){
        var id = $(this).data('id');
        var formData = new FormData($("#editrewardsettingform"+id)[0]);
        $('.title-error' ).html( "" );
        $('.point-error' ).html( "" );
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('rewardsetting.update') }}",
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
                    if(result.errors.points){
                        $( '.point-error' ).html( result.errors.points[0] );
                    }                  
                }
                if(result.status == true)
                {
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setInterval(function(){ 
                        $('#editRewardSetting'+id).modal('hide');
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
        $('#addRewardsettingSubmit').click(function(e){
            var formData = new FormData($("#addRewardform")[0]);
            $( '#title-error' ).html( "" );
            $( '#point-error' ).html( "" );
                
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('addRewardSetting') }}",
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
                    if(result.errors.point){
                        $( '#point-error' ).html( result.errors.point[0] );
                    }                  
                }
                if(result.status == true)
                {
                    var data = result.data.events;
                    var propertyadmin =  result.data.propertyadmin;
                    $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                    setTimeout(function(){ 
                        $('.statusMsg').html('');
                        $("#addRewardform")[0].reset();
                        $('#addRewardSetting').modal('hide');
                        window.location.reload();
                    }, 3000);
                    
                    $("#addRewardform")[0].reset();
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
<div id="addRewardSetting" class="modal fade">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add Reward Setting</h1>
            </div>
            <div class="modal-body">
            <p class="statusMsg"></p>
                <form name="addRewardform" id="addRewardform" role="form" method="POST" enctype= "multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="exampleInputRole">Type</label>
                            <select class="form-control" id="title" name="title">
                            <option value="" selected="">Type</option>
                            <option value="Referral Bonus">Referral Bonus</option>
                            <option value="1SGD">1SGD</option>
                            <option value="Signup">Signup</option>
                            </select>
                            <span class="text-danger">
                                <strong id="title-error"></strong>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exampleInputName">Points</label>
                            <input type="text" class="form-control"  id="point" name="point" placeholder="Points">
                            <span class="text-danger">
                                <strong id="point-error"></strong>
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" id="addRewardsettingSubmit">Submit</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
