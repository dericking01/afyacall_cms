@extends('layouts.app')

@push('before-styles')
    <link rel="stylesheet" type="text/css" href="/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css">
@endpush

@push('after-scripts')
    <script src="{{ asset('assets/plugins/bootstrap-table/dist/bootstrap-table.min.js')}}" type="text/javascript">
    </script>
    <script>
 
        var data = {!! json_encode($deliveries) !!};

        $(function() {
            $('#smptabledeliver').bootstrapTable({
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
            buildTable($('#cllmtable'), 50, 50);
        });
    </script>    
@endpush

@section('content')
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">SMS Campaigns Statistics</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.contact.campaign') }}">SMS Campaigns</a></li>
                <li class="breadcrumb-item active">Overview</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">SMS Campaing Summary</h4>
                    <table id="smptabledeliver" class="table">
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
    </div>
@endsection

