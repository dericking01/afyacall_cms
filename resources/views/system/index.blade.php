@extends('layouts.app')
@push('before-styles')
<link rel="stylesheet" type="text/css" href="/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">
@endpush

@push('after-scripts')
<script src="/assets/plugins/datatables/datatables.min.js"></script>
<!-- start - This is for export functionality only -->
<script src="{{asset('js/buttons.min.js')}}"></script>
<script src="{{asset('js/buttons.flash.min.js')}}"></script>
<script src="{{asset('js/jszip.min.js')}}"></script>
<script src="{{asset('js/pdfmake.min.js')}}"></script>
<script src="{{asset('js/vfs_fonts.js')}}"></script>
<script src="{{asset('js/buttons.html5.min.js')}}"></script>
<script src="{{asset('js/buttons.print.min.js')}}"></script>
<script src="/assets/plugins/toast-master/js/jquery.toast.js"></script>
<script src="{{asset('js/toastr.js')}}"></script>
<script>

    $('#subscriptiontable').DataTable({
        dom: 'Bfrtip',
        buttons: [
             'csv', 'excel', 'pdf'
        ]
    });
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
</script>

<script>
    @if ($message = Session::get('success'))
            $.toast({
                heading: 'Successfully created backup!',
                text: 'You have successfully created backup',
                position: 'top-right',
                loaderBg:'#ff6849',
                icon: 'success',
                hideAfter: 3500, 
                stack: 6
            });
    @endif
</script>

@endpush

@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Backups</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
            <li class="breadcrumb-item active">Backup</li>
        </ol>
    </div>
    @can('users_manage')
    <div class="col-md-6 col-4 align-self-center">
        <a class="btn float-right hidden-sm-down btn-success"  href="{{ route("admin.backup.create") }}"><i class="mdi mdi-plus-circle"></i> Create Backup</a>
    </div>
    @endcan
</div>

<div class="row">
    <div class="col-12">
      
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Backup Lists</h4>
                <div class="table-responsive m-t-40">
                    <table id="subscriptiontable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>File Name</th>
                            <th>File Size</th>
                            <th>Created Date</th>
                            <th>Created Age</th>
                            <th>Action</th>
                         
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                        <tr>
                            <td>{{ $backup['file_name'] }}</td>
                            <td>{{ \App\Http\Controllers\Admin\BackupController::humanFilesize($backup['file_size']) }}</td>
                            <td>
                                {{ date('F jS, Y, g:ia (T)',$backup['last_modified']) }}
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($backup['last_modified'])->diffForHumans() }}
                            </td>
                            <td class="text-right">
                                <a class="btn btn-success"
                                   href="{{ route('admin.backup.download',$backup['file_name']) }}"><i
                                        class="fa fa-cloud-download"></i> Download</a>
                                <a class="btn btn-danger" onclick="return confirm('Do you really want to delete this file')" data-button-type="delete"
                                   href="{{ url('backup/delete/'.$backup['file_name']) }}"><i class="fa fa-trash-o"></i>
                                    Delete</a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection