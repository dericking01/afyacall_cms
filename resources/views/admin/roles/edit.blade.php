@extends('layouts.app')

@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Role</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route("admin.home") }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a  href="{{ route("admin.roles.index") }}">Roles</a></li>
            <li class="breadcrumb-item active">Update Role</li>
        </ol>
    </div>
  
</div>

<!-- Row -->
<div class="row">

    <div class="col-lg-12">

        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
        @endif

        <div class="card card-outline-info">
            <div class="card-header">
                <h4 class="mb-0 text-white">Role Update form</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.roles.update', $role->id) }}" method="PATCH" enctype="multipart/form-data">
                    @csrf
                    <div class="form-body">
                       
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Name  <span class="text-danger">*</span> </label>
                                <input type="text" id="name" name="name" class="form-control" required value={{ $role->name}}>
                            </div>
                        </div>
                        <!--/span-->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Permission  <span class="text-danger">*</span> </label>
                                <select name="permission[]" id="permission" class="form-control select2" multiple="multiple" required>
                                    @foreach($permissions as $id => $permission)
                                            <option value="{{ $id }}" {{ (in_array($id, old('permission', [])) || isset($role) && $rolePermissions) ? 'selected' : '' }}>{{ $permission->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
    
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Update</button>
                            <button type="button" class="btn btn-inverse">Cancel</button>
                        </div>
                    </div>
                   
                </form>
            </div>
        </div>
    </div>
</div>


@endsection