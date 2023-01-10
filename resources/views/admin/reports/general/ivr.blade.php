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
                <h4 class="card-title">General Reports for IVR</h4>
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
                              <option value="all">--All--</option>
                              <option value="1">Opt In</option>
                              <option value="0">Opt Out</option>
                              <option value="-1">Removed Out</option>
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
                                <th>Created Date</th>
                                <th>Msisdn</th>
                                <th>Language</th>
                                <th>Keyword</th>
                                <th>Status</th>                               
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
        <script src="{{asset('js/buttons.min.js')}}"></script>
        <script src="{{asset('js/buttons.flash.min.js')}}"></script>
        <script src="{{asset('js/jszip.min.js')}}"></script>
        <script src="{{asset('js/pdfmake.min.js')}}"></script>
        <script src="{{asset('js/vfs_fonts.js')}}"></script>
        <script src="{{asset('js/buttons.html5.min.js')}}"></script>
        <script src="{{asset('js/buttons.print.min.js')}}"></script>
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
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: "{{ route('admin.daterange.ivr') }}",
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
                        console.log(response)
                        var dataTable = $("#ticketreporttable").DataTable({
                            destroy: true,
                            dom: 'Bfrtip',
                            buttons: [
                            'csv', 'excel'
                             ],
                            columnDefs: [{
                                targets: 0,
                                render: function(data) {
                                    return moment(data).format('LLL');
                                }
                            }]
                        });
                        dataTable.clear().draw();
                        var resultData = response.data;
                        $.each(resultData, function(index, row) {
                            dataTable.row.add([
                                row.created_at,
                                row.msisdn,
                                row.language,
                                row.keyword,
                                row.status,
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
                <!-- start - This is for export functionality only -->
        
    @endpush
