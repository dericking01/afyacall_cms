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
                <h4 class="card-title">Revenue Reports</h4>
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
				<th>#</th>
                                <th>Transaction Date</th>
                                <th>D.Revenue (SMS-TIPS)</th>
                                <th>D.Revenue (IVR-TIPS)</th>
                                <th>D.Revenue (DOCTOR-TIPS)</th>
                                <th>D.Revenue TOTAL</th>
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
        <script src="{{ asset('js/buttons.min.js') }}"></script>
        <script src="{{ asset('js/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('js/jszip.min.js') }}"></script>
        <script src="{{ asset('js/pdfmake.min.js') }}"></script>
        <script src="{{ asset('js/vfs_fonts.js') }}"></script>
        <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
	<script src="{{ asset('js/buttons.print.min.js') }}"></script>
        <!-- start - This is for export functionality only -->
      <script>
            $(".shawCalRanges").daterangepicker({
                ranges: {
                    Today: [moment(), moment()],
                    Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
                    "Last 7 Days": [moment().subtract(6, "days"), moment()],
                    "Last 30 Days": [moment().subtract(29, "days"), moment()],
                    "This Month": [moment().startOf("month"), moment().endOf("month")],
                    "Last Month": [
                        moment().subtract(1, "month").startOf("month"),
                        moment().subtract(1, "month").endOf("month"),
                    ],
                },
                alwaysShowCalendars: true,
            });

            function customerListSearch() {
                var startDate = $(".shawCalRanges")
                    .data("daterangepicker")
                    .startDate.format("YYYY-MM-DD");
                var endDate = $(".shawCalRanges")
                    .data("daterangepicker")
                    .endDate.format("YYYY-MM-DD");

                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    type: "POST",
                    url: "{{ route('admin.revenueSearch') }}",
                    data: {
                        startDate: startDate,
                        endDate: endDate,
                    },
                    beforeSend: function() {
                        $("#loader").show();
                        $("#showdata").hide();
                    },
                    success: function(response) {
                        var dataTable = $("#ticketreporttable").DataTable({
                            destroy: true,
                            dom: "Bfrtip",
                            pageLength: 25,
                            buttons: [{
                                    extend: "csv",
                                    titleAttr: "Click to download as a CSV",
                                    filename: "Revenue Reports",
                                },
                                {
                                    extend: "excel",
                                    titleAttr: "Click to download as a Excel",
                                    filename: "Revenue Reports",
                                },
                            ],
                            columnDefs: [{
                                targets: 1,
                                render: function(data) {
                                    return moment(data).format("LL");
                                },
                               },

                         ],
                        });
                        dataTable.clear().draw();
			var resultData = response.data;
			console.log(resultData);
                        $.each(resultData, function(index, row) {
                            dataTable.row
                                .add([
                                    (index + 1),
                                    row.DateCreated,
                                    row.sms,
                                    row.ivr,
                                    row.calls,
                                    row.total
                                ])
                                .draw();
                        });
                    },
                    complete: function(data) {
                        $("#loader").hide();
                        $("#showdata").show();
                    },
                });
            }
        </script>
    @endpush

