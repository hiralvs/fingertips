
@extends('layouts.app')

@section('content')
<div class="content-wrapper">

    <div class="row">
        <div class="col-lg-2 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="text-success font-weight-bold">{{$return_data['admincount']}}</h2>
                    </div>
                </div>
                <canvas id="newClient"></canvas>
                <div class="line-chart-row-title">Total Customer</div>
            </div>
        </div>
        <div class="col-lg-2 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="text-danger font-weight-bold">{{$return_data['brandcount']}}</h2>
                    </div>
                </div>
                <canvas id="allProducts"></canvas>
                <div class="line-chart-row-title">Total Brands</div>
            </div>
        </div>
        <div class="col-lg-2 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="text-info font-weight-bold">{{$return_data['checkincount']}}</h2>
                    </div>
                </div>
                <canvas id="invoices"></canvas>
                <div class="line-chart-row-title">Total Checkins</div>
            </div>
        </div>
        <div class="col-lg-2 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="text-warning font-weight-bold">{{$return_data['orderscount']}}</h2>
                    </div>
                </div>
                <canvas id="projects"></canvas>
                <div class="line-chart-row-title">Total Orders</div>
            </div>
        </div>
        <div class="col-lg-2 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="text-secondary font-weight-bold">{{$return_data['nearmecount'][0]->value}}</h2>
                    </div>
                </div>
                <canvas id="orderRecieved"></canvas>
                <div class="line-chart-row-title">Total users of NearMe</div>
            </div>
        </div>
        <!-- <div class="col-lg-2 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="text-dark font-weight-bold">7826</h2>
                        <i class="mdi mdi-cash text-dark mdi-18px"></i>
                    </div>
                </div>
                <canvas id="transactions"></canvas>
                <div class="line-chart-row-title">TRANSACTIONS</div>
            </div>
        </div> -->
    </div>
    <div class="row">
        <div class="col-lg-6 grid-margin  stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Top 20 Purchases</h4>
                    <div class="table-responsive" style="overflow:auto;width: 570px;height: 500px;">
                    <table class="table table-hover" id="taxtableData">
                      <thead>
                        <tr>
                          <th>Order Id</th>
                          <th>Product Name</th>
                          <th>Amount</th>
                          <th>Status</th>
                          <th>Created At</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($return_data['toppurchases']) && $return_data['toppurchases']->count() > 0)
                            @foreach($return_data['toppurchases'] as $key => $value)
                        <tr>
                            <td>{{$value->order_id}}</td>
                            <td>{{$value->product_name}}</td>
                            <td>{{$value->amount}}</td>
                            <td>{{$value->status}}</td>
                            <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                        </tr>
                        @endforeach
                        @else
                            <tr>
                            <td colspan="4">No Records Found</td>
                            </tr>
                        @endif

                      </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin  stretch-card" >
            <div class="card" >
                <div class="card-body" >
                    <h4 class="card-title">Top 20 Places Visited</h4>
                    <div class="table-responsive" style="overflow:auto;width: 570px;height: 500px;">
                        <table class="table table-hover" id="taxtableData">
                          <thead>
                            <tr>
                              <th>Username</th>
                              <th>Property Name</th>
                              <th>Property Type</th>
                              <th>Created At</th>
                            </tr>
                          </thead>
                          <tbody>
                            @if(!empty($return_data['topplacesvisiter']) && $return_data['topplacesvisiter']->count() > 0)
                                @foreach($return_data['topplacesvisiter'] as $key => $value)
                            <tr>
                                <td>{{$value->username}}</td>
                                <td>{{$value->property_name}}</td>
                                <td>{{$value->esma_type}}</td>
                                <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                            </tr>
                            @endforeach
                            @else
                                <tr>
                                <td colspan="4">No Records Found</td>
                                </tr>
                            @endif

                          </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
       <div class="row">
        <div class="col-lg-6 grid-margin  stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Top 20 Favorites</h4>
                    <div class="table-responsive" style="overflow:auto;width: 570px;height: 500px;">
                        <table class="table table-hover" id="taxtableData">
                          <thead>
                            <tr>
                              <th>Id</th>
                              <th>Property name</th>
                              <th>Created At</th>
                            </tr>
                          </thead>
                          <tbody>
                            @if(!empty($return_data['topfavourites']) && $return_data['topfavourites']->count() > 0)
                                @foreach($return_data['topfavourites'] as $key => $value)
                            <tr>
                                <td>{{$value->id}}</td>
                                <?php if($value->event_name != null)
                                    $name = $value->event_name;
                                else if($value->attraction_name != null)
                                    $name = $value->attraction_name;
                                else
                                    $name = $value->name;
                                ?>
                               <td>{{$name}}</td>
                               <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                            </tr>
                            @endforeach
                            @else
                                <tr>
                                <td colspan="4">No Records Found</td>
                                </tr>
                            @endif

                          </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin  stretch-card">
            <div class="card">
            <div class="card-body">
                <h4 class="card-title">Top 20 Social Media Share</h4>
                <div class="table-responsive" style="overflow:auto;width: 570px;height: 500px;">
                    <table class="table table-hover" id="taxtableData">
                      <thead>
                        <tr>
                          <th>Id</th>
                          <th>Property name</th>
                          <th>Shared On</th>
                          <th>Type</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($return_data['topsocialshare']) && $return_data['topsocialshare']->count() > 0)
                            @foreach($return_data['topsocialshare'] as $key => $value)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$value->property_name}}</td>
                            <td>{{$value->sharing_type}}</td>
                           <td>{{$value->esma_type}}</td>
                        </tr>
                        @endforeach
                        @else
                            <tr>
                            <td colspan="4">No Records Found</td>
                            </tr>
                        @endif

                      </tbody>
                    </table>
                </div>
            </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 grid-margin  stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Customer Signup  
                        <select name="gender" id="gender" onchange="customerchart()" style="float: right;">
                            <option value="">Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>&nbsp;
                        <select name="dob" id="dob" onchange="customerchart()" style="float: right;padding-right: 10px;margin-right: 10px;">
                            <option value="">DOB</option>
                            @foreach($return_data['dob'] as $value)
                                <option value="{{$value}}">{{$value}}</option>
                            @endforeach
                        </select>
                    </h4>

                    <div class="card-body py-3 px-3" id="customerchart">
                        <canvas id="canvas" height="280" width="600"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin  stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">ShopChart</h4>
                    <div class="card-body py-3 px-3" id="customerchart">
                        <canvas id="shopcanvas" height="280" width="600"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 grid-margin  stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Mall Chart</h4>
                    <div class="card-body py-3 px-3" id="customerchart">
                        <canvas id="mallcanvas" height="280" width="600"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin  stretch-card">
            <div class="card">
            <div class="card-body">
                <h4 class="card-title">Attraction Chart</h4>
                <div class="card-body py-3 px-3" id="customerchart">
                    <canvas id="attractioncanvas" height="280" width="600"></canvas>
                </div>
            </div>
            </div>
        </div>
    </div>
     <div class="row">
        <div class="col-lg-6 grid-margin  stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Event Chart</h4>
                    <div class="card-body py-3 px-3" id="customerchart">
                        <canvas id="eventcanvas" height="280" width="600"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin  stretch-card">
            <div class="card">
            <div class="card-body">
                <h4 class="card-title">Orders Generated</h4>
                <div class="card-body py-3 px-3" id="customerchart">
                    <canvas id="purchasecanvas" height="280" width="600"></canvas>
                </div>
            </div>
            </div>
        </div>
    </div>
        <!-- <div class="col-sm-6 grid-margin grid-margin-md-0 stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title">Support Tracker</h4>
                        <h4 class="text-success font-weight-bold">Tickets<span class="text-dark ml-3">163</span></h4>
                    </div>
                    <div id="support-tracker-legend" class="support-tracker-legend"></div>
                    <canvas id="supportTracker"></canvas>
                </div>
            </div>
        </div> -->
        <!-- <div class="col-sm-6 grid-margin grid-margin-md-0 stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">Product Orders</h4>
                        <p class="text-dark">+5.2% vs last 7 days</p>
                    </div>
                    <div class="product-order-wrap padding-reduced">
                        <div id="productorder-gage" class="gauge productorder-gage"></div>
                    </div>
                </div>
            </div>
        </div> -->
    
</div>
<script>
var url = "{{url('user/chart')}}";

$(document).ready(function(){
customerchart();
shopchart();
mallchart();
eventchart();
attractionchart();
purchasechart();
    // $.get(url, function(response){
    //     response.forEach(function(data){
    //         monthlab.push(data.monthlab);
    //         Labels.push(data.count);
    //         dataval.push(data.count);
    //     });
    //     var ctx = document.getElementById("canvas").getContext('2d');
    //         var myChart = new Chart(ctx, {
    //           type: 'bar',
    //           data: {
    //               labels:monthlab,
    //               datasets: [{
    //                   label: 'Customer Count',
    //                   backgroundColor: "rgba(220,220,220,0.5)",
    //                   data: dataval,
    //                   borderWidth: 1
    //               }]
    //           },
    //           options: {
    //               elements: {
    //                 rectangle: {
    //                         borderWidth: 2,
    //                         borderColor: 'rgb(0, 255, 0)',
    //                         borderSkipped: 'bottom'
    //                     }
    //                 },
    //                 responsive: true,
    //                 title: {
    //                     display: true,
    //                     text: 'Customer Signup Chart'
    //                 },
    //                 setDrawValues:false
    //           }
    //       });
    //   });
    // myChart.setDrawValues(false);


});
function customerchart()
{
    var monthlab = new Array();
    var Labels = new Array();
    var dataval = new Array();

    var gender = dob = "";
    if($("#gender").val() != "")
    {
        gender=$("#gender").val();
        $('#canvas').remove(); // this is my <canvas> element
        $('#customerchart').append('<canvas id="canvas" height="280" width="600"></canvas>');
    }
    if($("#dob").val() != "")
    {
        dob=$("#dob").val();
        $('#canvas').remove(); // this is my <canvas> element
        $('#customerchart').append('<canvas id="canvas" height="280" width="600"></canvas>');
    }
    $.ajax({
        url: url,
        method: 'get',
        data: {'gender':gender,'dob':dob},
        success: function(response){
            response.forEach(function(data){
            monthlab.push(data.monthlab);
            Labels.push(data.count);
            dataval.push(data.count);
        });
        var ctx = document.getElementById("canvas").getContext('2d');
            var myChart = new Chart(ctx, {
              type: 'bar',
              barValueSpacing : 15,
              barShowStroke : false,
              barBeginAtOrigin:true,
              barStrokeWidth:1,
              data: {
                  labels:monthlab,
                  datasets: [{
                      label: 'Customer Count',
                      backgroundColor: "rgba(220,220,220,0.5)",
                      data: dataval,
                      borderWidth: 1
                  }]
              },
              options: {
                  elements: {
                    rectangle: {
                            borderWidth: 2,
                            borderColor: 'rgb(0, 255, 0)',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Customer Signup Chart'
                    },
                      scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    },
              }
          });
        }
    });
}

function shopchart()
{
    var location = new Array();
    var Labels = new Array();
    var dataval = new Array();

    $.ajax({
        url: "{{url('user/shopchart')}}",
        method: 'get',
        data: {},
        success: function(response){
            response.forEach(function(data){
            location.push(data.location);
            Labels.push(data.count);
            dataval.push(data.count);
        });
        var ctx = document.getElementById("shopcanvas").getContext('2d');
            var myChart = new Chart(ctx, {
              type: 'bar',
              data: {
                  labels:location,
                  datasets: [{
                      label: 'Location Count',
                      backgroundColor: "rgba(220,220,220,0.5)",
                      data: dataval,
                      borderWidth: 1
                  }]
              },
              options: {
                  elements: {
                    rectangle: {
                            borderWidth: 2,
                            borderColor: 'rgb(0, 255, 0)',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Shop Chart'
                    },
                      scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    },
                    setDrawValues:false
              }
          });
        }
    });
}


function mallchart()
{
    var location = new Array();
    var Labels = new Array();
    var dataval = new Array();

    $.ajax({
        url: "{{url('user/mallchart')}}",
        method: 'get',
        data: {},
        success: function(response){
            response.forEach(function(data){
            location.push(data.location);
            Labels.push(data.count);
            dataval.push(data.count);
        });
        var ctx = document.getElementById("mallcanvas").getContext('2d');
            var myChart = new Chart(ctx, {
              type: 'bar',
              data: {
                  labels:location,
                  datasets: [{
                      label: 'Location Count',
                      backgroundColor: "rgba(220,220,220,0.5)",
                      data: dataval,
                      borderWidth: 1
                  }]
              },
              options: {
                  elements: {
                    rectangle: {
                            borderWidth: 2,
                            borderColor: 'rgb(0, 255, 0)',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Mall Chart'
                    },
                      scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    },
                    setDrawValues:false
              }
          });
        }
    });
}

function eventchart()
{
    var location = new Array();
    var Labels = new Array();
    var dataval = new Array();

    $.ajax({
        url: "{{url('user/eventchart')}}",
        method: 'get',
        data: {},
        success: function(response){
            response.forEach(function(data){
            location.push(data.location);
            Labels.push(data.count);
            dataval.push(data.count);
        });
        var ctx = document.getElementById("eventcanvas").getContext('2d');
            var myChart = new Chart(ctx, {
              type: 'bar',
              data: {
                  labels:location,
                  datasets: [{
                      label: 'Location Count',
                      backgroundColor: "rgba(220,220,220,0.5)",
                      data: dataval,
                      borderWidth: 1
                  }]
              },
              options: {
                  elements: {
                    rectangle: {
                            borderWidth: 2,
                            borderColor: 'rgb(0, 255, 0)',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Event Chart'
                    },
                      scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    },
                    setDrawValues:false
              }
          });
        }
    });
}

function attractionchart()
{
    var location = new Array();
    var Labels = new Array();
    var dataval = new Array();

    $.ajax({
        url: "{{url('user/attractionchart')}}",
        method: 'get',
        data: {},
        success: function(response){
            response.forEach(function(data){
            location.push(data.location);
            Labels.push(data.count);
            dataval.push(data.count);
        });
        var ctx = document.getElementById("attractioncanvas").getContext('2d');
            var myChart = new Chart(ctx, {
              type: 'bar',
              data: {
                  labels:location,
                  datasets: [{
                      label: 'Location Count',
                      backgroundColor: "rgba(220,220,220,0.5)",
                      data: dataval,
                      borderWidth: 1
                  }]
              },
              options: {
                  elements: {
                    rectangle: {
                            borderWidth: 2,
                            borderColor: 'rgb(0, 255, 0)',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Attraction Chart'
                    },
                      scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    },
                    setDrawValues:false
              }
          });
        }
    });
}

function purchasechart()
{
    var monthlab = new Array();
    var Labels = new Array();
    var dataval = new Array();

    $.ajax({
        url: "{{url('user/purchasechart')}}",
        method: 'get',
        data: {},
        success: function(response){
            response.forEach(function(data){
            monthlab.push(data.monthlab);
            Labels.push(data.totalamt);
            dataval.push(data.totalamt);
        });
        var ctx = document.getElementById("purchasecanvas").getContext('2d');
            var myChart = new Chart(ctx, {
              type: 'bar',
              data: {
                  labels:monthlab,
                  datasets: [{
                      label: 'Location Count',
                      backgroundColor: "rgba(220,220,220,0.5)",
                      data: dataval,
                      borderWidth: 1
                  }]
              },
              options: {
                  elements: {
                    rectangle: {
                            borderWidth: 2,
                            borderColor: 'rgb(0, 255, 0)',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Attraction Chart'
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    },
                    setDrawValues:false
              }
          });
        }
    });
}
</script>
@endsection
