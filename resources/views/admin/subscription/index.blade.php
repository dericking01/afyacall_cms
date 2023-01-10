@extends('layouts.app')
@push('before-styles')
    <link rel="stylesheet" type="text/css" href="/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">
@endpush

@push('after-scripts')
    <script src="/assets/plugins/datatables/datatables.min.js"></script>
    <!-- start - This is for export functionality only -->
    <script src="{{ asset('js/buttons.min.js') }}"></script>
    <script src="{{ asset('js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('js/jszip.min.js') }}"></script>
    <script src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/buttons.print.min.js') }}"></script>
    <script>
        $('#subscriptiontable').DataTable({
            dom: 'Bfrtip',
            order: [
                [2, "desc"]
            ],
            pageLength: 25,
            buttons: [{
                    extend: 'csv',
                    titleAttr: 'Click to download as a CSV',
                    filename: 'Subscription Reports',
                },
                {
                    extend: 'excel',
                    titleAttr: 'Click to download as a Excel',
                    filename: 'Subscription Reports',
                }

            ]
        });
        $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
    </script>

@endpush

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Subscription</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                <li class="breadcrumb-item active">Subscription</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Subscription Lists</h4>
                    <div class="table-responsive m-t-40">
                        <table id="subscriptiontable" class="display nowrap table table-hover table-striped table-bordered"
                            cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Msisdn</th>
                                    <th>Product</th>
                                    <th>Started At</th>
                                    <th>End At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subscriptions as $key => $subscription)
                                    <tr data-entry-id="{{ $subscription->id }}">

                                        <td>
                                            {{ $subscription->msisdn }}
                                        </td>

                                        <td>
                                            {{ $subscription->name }}
                                        </td>

                                        <td>
                                            {{ $subscription->starts_at ?? '' }}
                                        </td>

                                        <td>
                                            {{ $subscription->ends_at ?? '' }}
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
