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
                    <a id="clear16" class="btn btn-secondary" href="{{route('rewards')}}" tabindex="" >CLEAR</a>
                </div> 
                {{-- <div class="pr-1 mb-3 mb-xl-0">
                    <a id="addnew15" class="btn btn-primary" data-toggle="modal" data-target="#addShopsandMalls" tabindex="">ADD NEW</a>
                </div> --}}
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
                          <th>@sortablelink('Customer Name')</th>
                          <th>@sortablelink('Wallet Value')</th>
                          <th>@sortablelink('Member Since')</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                             <td>{{$value->name}}</td>
                             <td>{{$value->earned}}</td>
                             <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                             
                            <td><a class="edit open_modal" data-toggle="modal" data-target="#editRewards{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a>
                                <a class="delete" onclick="return confirm('Are you sure you want to delete this Reward?')" href="{{route('rewards.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                        </tr>
                        <!-- Edit Modal HTML Markup -->
                        <div id="editRewards{{$value->id}}" class="modal fade">
                            <div class="modal-dialog  modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title">Edit Tax</h1>
                                    </div>
                                    <div class="modal-body">
                                    <p class="statusMsg"></p>
                                        <form name="addbrandform" id="editrewardsform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                                @csrf
                                            <div class="row">
                                                <div class="form-group col-md-6 title">
                                                    <label for="exampleInputName"> Tax Percentage </label>
                                                    <input type="text" class="form-control value" required id="value" value="{{$value->value}}" name="value" placeholder="Title">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="text-danger">
                                                        <strong class="value-error"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary mr-2 editTaxSubmit" data-id="{{$value->id}}" id="editPrivacySubmit">Submit</button>
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
        $(document).on('click','#search',function(){ 
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });       
        $.ajax({
                url: "{{route('rewards.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val()},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    
                    var findnorecord = $('#rewardstableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#rewardstableData tr.norecord').remove();
                        }
                        
                    if(data.created_at)
                    {
                        var cdate = "<?php echo date("d F Y",strtotime($value->created_at)) ?>";
                    }
                    var deleteurl = '{{ route("rewards.delete", ":id") }}';
                    deleteurl = deleteurl.replace(':id', data.id);
                    // var imageurl = "{{asset('public/upload/malls')}}";
                    var tr_str = "<tr>"+
                    "<td>"+data.name+"</td>" +
                    "<td>"+data.earned+"</td>" +
                    "<td>"+data.used+"</td>"+
                    "<td>"+data.redeem+"</td>" +
                    "<td>"+cdate+"</td>" +
                    "<td><a class='delete' onclick='return confirm('Are you sure you want to delete this Reward?')' href="+deleteurl+"><i class='mdi mdi-delete'></i></a></td>"+
                    "</tr>";
                    $("#rewardstableData tbody").html(tr_str);
                    $("#paging").hide();
                }
                else
                {
                    $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');


                    // $.each(result.errors, function(key, value){
                    //     $('.alert-danger').show();
                    //     $('.alert-danger').append('<li>'+value+'</li>');
                    // });
                }
                }
            });
    });
</script>
@endsection
