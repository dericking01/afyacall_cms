@extends('layouts.app')

@push('before-styles')

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/datatables/media/css/dataTables.bootstrap4.css') }}">

@endpush

@push('after-scripts')
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/toast-master/js/jquery.toast.js') }}"></script>
    <script src="{{ asset('js/toastr.js')}}"></script>
    <script src="{{ asset('js/buttons.min.js') }}"></script>
    <script src="{{ asset('js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('js/jszip.min.js') }}"></script>
    <script src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/buttons.print.min.js') }}"></script>
    <script>
        $('#example23').DataTable({
            dom: 'Bfrtip',
            order: [
                [0, "desc"]
            ],
            pageLength: 100,
            buttons: [{
                    extend: 'csv',
                    titleAttr: 'Click to download as a CSV',
                    filename: 'Group Lists',
                },
                {
                    extend: 'excel',
                    titleAttr: 'Click to download as a Excel',
                    filename: 'Group Lists',
                }

            ],
        });
        $('.buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
    </script>
@endpush

@section('content')
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Groups</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">groups</li>
            </ol>
        </div>
        <div class="col-md-6 col-4 align-self-center">
            @can('push_enticement')
            <div class="col-md-12 col-4 align-self-center">
                <div class="dropdown float-right mr-2 hidden-sm-down">
                    <button class="btn btn-success" type="button"  data-toggle="modal" data-target="#importModal" data-backdrop="static" data-keyboard="false" ><i class="mdi mdi-plus-circle"></i>Create Group</button>
                </div>
            </div>
            @endcan

        </div>

    </div>

    <div class="row">
        <div class="col-12">
                 @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
            
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            <div class="card">
                <div id="showdatatable" class="card-body">

                    <h4 class="card-title">Group Lists</h4>
                    <div class="table-responsive">
                        <table id="example23" class="table table-hover table-striped table-bordered" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Group Name</th>
				    <th>Description</th>
                                    <th>Expire In</th>
                                    <th>Created by</th>
                                    <th width = 20%>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groups as $key => $group)
                                <tr data-entry-id="{{ $group->id }}">
                                        <td>{{ $no++ }}</td>
                                    <td>
                                        {{ $group->name ?? '' }}
                                    </td>
                                    <td>
                                        {{ $group->description ?? '' }}
                                    </td>
                                    <td>
                                        {{ $group->expire ?? '' }}
                                    </td>

                                    <td>
                                        {{ $group->user['name'] ?? '' }}
                                    </td>


                                    <td>
                                        <a class="btn btn-xs btn-info"  href="{{ route('admin.contact.showcontacts', $group->id) }}">
                                            View Contacts
                                        </a>

                                        <form action="{{ route('admin.contact.group.destroy', $group->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger"  value="Delete">
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


        <div  id="importModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Create Group</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
        
                        <form action="{{ route("admin.contact.group.store") }}" method="POST" enctype="multipart/form-data">
                            @csrf
    
                            <div class="mb-3">
                                <label for="recipient-name" class="control-label">Group Name</label>
                                <input class="form-control" name="groupname" id="groupname" required>
                            </div>
    

                            <div class="mb-3">
                                <label for="message-text" class="control-label">Description</label>
                                <textarea class="form-control" id="description" name="description"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="recipient-name" class="control-label">Group Retention</label>
                                <select class="form-control select2" name="retention" id="retention">
                                      <option value="1">Temporary</option>
                                      <option value="7">7 Days</option>
                                      <option value="30">30 Days</option>
                                      <option value="180">180 Days</option>
                                      <option value="360">360 Days</option>
                                </select>
                            </div>
    
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success">Submit</button>
                            </div>
                        </form>
        
                    </div>
        
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
    @endsection
