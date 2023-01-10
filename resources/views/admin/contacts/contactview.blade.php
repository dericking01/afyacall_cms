@extends('layouts.app')

@push('before-styles')

    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/plugins/datatables/media/css/dataTables.bootstrap4.css') }}">

@endpush

@push('after-scripts')
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/toast-master/js/jquery.toast.js') }}"></script>
    <script src="{{ asset('js/toastr.js') }}"></script>
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
            pageLength: 100,
            buttons: [{
                    extend: 'csv',
                    titleAttr: 'Click to download as a CSV',
                    filename: 'Contacts Lists',
                },
                {
                    extend: 'excel',
                    titleAttr: 'Click to download as a Excel',
                    filename: 'Contacts Lists',
                }

            ],
        });
        $('.buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
    </script>

@endpush

@section('content')
    <div class="row page-titles">
	<div class="col-md-6 col-8 align-self-center">
  <h3 class="text-themecolor mb-0 mt-0">Contact List - {{ $group->name }}</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.contact.groups') }}">Groups</a></li>
                <li class="breadcrumb-item active">Contact list</li>
            </ol>
        </div>

        <div class="col-md-6 col-4 align-self-center">
            @can('push_enticement')
            <a class="btn float-right hidden-sm-down btn-success" style="margin:5px;" href="{{ route('admin.contact.groups.import', $id) }}"><i
                    class="mdi mdi-plus-circle"></i>Import Contacts </a>
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


                    <h4 class="card-title">Contacts List </h4>
                    <div class="row mt-12">
                        <div class="col-4">
                            <div class="alert alert-info">
                                <h4 class="text-info"><i class="fa fa-exclamation-circle"></i> Total Numbers </h4>
                                {{ $contactCounts }}
                            </div>
                        </div>

                        <div class="col-4">
                            @if (isset($group->previous))
                                <a href="{{ route('admin.contact.showcontacts', $group->previous->id) }}">
                                    <div> Previous Group</div>
                                    <p>{{ $group->previous->name }}</p>
                                </a>
                            @endif
                        </div>

                        <div class="col-4">
                            @if (isset($group->next))
                                <a href="{{ route('admin.contact.showcontacts', $group->next->id) }}">
                                    <div>Next Group</div>
                                    <p>{{ $group->next->name }}</p>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="example23" class="table table-hover table-striped table-bordered" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>msisdn</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contacts as $key => $contact)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>
                                            {{ $contact->msisdn ?? '' }}
                                        </td>

                                        <td>
                                            
                                            <form action="{{ route('admin.contact.group.contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="submit" class="btn btn-xs btn-danger"  value="Remove Contact">
                                            </form>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {!! $contacts->links() !!}
                    </div>
                </div>
            </div>
        </div>

    @endsection
