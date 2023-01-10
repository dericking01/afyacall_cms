@extends('layouts.app')

@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Content Type</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route("admin.home") }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a  href="{{ route("admin.contentstype.index") }}">All Content Type</a></li>
            <li class="breadcrumb-item active">Create Content Type</li>
        </ol>
    </div>
  
</div>

<!-- Row -->
<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <h4 class="mb-0 text-white">Content Type Creation form</h4>
            </div>
            <div class="card-body">
                <form action="{{ route("admin.contentstype.store") }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-body">
                        <div class="row pt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Name <span class="text-danger">*</span> </label>
                                    <input type="text" id="name" name="name" class="form-control" required>
                                </div>
                            </div>
                           
                        </div>
                        <!--/row-->
                        <div class="row">
                          
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Description</label>
                                        <div class="controls">
                                            <textarea class="textarea_editor form-control" id="description" name="description" rows="3" placeholder="Enter text ..." ></textarea>
                                        </div>
                                        
                                    </div>
                                </div>
                                             
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                        <button type="button" class="btn btn-inverse">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection