        <title>Export data to excel</title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" />
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
        <style type="text/css">
            .box{
                width:600px;
                margin:0 auto;
                border:1px solid #ccc;
            }
        </style>
    </head>
    <br />
    <div class="container">
        <h3 align="center">Export data to excel</h3><br />
        <div align="center">
        <a href="{{ route('export_excel.excel')}}" class="btn btn-sucess">Export to Excel</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td>Id</td>
                    <td>Name</td>
                    {{-- <td>No Of Products</td> --}}
                    <td>Category</td>
                    {{-- <td>No Of Presence</td> --}}
                    <td>Total Earnings</td>
                </tr>
                @foreach($brand_data as $brand)
                <tr>
                    <td>{{ $brand->unique_id }}</td>
                    <td>{{ $brand->name }}</td>
                    {{-- <td>{{ $brand->noofproducts }}</td> --}}
                    <td>{{$brand->category_id}}</td>
                    {{-- <td>{{$brand->noofpresence}}</td> --}}
                    <td>{{$brand->commission}}</td>
                </tr>
                @endforeach
            </table>
    </div>
</html>