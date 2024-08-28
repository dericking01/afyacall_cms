@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor mb-0 mt-0">{{ $customers->msisdn }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                    <li class="breadcrumb-item active">Customers</li>
                </ol>
            </div>

            @can('subscribe_unsubscribe_customer')
		<div class="col-md-6 col-4 align-self-center">

                    @if ($customers->status == '1' || $customers->status == '0' || $customers->ivr_status == '1' || $customers->ivr_status == '0')
                        <button class="btn float-right hidden-sm-down btn-danger" data-toggle="modal"
                            data-target=".bs-example-modal-lg" style="margin:5px;"><i class="mdi mdi-minus-circle"></i>
                            UnSubscribe</button>
                    @endif
                </div>
            @endcan
        </div>

        <!-- Column -->
        <!-- Column -->
        <div class="col-12">
          
            <div class="card">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs profile-tab" role="tablist">
                    <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#home"
                            role="tab">Subscriptions</a> </li>
                    @can('view_customer_balance')
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#balance" role="tab">Main
                                Account Balance</a> </li>
		            @endcan

                </ul>
                <!-- Tab panes -->
                <div class="tab-content">

                    <div class="tab-pane active" id="home" role="tabpanel">
                        <div class="card-body">
                            <table id="subscrptiontable"
                                class="display nowrap table table-hover table-striped table-bordered" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>product</th>
                                        <th>Keyword</th>
                                        <th>Content</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach ($customers['opts'] as $key => $customer)
                                        <tr>

                                            <td>
                                                {{ $customer->date ?? '' }}
                                            </td>
                                            <td>
                                                {{ $customer->product['name'] ?? '' }}
                                            </td>
                                            <td>
                                                {{ $customer->ConversationID ?? '' }}
                                            </td>
                                            <td>
                                                {{ $customer->OriginatorConversationID ?? '' }}
                                            </td>
                                            <td>
                                                @if ($customer->opt_value == 1)
                                                    <span class="badge badge-pill badge-success">opt in</span>
                                                @else
                                                    <span class="badge badge-pill badge-danger">opt out</span>
                                                @endif
                                            </td>


                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div class="tab-pane" id="balance" role="tabpanel">
                        <div class="card-body">
                            <input type="hidden" id="customer_number" name="customer_number"
                                value="{{ $customers->msisdn }}">
                           


                            <div class="center-block" id='loader'
                                style='display: none; margin: -50px 0px 0px -50px;position: absolute;top: 50%;left: 50%;'>
                                <img src='{{ asset('assets/images/loading.gif') }}' width='50px' height='50px'>
                            </div>


                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Column -->
    </div>
    </div>


    <!-- Row -->
    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->


    </div>
    @include('admin.customers.modal.subscribe')
@endsection
@push('after-scripts')
    <script>
        $(function() {

            var start = moment().subtract(29, 'days');
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

        });


        function updateButton() {

            var customer_number = document.getElementById("customer_number").value;
            $.ajax({
                type: 'GET',
                url: "#",
                data: {
                    customer_number: customer_number
                },
                beforeSend: function() {
                    $("#loader").show();
                    $("#showdata").hide();
                },
                success: function(response) {
                    var resultData = JSON.parse(response);
                    $.each(resultData, function(k, v) {
                        $.each(v.details, function(m, n) {
                            $.each(n, function(x, y) {
                                document.getElementById("amountbalance").innerText = y
                                    .amount / 100 + " Tshs";
                            });
                        });
                    });

                },
                error: function(error) {
                    $.toast({
                        heading: 'Check Balance Failure',
                        text: 'Sorry there is an error when tring to check balance.',
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'error',
                        hideAfter: 3500

                    });

                },
                complete: function(data) {
                    $("#loader").hide();
                    $("#showdata").show();
                    $.toast({
                        heading: 'Check Balance Success',
                        text: 'You have successfull check balance for a customer.',
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'success',
                        hideAfter: 3500,
                        stack: 6
                    });
                }
            });
        }
    </script>
@endpush

