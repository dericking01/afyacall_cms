@extends('layouts.app')

@push('after-scripts')
<script src="{{ asset('js/jquery.PrintArea.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#print").click(function() {
            var mode = 'iframe'; //popup
            var close = mode == "popup";
            var options = {
                mode: mode,
                popClose: close
            };
            $("div.printableArea").printArea(options);
        });
    });
</script>

@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-body printableArea">
            <h3><b>INVOICE</b> <span class="float-right">{{$invoice->invoice_reference}}</span></h3>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div class="float-left">
                        <address>
                            <h3> &nbsp;<b class="text-danger">Afyacall Tanzania </b></h3>
                            <p class="text-muted ml-1">Vodacom Tower, Ursino Estate
                                <br/> Plot No. 23 Bagamoyo Road,
                                <br/> P. O. Box 2369,
                                <br/> Dar Es Salaam, Tanzania</p>
                        </address>
                    </div>
                    <div class="float-right text-right">
                        <address>
                            <h3>To,</h3>
                            <h4 class="font-bold">Vodacom Tanzania,</h4>
                            <p class="text-muted ml-4">Vodacom Tower, Ursino Estate,
                                <br/> Plot No. 23 Bagamoyo Road,
                                <br/> P. O. Box 2369,
                                <br/> Dar Es Salaam, Tanzania</p>
                        </address>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive mt-5" style="clear: both;">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Billing Period</th>
                                <th class="text-right">Invoice Date</th>
                                <th class="text-right">Unit Cost</th>
                                <th class="text-right">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>  {{ $invoice->period_from ?? '' }}  to  {{ $invoice->period_to ?? '' }} </td>
                                <td class="text-right">{{ $invoice->period_to ?? '' }} </td>
                                <td class="text-right">1</td>
                                <td class="text-right"> {{ number_format($invoice->total_amount, 2, '.', ',') }} </td>
                            </tr>
                          
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="float-right mt-4 text-right">
                        <p>Total amount: {{ number_format($invoice->total_amount, 2, '.', ',') }}</p>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="text-right">
                        <button class="btn btn-danger" type="submit"> Proceed to payment </button>
                        <button id="print" class="btn btn-default btn-outline" type="button"> <span><i class="fa fa-print"></i> Print</span> </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
