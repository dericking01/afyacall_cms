@extends('layouts.app')

@push('before-styles')

    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/plugins/datatables/media/css/dataTables.bootstrap4.css') }}">

@endpush

@push('after-scripts')

    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
    <!-- end - This is for export functionality only -->
    <script src="{{ asset('js/buttons.min.js') }}"></script>
    <script src="{{ asset('js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('js/jszip.min.js') }}"></script>
    <script src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/buttons.print.min.js') }}"></script>
    <script>
        $('#transactiontable').DataTable({
            dom: 'Bfrtip',
            order: [
                [2, "desc"]
            ],
            pageLength: 100,
            buttons: [{
                    extend: 'csv',
                    titleAttr: 'Click to download as a CSV',
                    filename: 'Transactions Lists',
                },
                {
                    extend: 'excel',
                    titleAttr: 'Click to download as a Excel',
                    filename: 'Transactions Lists',
                }

            ]
        });
        $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
    </script>

@endpush

@section('content')



    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Transaction</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                <li class="breadcrumb-item active">Transaction</li>
            </ol>
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Transaction Lists</h4>
                    <div class="table-responsive m-t-40">
                        <table id="transactiontable" class="table table-hover table-striped table-bordered" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th>Msisdn</th>
                                    <th>Amount (Tshs)</th>
                                    <th>Transaction Date</th>
                                    <th>Status</th>
                                    <th>Product</th>
                                    <th>Method</th>
                                    <th>Response</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($transactions as $key => $transaction)
                                    <tr data-entry-id="{{ $transaction->id }}">

                                        <td>
                                            {{ $transaction->customer['msisdn'] ?? '' }}
                                        </td>
                                        <td>
                                            {{ number_format($transaction->amount_IN, 2 ?? '') }}
                                        </td>

                                        <td>
                                            {{ $transaction->created_at ?? '' }}
                                        </td>

                                        <td>
                                            @if ($transaction->status == '1')
                                                <span class="badge badge-pill badge-success">success</span>
                                            @elseif ($transaction->status == '-1')
                                                <span class="badge badge-pill badge-warning">Waiting..</span>
                                            @elseif ($transaction->status == '0')
                                                <span class="badge badge-pill badge-danger">Failed</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ $transaction->product['name'] ?? '' }}
                                        </td>

                                        <td>
                                            {{ $transaction->currency ?? '' }}
                                        </td>

                                        <td>
                                            {{ $transaction->response ?? '' }}
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                        {!! $transactions->links() !!}
                    </div>
                </div>
            </div>
        </div>
    @endsection

