@extends('layouts.app')

@push('before-styles')
    <link rel="stylesheet" type="text/css" href="/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css">
@endpush

@push('after-scripts')
    <script src="{{ asset('assets/plugins/bootstrap-table/dist/bootstrap-table.min.js')}}" type="text/javascript">
    </script>
    <script>
        // {{ $campaigns }}
        var data = {!! json_encode($campaigns) !!};
        console.log(data);

        $(function() {
            $('#smptable').bootstrapTable({
                data: data
            });
        });


        /*table column*/

        function buildTable($el, cells, rows) {
            var i, j, row,
                columns = [],
                data = [];

            for (i = 0; i < cells; i++) {
                columns.push({
                    field: 'field' + i,
                    title: 'Cell' + i
                });
            }
            for (i = 0; i < rows; i++) {
                row = {};
                for (j = 0; j < cells; j++) {
                    row['field' + j] = 'Row-' + i + '-' + j;
                }
                data.push(row);
            }
            $el.bootstrapTable('destroy').bootstrapTable({
                columns: columns,
                data: data
            });
        }

        $(function() {
            buildTable($('#clmtable'), 50, 50);
        });
    </script>    
@endpush

@section('content')
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Outbound Dialler(OBD) Statistics</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.contact.outboundcampaing') }}">Outbound Dialler (OBD)</a></li>
                <li class="breadcrumb-item active">Overview</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Outbound Dialler Summary</h4>
                    <table id="smptable" class="table">
                        <thead>
                            <tr>
                                <th data-field="status">Status</th>
                                <th data-field="total">Total</th>

                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
           <small>ANSWER: Call is answered. A successful dial. Afyacall reached the Customer. <br>
            BUSY: Busy signal. Afyacall reached its customer number but the customer number is busy. <br>
            NOANSWER: No answer. Afyacall reached its customer number, the customer number rang for too long, then the dial timed out. <br>
            CANCEL: Call is cancelled. Afyacall reached its customer number but the Afyacall hung up before the Customer picked up. <br>
            CONGESTION: Congestion. This status is usually a sign that the customer number is not recognised. <br>
            CHANUNAVAIL: Channel unavailable. On SIP, peer may not be registered. <br>
            DONTCALL: Privacy mode, Customer rejected the call <br>
            TORTURE: Privacy mode, callee chose to send Afyacall to blacklist menu <br>
            INVALIDARGS: Error parsing Dial command arguments</small>
        </div>
    </div>
@endsection

