@extends('layouts.app')

@push('before-styles')

<link rel="stylesheet" type="text/css" href="/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">

@endpush

@push('after-scripts')
<script src="{{asset('assets/plugins/datatables/datatables.min.js')}}"  type="text/javascript"></script>

<script src="{{asset('js/buttons.min.js')}}"></script>
<script src="{{asset('js/buttons.flash.min.js')}}"></script>
<script src="{{asset('js/jszip.min.js')}}"></script>
<script src="{{asset('js/pdfmake.min.js')}}"></script>
<script src="{{asset('js/vfs_fonts.js')}}"></script>
<script src="{{asset('js/buttons.html5.min.js')}}"></script>
<script src="{{asset('js/buttons.print.min.js')}}"></script>
<script>
    $('#example23').DataTable({
    dom: 'Bfrtip',

	            order: [
                [1, "asc"]
        ],
        buttons: [{
                    extend: 'csv',
                    titleAttr: 'Click to download as a CSV',
                    filename: 'Monthly Reports',
                },
                {
                    extend: 'excel',
                    titleAttr: 'Click to download as a Excel',
                    filename: 'Monthly Reports',
                }

            ]
    });
    $('.buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
</script>

@endpush

@section('content')
<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Reports</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route("admin.home") }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Monthly Report</li>
        </ol>
    </div>

  
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Monthly Revenue Reports</h4>
                <div class="table-responsive">
                    <table id="example23" class="table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                               <th></th>
                                <th colspan="3" class="text-center" >REVENUE</th>
                           
                                <th colspan="4" class="text-center">ACTIVE BASE/CHARGED BASE</th>
                        </tr>
                  
			<tr>
                            <th style="width:5%">MONTH</th>
                            <th style="width:30%">SMS</th>
                            <th style="width:30%">IVR</th>
                            <th style="width:3%">DR.CALL</th>
                            <th style="width:5%">TOTAL</th>
                            <th style="width:15%">SMS</th>
                            <th style="width:15%">IVR</th>
                            <th style="width:15%">DR.CALL</th>
                            <th style="width:15%">TOTAL</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlys as $key => $monthly)
                            <tr >
                                <td>
                                    {{ $monthly->Monthly ?? '' }}
                                </td>
                               
                                <td>
                                    {{ $monthly->sms ?? '' }}
                                </td>

                                <td>
                                    {{ $monthly->ivr ?? '' }}
                                </td>

                                <td>
                                    {{ $monthly->calls ?? '' }}
                                </td>

                                <td>
                                    {{ $monthly->total ?? '' }}
                                </td>
                                <td>
                                    {{ $monthly->sms_sub ?? '' }}
                                </td>
                                <td>
                                    {{ $monthly->ivr_sub ?? '' }}
                                </td>
                                <td>
                                    {{ $monthly->calls_sub ?? '' }}
                                </td>
                        
                                <td>
                                    {{ $monthly->total_sub ?? '' }}
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
