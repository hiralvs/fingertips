<div class="row mt-4">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
            <h4 class="card-title" style="float:left">{{$title}}-Shop</h4>
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
                <table class="table table-hover" id="malltableData">
                <thead>
                    <tr>
                    <th>@sortablelink('id')</th>
                    <th>Image</th>
                    <th>@sortablelink('name')</th>
                    <th>@sortablelink('location')</th>
                    <th>@sortablelink('openinghrs','Opening Hours')</th>
                    <th>@sortablelink('contact','Contact Info')</th>
                    <th>@sortablelink('type','Mall Type')</th>
                    <th>@sortablelink('','Mall Admin')</th>
                    <th>@sortablelink('created_at','Created On')</th>
                    <th>@sortablelink('','Created By')</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($shopdata) && $shopdata->count() > 0)
                        @foreach($shopdata as $key => $value)
                    <tr>
                        <td>{{$value->unique_id}}</td>
                    <td>
                        @if($value->image!= null)
                            <img src="{{asset('public/upload/malls')}}{{'/'.$value->image}}" alt="">
                        @else

                        @endif
                    </td>
                    <td>{{$value->name}}</td>
                    <td>{{$value->location}}</td>
                    <td>{{$value->openinghrs}}</td>
                    <td>{{$value->contact}}</td>
                    <td>{{$value->type}}</td>
                    <td>{{$value->propertyadmin}}</td>
                    <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                    <td>{{$value->created_by}}</td>
                    <td><a class="edit open_modal" data-toggle="modal" data-id="{{$value->id}}" data-target="#editShopsandmalls{{$value->id}}" ><i class="mdi mdi-table-edit"></i></a> 
                    <a class="delete" onclick="return confirm('Are you sure you want to delete this Shop?')" href="{{route('shopsmalls.delete', $value->id)}}"><i class="mdi mdi-delete"></i></a> </td>
                    </tr>
                    <!-- Edit Modal HTML Markup -->
                            <div id="editShopsandmalls{{$value->id}}" class="modal fade">
                                <div class="modal-dialog  modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title">Edit Shop</h1>
                                        </div>
                                        <div class="modal-body">
                                        <p class="statusMsg"></p>
                                            <form name="editShopsmalls" id="editShopsmallsform{{$value->id}}" role="form" method="POST" enctype= "multipart/form-data">
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
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="image-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputName">Name</label>
                                                    <input type="text" required class="form-control" Re id="fullname" value="{{$value->name}}" name="name" placeholder="Name">
                                                    <input type="hidden" name="id" value="{{$value->id}}">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="name-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputEmail1">Location</label>
                                                    <input type="email" required class="form-control" id="location{{$value->id}}" value="{{$value->location}}" name="location" placeholder="location">
                                                    <input type="hidden" name="latitude" id="lat{{$value->id}}" value="{{$value->latitude}}">
                                                    <input type="hidden" name="longitude" id="long{{$value->id}}" value="{{$value->longitude}}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputPassword">Opening Hours</label>
                                                    <input type="text" class="form-control timepicker" value="{{$value->openinghrs}}"  id="openinghrs" name="openinghrs" placeholder="Opening Houres">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="openinghrs-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectGender">Closing Hours</label>
                                                    <input type="text" class="form-control timepicker" value="{{$value->closinghrs}}" id="closinghrs" name="closinghrs" placeholder="Closing Hours">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="closinghrs-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>                 
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectGender">Contact Info</label>
                                                    <input type="text" class="form-control" id="contact" value="{{$value->contact}}" name="contact" placeholder="Contact">
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="contact-error{{$value->id}}"></strong>
                                                    </span>
                                                </div> 
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputRole">Property Admin</label>
                                                    <select class="form-control" id="property_admin" name="property_admin">
                                                    <option value="">--Select--</option>
                                                    @if(!empty($property_admin) && $property_admin->count() > 0)
                                                        @foreach($property_admin as $key => $pd)
                                                            <option value="{{$pd->id}}" {{ $value->property_admin_user_id == $pd->id ? 'selected' : ''}} >{{$pd->name}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="property_admin-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Filter</label>
                                                    <select class="form-control category_id" multiple name="filter[]" id="filter">
                                                    <!-- <option value="">--Select--</option> -->
                                                    @if(!empty($category) && $category->count() > 0)
                                                        @foreach($category as $key => $cat)
                                                        <option value="{{$cat->id}}"  {{ in_array($cat->id,explode(",",$value->category_id))  ? 'selected' : ''}}>{{$cat->category_name}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="category-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleSelectPhoto">Area</label>
                                                    <select class="form-control " id="area" name="area">
                                                    <option value="">--Select--</option>
                                                    @if(!empty($area) && $area->count() > 0)
                                                        @foreach($area as $key => $avalue)
                                                            <option value="{{$avalue->id}}" {{ $value->area_id == $avalue->id ? 'selected' : ''}}>{{$avalue->area_name}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="area-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputRole">Featured Mall</label>
                                                    <select class="form-control " id="featured_mall" name="featured_mall">
                                                        <option value="">--Select--</option>
                                                        <option value="yes" {{ $value->featured_mall == 'yes' ? 'selected' : ''}}>Yes</option>
                                                        <option value="no" {{ $value->featured_mall == 'no' ? 'selected' : ''}}>No</option>
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="featured_mall-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="exampleInputStatus">Layer</label>
                                                    <select class="form-control" id="layer" name="layer">
                                                        <option value="">--Select--</option>
                                                        <option value="mall" {{ $value->type == 'mall' ? 'selected' : ''}}>Mall</option>
                                                        <option value="shop" {{ $value->type == 'shop' ? 'selected' : ''}}>Shop</option>
                                                    </select>
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    <span class="text-danger">
                                                        <strong id="type-error{{$value->id}}"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <textarea class="form-control ckeditor" id="description{{$value->id}}" name="desc">{{$value->description}}</textarea>
                                                </div>
                                            </div>
                                        <button type="button" class="btn btn-primary mr-2 editMallsSubmit" data-id="{{$value->id}}" id="editAreaSubmit">Submit</button>
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
                        <div id="paging">
                        {{ $shopdata->appends(['type' => 'shop'])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!--admin tabcontent ends -->