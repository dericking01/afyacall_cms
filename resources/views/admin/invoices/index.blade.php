@extends('layouts.app')

@push('before-styles')

    <link rel="stylesheet" type="text/css" href="/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">

@endpush

@push('after-scripts')
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>

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

            buttons: [
                'csv', 'excel', 'pdf'
            ]
        });
        $('.buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
    </script>
@endpush

@section('content')
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Invoices</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Invoice</li>
            </ol>
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">

                <div id="showdatatable" class="card-body">

                    <h4 class="card-title">Invoice Lists</h4>
                    <div class="table-responsive">
                        <table id="example23" class="table table-hover table-striped table-bordered" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th>Invoice Number</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Amount</th>
                                    <th>status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($invoices as $key => $invoice)
                                    <tr data-entry-id="{{ $invoice->id }}">

                                        <td>
                                            {{ $invoice->invoice_reference ?? '' }}
                                        </td>
                                        <td>
                                            {{ $invoice->period_from ?? '' }}
                                        </td>

                                        <td>
                                            {{ $invoice->period_to ?? '' }}
                                        </td>

                                        <td>
                                            {{ number_format($invoice->total_amount, 2, '.', ',') }}
                                        </td>


                                        <td>
                                            @if ($invoice->status == 1)
                                                <span class="badge badge-pill badge-success">Paid</span>
                                            @else
                                                <span class="badge badge-pill badge-danger">not Paid</span>
                                            @endif
                                        </td>

                                        <td>

                                            @can('view_invoice')
                                                <a class="btn btn-xs btn-info"
                                                    href="{{ route('admin.invoice.show', $invoice->id) }}">
                                                    View Invoice
                                                </a>
                                            @endcan
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
