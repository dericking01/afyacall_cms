@extends('layouts.app')

@push('before-styles')

    <link href="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/tablesaw-master/dist/tablesaw.css') }}" rel="stylesheet">

@endpush

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Ticket Reports</h4>
                <div class="row pt-3">
                    <!--/span-->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Date Range</label>
                            <input type='text' class="form-control shawCalRanges" />
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <select class="form-control select2" name="status" id="status">
                                <option value="all">All</option>
                                <option value="0">Open</option>
                                <option value="1">InProgress</option>
                                <option value="2">Closed</option>
                            </select>
                        </div>
                    </div>
                    <!--/span-->

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Search </label>
                            <button type="button" class="btn waves-effect waves-light btn-block btn-success"
                                onclick="customerListSearch()"><i class="fas fa-search"></i> Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="center-block" id='loader'
            style='display: none; margin: -50px 0px 0px -50px;position: absolute;top: 50%;left: 50%;'>
            <img src='{{ asset('assets/images/loading.gif') }}' width='50px' height='50px'>
        </div>

        <div class="card" id="showdata" style='display: none;'>
            <div class="card">
                <div class="card-body">
                    <table id="ticketreporttable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date Created</th>
                                <th>Mobile Number</th>
                                <th>Ticket Number</th>
                                <th>Descriptions</th>
                                <th>Status</th>
                                <th>Reported by</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @endsection

    @push('after-scripts')

        <script src="{{ asset('assets/plugins/moment/moment.js') }}"></script>
        <script src="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/toast-master/js/jquery.toast.js') }}"></script>
        <script>
            $('.shawCalRanges').daterangepicker({
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                        'month')]
                },
                alwaysShowCalendars: true,
            });

            function customerListSearch() {

                var startDate = $('.shawCalRanges').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var endDate = $('.shawCalRanges').data('daterangepicker').endDate.format('YYYY-MM-DD');
                var status = document.getElementById("status").value;

                console.log(status)
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: "{{ route('admin.ticketingSearch') }}",
                    data: {
                        startDate: startDate,
                        endDate: endDate,
                        status: status
                    },
                    beforeSend: function() {
                        $("#loader").show();
                        $("#showdata").hide();
                    },
                    success: function(response) {

                        var dataTable = $("#ticketreporttable").DataTable({
                            destroy: true,
                            dom: 'Bfrtip',
                            pageLength: 25,
                            buttons: [{
                                    extend: 'csv',
                                    titleAttr: 'Click to download as a CSV',
                                    filename: 'Tickets Reports',
                                },
                                {
                                    extend: 'excel',
                                    titleAttr: 'Click to download as a Excel',
                                    filename: 'Tickets Reports',
                                }

                            ],

                            columnDefs: [{
                                    targets: 0,
                                    render: function(data) {
                                        return moment(data).format('LLL');
                                    }
                                },
                                {
                                    targets: 4,
                                    render: function(data) {
                                        if (data == 1) {
                                            return '<span class="badge badge-pill badge-success">Success</span>';
                                        } else {
                                            return '<span class="badge badge-pill badge-danger">Failed</span>';
                                        }

                                    }

                                }
                            ],
                        });
                        dataTable.clear().draw();
                        var resultData = response.data;
                        $.each(resultData, function(index, row) {
                            dataTable.row.add([
                                row.created_at,
                                row.msisdn,
                                row.reference,
                                row.message,
                                row.status,
                                row.user['name'],
                            ]).draw();
                        })

                    },
                    complete: function(data) {
                        $("#loader").hide();
                        $("#showdata").show();
                    }
                });
            }
        </script>
    @endpush
