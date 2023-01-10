@extends('layouts.app')

@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Permission</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route("admin.home") }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a  href="{{ route("admin.permissions.index") }}">Permissions</a></li>
            <li class="breadcrumb-item active">Create New Permission</li>
        </ol>
    </div>
  
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
        <form action="{{ route("admin.permissions.store") }}" method="post" enctype="multipart/form-data" >   
             {{ csrf_field() }}
            <div class="row" >
                <div class="col-xlg-10 col-lg-8 col-md-7">
                    <div class="card-body">
                        <h3 class="card-title">Create New Permission</h3>
                       
                        <div class="form-group">
                            <input class="form-control" placeholder="Permission"  id="title" name="title" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success mt-3"><i class="fa fa-envelope-o"></i> Save</button>
                        <button class="btn btn-inverse mt-3"><i class="fa fa-times"></i> Discard</button>
                    </div>
                </div>
            </div>
         </form>
        </div>
    </div>
</div>

@endsection