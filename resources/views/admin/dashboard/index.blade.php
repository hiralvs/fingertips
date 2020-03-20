
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
                        <h2 class="text-info font-weight-bold">244</h2>
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
                        <h2 class="text-warning font-weight-bold">3259</h2>
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
                        <h2 class="text-secondary font-weight-bold">586</h2>
                    </div>
                </div>
                <canvas id="orderRecieved"></canvas>
                <div class="line-chart-row-title">Revenue Generated</div>
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
                <h4 class="card-title">Customer Signup</h4>
                <div class="card-body py-3 px-3">
                        {!! $usersChart->container() !!}
                    </div>
                    {!! $usersChart->script() !!}
            </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin  stretch-card">
            <div class="card">
            <div class="card-body">
                <h4 class="card-title">Orders Generated</h4>
                <canvas id="barChart"></canvas>
            </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 grid-margin   stretch-card">
            <div class="card">
            <div class="card-body">
                <h4 class="card-title">Top 5 Brands</h4>
                <canvas id="barChart"></canvas>
            </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
            <div class="card-body">
                <h4 class="card-title">Top 5 Malls</h4>
                <canvas id="barChart"></canvas>
            </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 grid-margin   stretch-card">
            <div class="card">
            <div class="card-body">
                <h4 class="card-title">Top 5 Events </h4>
                <canvas id="barChart"></canvas>
            </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
            <div class="card-body">
                <h4 class="card-title">Top 5 Attractions</h4>
                <canvas id="barChart"></canvas>
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
$(document).ready(function(){
     var data = {
    labels: ["2013", "2014", "2014", "2015", "2016", "2017"],
    datasets: [{
      label: '# of Votes',
      data: [10, 19, 3, 5, 2, 3],
      backgroundColor: [
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)'
      ],
      borderColor: [
        'rgba(255,99,132,1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
      ],
      borderWidth: 1,
      fill: false
    }]
  };
  if ($("#barChart1").length) {
    var barChartCanvas = $("#barChart1").get(0).getContext("2d");
    // This will get the first returned node in the jQuery collection.
    var barChart = new Chart(barChartCanvas, {
      type: 'bar',
      data: data,
      options: options
    });
  }
});
</script>
@endsection
