<div class="row mt-4">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
            <h4 class="card-title" style="float:left">{{$title}}-Brand Merchant</h4>
            <!-- <select name="per_page" id="per_page" style="float:right;">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            </select> -->
            

            <div class="box-header ">
                    @if (session()->has('success'))
                    <h4 class="mess" style="text-align: center; color: green;">{{ session('success') }}</h4>
                    @endif
                    @if (session()->has('error'))
                    <h4 class="mess" style="text-align: center; color: red;">{{ session('error') }}</h4>
                    @endif
                </div>
            <div class="table-responsive">
                <table class="table table-hover" id="brand_merchanttableData">
                <thead>
                    <tr>
                    <th>Image</th>
                    <th>@sortablelink('id')</th>
                    <th>@sortablelink('name')</th>
                    <th>@sortablelink('email')</th>
                    <th>Gender</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>@sortablelink('created_at','Created on')</th>
                    <!-- <th>@sortablelink('created_at','Created on',['filter' => 'active, visible'], ['class' => 'btn btn-block', 'rel' => 'nofollow'])</th> -->
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($brandmerchantdata) && $brandmerchantdata->count() > 0)
                        @foreach($brandmerchantdata as $key => $value)
                    <tr>
                    <td>
                        @if($value->profile_pic!= null)
                            <img src="{{asset('public/upload/')}}{{'/'.$value->profile_pic}}" alt="">
                        @else

                        @endif
                        </td>
                    <td>{{$value->unique_id}}</td>
                    <td>{{$value->name}}</td>
                    <td>{{$value->email}}</td>
                    <td>{{$value->gender}}</td>
                    <td>{{$value->role}}</td>
                    <td> @if($value->status == '1') 
                            Inactive
                        @else
                            Active
                            @endif
                        </td>
                    <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                    <td><a class="edit open_modal" data-toggle="modal" data-target="#editUser{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                    <a class="delete" onclick="return confirm('Are you sure you want to delete this User?')" href="{{route('user.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                    </tr>
                    <!-- Edit Modal HTML Markup -->
                    <div id="editUser{{$value->id}}" class="modal fade">
                        <div class="modal-dialog  modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title">Edit User</h1>
                                </div>
                                <div class="modal-body">
                                <p class="statusMsg"></p>
                                    <form name="adduserform" id="edituserform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
                                        @csrf
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="exampleInputName">Name</label>
                                            <input type="text" class="form-control" required id="fullname" value="{{$value->name}}" name="name" placeholder="Name">
                                            <input type="hidden" name="id" value="{{$value->id}}">
                                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                            <span class="text-danger">
                                                <strong id="name-error{{$value->id}}"></strong>
                                            </span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="exampleInputEmail1">Email id</label>
                                            <input type="email" class="form-control" required id="email" name="email" value="{{$value->email}}"  placeholder="Email">
                                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                            <span class="text-danger">
                                                <strong id="email-error{{$value->id}}"></strong>
                                            </span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="exampleSelectGender">Gender</label>
                                            <select class="form-control" name="gender" id="exampleSelectGender">
                                            <option value="male" {{ $value->gender == 'male' ? 'selected' : ''}}>Male</option>
                                            <option value="female" {{ $value->gender == 'female' ? 'selected' : ''}}>Female</option>
                                            </select>
                                        </div>
                                    
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="exampleInputRole">Role</label>
                                            <select class="form-control" id="role" name="role">
                                                <option value="" selected="">Role</option>
                                                <option value="admin" {{ $value->role == 'admin' ? 'selected' : ''}} >Admin</option>
                                                <option value="brand_merchant" {{ $value->role == 'brand_merchant' ? 'selected' : ''}}>Brand Merchant</option>
                                                <option value="property_admin" {{ $value->role == 'property_admin' ? 'selected' : ''}}>Property Admin</option>
                                                <option value="customer" {{ $value->role == 'customer' ? 'selected' : ''}}>Customer</option>
                                            </select>
                                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                            <span class="text-danger">
                                                <strong id="role-error{{$value->id}}"></strong>
                                            </span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="exampleInputStatus">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="" selected="">Status</option>
                                                <option value="0" {{ $value->status == '0' ? 'selected' : ''}}>Active</option>
                                                <option value="1" {{ $value->status == '1' ? 'selected' : ''}}>Inactive</option>
                                            </select>
                                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                            <span class="text-danger">
                                                <strong id="status-error{{$value->id}}"></strong>
                                            </span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="exampleSelectPhoto">Photo</label>
                                            <input type="file" name="profile_pic" class="file-upload-default">
                                            <div class="input-group col-xs-12">
                                                <input type="text" value="{{ $value->profile_pic != null ? $value->profile_pic : ''}}" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                                <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="exampleInputEmail1">Date of Birth</label>
                                            <input type="date" required class="form-control" id="dob" name="dob" placeholder="dob" value="{{ $value->dob != null ? $value->dob : ''}}">
                                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                            <span class="text-danger">
                                                <strong id="dob-error{{$value->id}}"></strong>
                                            </span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="exampleInputPassword">Contact</label>
                                            <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact" value="{{ $value->mobile != null ? $value->mobile : ''}}">
                                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                            <span class="text-danger">
                                                <strong id="mobile-error{{$value->id}}"></strong>
                                            </span>
                                        </div>               
                                    </div>
                                    <button type="button" class="btn btn-primary mr-2 editUserSubmit" data-id="{{$value->id}}" id="addUserSubmit">Submit</button>
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button> 
                                    </form>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- edit /.modal -->
                    @endforeach
                    @else
                    <tr>
                    <td colspan="9">No Records Found</td>
                    </tr>
                    @endif


                </tbody>
                </table>

            </div>
                <div id="brand_merchantpaging">
                {{ $brandmerchantdata->appends(['role' => 'brand_merchant'])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>