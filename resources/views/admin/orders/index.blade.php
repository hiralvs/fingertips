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
                    <a id="clear16" class="btn btn-secondary" href="{{route('orders')}}" tabindex="" >CLEAR</a>
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
                    <p class="statusMsg"></p>
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
                    <table class="table table-hover" id="orderstableData">
                      <thead>
                        <tr>
                          <th>@sortablelink('order_id','Order Id')</th>
                          <th>@sortablelink('order_id','Order Item No')</th>
                          <th>@sortablelink('unique_id','Product Id')</th>
                          <th>@sortablelink('product_name','Product Name')</th>
                          <th>@sortablelink('brand_name','Brand Name')</th>
                          <th>@sortablelink('order_by','Customer Name')</th>
                          <th>@sortablelink('Amount')</th>
                          <th>@sortablelink('created_at','Order Date')</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($data) && $data->count() > 0)
                            @foreach($data as $key => $value)
                        <tr>
                            <td>{{$value->order_id}}</td>
                            <td>{{$value->order_bill_no}}</td>
                            <td>{{$value->unique_id}}</td>
                            <td>{{$value->product_name}}</td>
                            <td>{{$value->brand_name}}</td>
                            <td>{{$value->order_by}}</td>
                            <td>{{$value->amount}}</td>
                            <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                            <td><select name="status" id="status{{$value->id}}" onchange="changeStatus({{$value->id}})">
                                <option value="awaitig pickup" {{ $value->status == 'awaitig pickup' ? 'selected' : ''}} >Awaiting Pickup</option>
                                <option value="pickup complete" {{ $value->status == 'pickup complete' ? 'selected' : ''}}>Pickup Complete</option>
                            </select></td>
                        </tr>
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
<script src="{{asset('public/js/file-upload.js')}}" ></script>
<script>

$(document).ready(function(){
    setTimeout(function(){
           $("h4.mess").remove();
        }, 5000 ); // 5 secs
        $(document).on('click','#search',function(){ 
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });       
        $.ajax({
                url: "{{route('order.search')}}",
                method: 'post',
                data: {'search':$("#searchtext").val()},
                success: function(result){
                if(result.status == true)
                {
                    var data = result.data;
                    
                    
                    var findnorecord = $('#orderstableData tr.norecord').length;
                    if(findnorecord > 0){
                        $('#orderstableData tr.norecord').remove();
                        }
                     $("#orderstableData tbody").html(data);
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
        url: "{{route('order.export')}}",
        method: 'get',
        data: {'search':search},
        success: function(result){
            $(result).table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "orders",
                filename: "orders" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls", //do not include extension
                fileext: ".xls" // file extension
              }); 
        }
    });
}

function changeStatus(id)
{
    var status = $("#status"+id).val();
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });       
    $.ajax({
            url: "{{route('order.changestatus')}}",
            method: 'post',
            data: {'id':id,'status':status},
            success: function(result){
            if(result.status == true)
            {
                $('.statusMsg').html('<span style="color:green;">'+result.msg+'</p>');
                setTimeout(function(){ 
                    $('.statusMsg').html('');
                    window.location.reload();
                }, 3000);
            }
            else
            {
                $('.statusMsg').html('<span style="color:red;">'+result.msg+'</span>');
            }
            }
        });
}
</script>
@endsection

