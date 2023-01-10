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
            buttons: [
                'csv', 'excel'
            ]
        });
        $('.buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
    </script>
@endpush

@section('content')
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Campaigns</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Campaign</li>
            </ol>
        </div>
        <div class="col-md-6 col-4 align-self-center">
            @can('push_enticement')
            <a class="btn float-right hidden-sm-down btn-success"  href="{{ route('admin.contact.compaingservice') }}"><i
                    class="mdi mdi-plus-circle"></i>Create Campaign </a>
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

                    <h4 class="card-title">Campaign Services</h4>
                    <div class="table-responsive">
                        <table id="example23" class="table table-hover table-striped table-bordered" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Campaign Name</th>
                                    <th>Groups</th>
                                    <th>Message</th>
                                    <th>Total Contacts</th>
                                    <th>Uploaded by</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($campaigns as $key => $campaign)
                                <tr>
                                    <td>
                                        {{ $no++ }}
                                    </td>
                                    <td>
                                        {{ $campaign->name ?? '' }}
                                    </td>
                                    <td>
                                        @foreach (explode(',',$campaign->uploadvia) as $value)
                                          <span class="badge badge-pill badge-primary">{{$value}}</span>
                                        @endforeach
                                    </td>

                                    <td>
                                        {{ $campaign->message ?? '' }}
                                    </td>

                                    <td>
                                        {{ $campaign->delivery ?? '' }}
                                    </td>

                                    <td>
                                        {{ $campaign->user['name'] ?? '' }}
                                    </td>

                                    <td>
                                        <a class="btn btn-xs btn-info"  href="{{ route('admin.contact.campaigndetails', $campaign->id) }}">
                                            View
                                        </a>
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

