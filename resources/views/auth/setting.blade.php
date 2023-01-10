@extends('layouts.app')

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h4 class="text-themecolor mb-0 mt-0">Account Settings</h4>

        </div>

    </div>

    <!-- Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="mb-0 text-white">General Setting</h4>
                </div>

    
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Username <a class="get-code" data-toggle="collapse" href="#tt1" aria-expanded="true"><i class="fa fa-exclamation-circle" title="Your username was set when you first created your account and cannot be changed. Please ensure that your password is changed periodically to ensure the highest level of security." data-toggle="tooltip"></i></a>
                                </label>
                                <input type="text" id="name" name="name" class="form-control" value={{$user->name}} readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label">update<span class="text-info"></span>
                            </label>
                            <div class="form-group">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">Change Password </button>
                            </div>
                        </div>
                    </div>
                    <br>
                    <form action="#" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Full Name<span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="name" name="name" class="form-control" value={{$user->name}} required>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Contact 1 <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="shortcode" value={{$user->mobile}} name="shortcode" class="form-control" required>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Email Address<span
                                                class="text-danger">*</span> </label>
                                            <input type="text" name="product_ID"  value={{$user->email}}  class="form-control" required >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Last Login At
                                        </label>
                                        <div class="controls">
                                            <input type="text" name="price" value={{$user->last_login_at}}  class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Update Setting</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel1">Change Password</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Current Password:</label>
                            <input type="password" class="form-control" id="recipient-name1">
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">New Password:</label>
                            <input type="password" class="form-control" id="recipient-name1">
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">Confirm Password:</label>
                            <input type="password" class="form-control" id="recipient-name1">
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success">Change</button>
                </div>
            </div>
        </div>
    </div>


@endsection
