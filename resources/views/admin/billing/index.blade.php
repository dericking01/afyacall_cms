@extends('layouts.app')

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h4 class="text-themecolor mb-0 mt-0">Billing Details</h4>

            To view our billing guides Click Here.

        </div>

    </div>

    <!-- Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="mb-0 text-white">Edit Billing Details</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.billing.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Company Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="company" name="company" class="form-control" value="{{$billing->company  ?? '' }}" required>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Address 1 <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="address_one" name="address_one" class="form-control" value="{{$billing->address_one  ?? '' }}" required>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Registration Number<span
                                                class="text-danger">*</span> </label>
                                        <div class="controls">
                                            <input type="text" name="register_number" class="form-control" value="{{$billing->register_number  ?? '' }}"
                                              >
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Address 2
                                        </label>
                                        <div class="controls">
                                            <input type="text" name="address_two" class="form-control" value="{{$billing->address_two  ?? '' }}" required
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Registered for Tanzania VAT?<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control select2" name="vatregister" id="vatregister">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                        
                                  </select>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">City name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="state" name="state" class="form-control" value="{{$billing->address_two  ?? '' }}">
                                </div>
                            </div>
                            <!--/span-->
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">VAT number</label>
                                    <input type="text" id="vatnumber" name="vatnumber" class="form-control" value="{{$billing->vatnumber  ?? '' }}">
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">State/Province
                                    </label>
                                    <input type="text" id="state" name="state" class="form-control" value="{{$billing->state  ?? '' }}">
                                </div>
                            </div>
                            <!--/span-->
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Invoice email  
                                    </label>
                                    <input type="text" id="invoice_email" name="invoice_email" class="form-control" value="{{$billing->invoice_email  ?? '' }}">
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Zip Code
                                    </label>
                                    <input type="text" id="zipcode" name="zipcode" class="form-control" value="{{$billing->zipcode  ?? '' }}">
                                </div>
                            </div>
                            <!--/span-->
                        </div>

                        <div class="row">
                            
                            <!--/span-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Country of residence  <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control select2" name="country" id="country">
                                          <option value="Tanzania">Tanzania</option>
                                    </select>
                                   
                                </div>
                            </div>
                            <!--/span-->
                        </div>




                        <div class="form-actions">
                            <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Update Billing
                                Details</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
