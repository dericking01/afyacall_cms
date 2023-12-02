@extends('layouts.app')
@push('before-styles')
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/plugins/datatables/media/css/dataTables.bootstrap4.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
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
            searching: false,
            order: [
                [6, "desc"]
            ],
            pageLength: 100,
            buttons: [{
                    extend: 'csv',
                    titleAttr: 'Click to download as a CSV',
                    filename: 'Customers Reports',
                },
                {
                    extend: 'excel',
                    titleAttr: 'Click to download as a Excel',
                    filename: 'Customers Reports',
                }

            ]
        });
        $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
    </script>
@endpush

@section('content')
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Customers</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                <li class="breadcrumb-item active">Customers</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Customer Lists</h4>
                    <form action="{{ route('admin.customer-search') }}" method="post" role="search">
                        @csrf
                        <div class="row">
                            <div class="col-10">
                                <input type="text" placeholder="Search.." name="q" class="form-control">
                            </div>

                            <div class="col-2">
                                <button type="submit" class="btn btn-primary">Search Number <i
                                        class="fa fa-search fa-sm"></i></button>
                            </div>
                        </div>

                    </form>
                    <br>
                    <div class="table-responsive m-t-40">
                        <table id="subscriptiontable" class="display nowrap table table-hover table-striped table-bordered"
                            cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Msisdn</th>
                                    <th>IVR</th>
                                    <th>SMS</th>
                                    <th>Doctor</th>
                                    <th>D-Sub</th>
                                    <th>Keyword</th>
                                    <th>Updated At</th>
                                    <th>Actions</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $key => $customer)
                                    <tr data-entry-id="{{ $customer->id }}">

                                        <td>
                                            {{ $customer->msisdn ?? '' }}
                                        </td>

                                        <td>
                                            @if ($customer->ivr_status == '1')
                                                <span class="badge badge-pill badge-success">Opt In</span>
                                            @elseif ($customer->ivr_status == '-1')
                                                <span class="badge badge-pill badge-danger">Removed Out</span>
                                            @elseif ($customer->ivr_status == '0')
                                                <span class="badge badge-pill badge-warning">Opt Out</span>
                                            @elseif ($customer->ivr_status == null)
                                                <span class="badge badge-pill badge-primary">Not Yet</span>
                                            @elseif ($customer->ivr_status == '-5')
                                                <span class="badge badge-pill badge-dark">N/A</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($customer->status == '1')
                                                <span class="badge badge-pill badge-success">Opt In</span>
                                            @elseif ($customer->status == '-1')
                                                <span class="badge badge-pill badge-danger">Removed Out</span>
                                            @elseif ($customer->status == '0')
                                                <span class="badge badge-pill badge-warning">Opt Out</span>
                                            @elseif ($customer->status == null)
                                                <span class="badge badge-pill badge-primary">Not Yet</span>
                                            @elseif ($customer->status == '-5')
                                               <span class="badge badge-pill badge-dark">N/A</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($customer->doctor_status == '1')
                                                <span class="badge badge-pill badge-success">Opt In</span>
                                            @elseif ($customer->doctor_status == '-1')
                                                <span class="badge badge-pill badge-danger">Removed Out</span>
                                            @elseif ($customer->doctor_status == '0')
                                                <span class="badge badge-pill badge-warning">Opt Out</span>
                                            @elseif ($customer->doctor_status == null)
                                                <span class="badge badge-pill badge-primary">Not Yet</span>
                                            @elseif ($customer->doctor_status == '-5')
                                               <span class="badge badge-pill badge-dark">N/A</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($customer->doctor_subscription_status == '1')
                                                <span class="badge badge-pill badge-success">Opt In</span>
                                            @elseif ($customer->doctor_subscription_status == '-1')
                                                <span class="badge badge-pill badge-danger">Removed Out</span>
                                            @elseif ($customer->doctor_subscription_status == '0')
                                                <span class="badge badge-pill badge-warning">Opt Out</span>
                                            @elseif ($customer->doctor_subscription_status == null)
                                                <span class="badge badge-pill badge-primary">Not Yet</span>
                                            @elseif ($customer->doctor_subscription_status == '-5')
                                               <span class="badge badge-pill badge-dark">N/A</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ $customer->keyword ?? '' }}
                                        </td>

                                        <td>
                                            {{ $customer->created_at ?? '' }}
                                        </td>


                                        <td>

                                            <a class="btn btn-xs btn-info"
                                                href="{{ route('admin.customers.show', $customer->id) }}">
                                                view more
                                            </a>


                                            @can('add_blacklist')
                                                <a class="btn btn-xs btn-danger" href="#" data-toggle="modal"
                                                    data-target=".bs-example-modal-lg" data-id="{{ $customer->id }}"><i
                                                        class="mdi mdi-minus-circle"></i> add to blacklist</a>
                                            @endcan
                                        </td>


                                    </tr>
                                @endforeach


                            </tbody>
                        </table>

                        {!! $customers->links() !!}
                    </div>
                </div>
            </div>
        </div>
        @include('admin.customers.modal.blacklist')
    @endsection
    @push('after-scripts')
        <script>
            $('#gfgmodal').on('show.bs.modal', function(e) {
                // get information to update quickly to modal view as loading begins
                var opener = e.relatedTarget; //this holds the element who called the modal

                //we get details from attributes
                var myArticleId = $(opener).attr('data-id');
                document.getElementById("customer_id").value = myArticleId;

            });
        </script>
    @endpush

