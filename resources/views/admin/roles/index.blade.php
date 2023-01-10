@extends('layouts.app')
@section('content')


<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Roles</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
            <li class="breadcrumb-item active">Roles</li>
        </ol>
    </div>
    <div class="col-md-6 col-4 align-self-center">

        <a class="btn float-right hidden-sm-down btn-success" href="{{ route("admin.roles.create") }}"><i class="mdi mdi-plus-circle"></i> Create Role</a>
       
    </div>
</div>

<div class="row" id="foot">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Roles list</h4>
                <h6 class="card-subtitle"></h6>

                <div class="table-responsive">

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <table id="demo-foo-addrow" class="table table-bordered m-t-30 table-hover contact-list" data-paging="true" data-paging-size="7">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                    
                            @foreach($roles as $key => $role)
                            <tr >
                                <td>
                                   {{ $no++ }}
                                </td>
                                <td>{{ $role->name }}</td>
                              
                                <td>
                                       
                                            <a class="btn btn-xs btn-info" href="{{ route('admin.roles.edit', $role->id) }}"><i class="mdi mdi-plus-circle"></i>
                                                edit
                                            </a>
                                  
                                       
                            
       
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger" value="delete">
                                        </form>
                            
                                </td>
    
                            </tr>
                        @endforeach

                      
                      
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection