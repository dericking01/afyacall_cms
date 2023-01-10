@extends('layouts.app')
@section('content')
<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Users</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
            <li class="breadcrumb-item active">Users</li>
        </ol>
    </div>
    <div class="col-md-6 col-4 align-self-center">
        {{-- <button class="right-side-toggle waves-effect waves-light btn-info btn-circle btn-sm float-right ml-2"><i class="ti-settings text-white"></i></button> --}}
        <a class="btn float-right hidden-sm-down btn-success"  href="{{ route("admin.users.create") }}"><i class="mdi mdi-plus-circle"></i> Create User</a>
       
    </div>
</div>

<div class="row" id="foot">
    <div class="col-12">

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h4 class="card-title"> Users list</h4>
                <h6 class="card-subtitle"></h6>

                <div class="table-responsive">
                    <table id="demo-foo-addrow" class="table table-bordered table-hover" data-paging="true" data-paging-size="10">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Last Login</th>
                                    <th>Ip Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                        <tbody>
                        @foreach($users as $key => $user)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>
                                    {{ $user->name ?? '' }}
                                </td>
                                <td>
                                    {{ $user->email ?? '' }}
                                </td>
                                <td>
                                    {{ $user->mobile ?? '' }}
                                </td>
                                <td>
                                    @foreach($user->roles as $key => $item)
                                        <span class="label label-info">{{ $item->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    {{ $user->last_login_at ?? '' }}
                                </td>

                                <td>
                                    {{ $user->last_login_ip ?? '' }}
                                </td>
                                <td>
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.users.edit', $user->id) }}">
                                        <i class="fas fa-edit"></i>  edit
                                    </a>

                                    
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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