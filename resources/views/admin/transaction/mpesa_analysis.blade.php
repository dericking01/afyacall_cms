@extends('layouts.app')

@push('before-styles')
    <link href="{{ asset('assets/plugins/chartist-js/dist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/chartist-js/dist/chartist-init.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css') }}"
        rel="stylesheet">
    <link href="{{ asset('assets/plugins/css-chart/css-chart.css') }}" rel="stylesheet">

@endpush

@section('content')

    <!-- Row -->
<div class="row">
    <!-- Column -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap">
                            <div>
                                <h3>Mpesa Analytics</h3>
                             </div>
                            <div class="ml-auto ">
                                <ul class="list-inline">
                                    <li>
                                        <h6 class="text-muted"><i class="fa fa-circle mr-1 text-success"></i>Success</h6> </li>
                                    <li>
                                        <h6 class="text-muted"><i class="fa fa-circle mr-1 text-danger"></i>Fail</h6> </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="total-sms" style="height: 350px;"></div>
                    </div>
                  
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->

@endsection

@push('after-scripts')
    <!-- chartist chart -->
    <script src="{{ asset('assets/plugins/chartist-js/dist/chartist.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js') }}"></script>
    <!-- Chart JS -->
    <script src="{{ asset('assets/plugins/echarts/echarts-all.js') }}"></script>
    <script src="{{ asset('assets/plugins/toast-master/js/jquery.toast.js') }}"></script>
    <!-- Chart JS -->
    <script src="{{ asset('vendor/js/dashboard1.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'GET',
                url: "{{ route('admin.get_mpesa_analytics') }}",
                success: function(response) {
                    console.log(response);
                    new Chartist.Bar('.total-sms', {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept',
                            'Oct', 'Nov', 'Dec'
                        ],
                        series: [response['success'], response['fail']]
                    }, {
                        low: 0,
                        showArea: true,
                        fullWidth: true,
                        plugins: [
                            Chartist.plugins.tooltip()
                        ], // As this is axis specific we need to tell Chartist to use whole numbers only on the concerned axis
                        axisY: {
                            onlyInteger: true,
                            offset: 20,
                            labelInterpolationFnc: function(value) {
                                return (value / 1);
                            }
                        }

                    });
                },
            });



        });
    </script>

@endpush
