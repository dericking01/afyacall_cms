@extends('layouts.app')
@push('before-styles')
    <!-- chartist CSS -->
    <link href="{{ asset('assets/plugins/chartist-js/dist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/chartist-js/dist/chartist-init.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css') }}"
        rel="stylesheet">
    <link href="{{ asset('assets/plugins/css-chart/css-chart.css') }}" rel="stylesheet">
@endpush

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
                url: "{{ route('admin.graphs_smschartsdata') }}",
                success: function(response) {
                    console.log(response);
                    new Chartist.Line('.total-sms', {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept',
                            'Oct', 'Nov', 'Dec'
                        ],
                        series: [response]
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
                                return (value / 1000) + 'k';
                            }
                        }

                    });
                },
            });


        });
    </script>
@endpush
@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <!-- Row -->
    <div class="row">
        <!-- Column -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daily Revenue</h4>
                    @if ($percentage < 0)
                        <div class="text-right">
                            <h2 class="font-light mb-0"><i class="ti-arrow-down text-danger"></i>
                                {{ number_format($dailytrans, 2) }}</h2>
                            <span class="text-muted">Revenue Decrease by</span>
                        </div>
                     
                    @else
                        <div class="text-right">
                            <h2 class="font-light mb-0"><i class="ti-arrow-up text-success"></i>
                                {{ number_format($dailytrans, 2) }}</h2>
                            <span class="text-muted">Revenue Increase by</span>
                        </div>

                    @endif
                    <span class="text-success"> {{ sprintf('%.2f', $percentage) }}%</span>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $percentage }}%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
                            aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
        <!-- Column -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Weekly Transaction</h4>
                    <div class="text-right">
                        <h2 class="font-light mb-0"><i class="ti-arrow-up text-info"></i>
                            {{ number_format($weeklytrans, 2) }}</h2>
                        <span class="text-muted">Weekly Income</span>
                    </div>
                    <span class="text-info">%</span>
                    <div class="progress">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 0%; height: 6px;"
                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
        <!-- Column -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Total Subscriptions</h4>
                    <div class="text-right">
                        <h2 class="font-light mb-0"><i class="ti-arrow-up text-purple"></i> {{ $totalactive }} </h2>
                        <span class="text-muted">subscribe data</span>
                    </div>
                    <span class="text-purple">%</span>
                    <div class="progress">
                        <div class="progress-bar bg-purple" role="progressbar" style="width:0%; height: 6px;"
                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
        <!-- Column -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Total Customer</h4>
                    <div class="text-right">
                        <h2 class="font-light mb-0"><i class="ti-arrow-up text-info"></i> {{ $totalcustomers }}</h2>
                        <span class="text-muted">Customer Data</span>
                    </div>
                    <span class="text-danger">%</span>
                    <div class="progress">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 0%; height: 6px;"
                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>
    <!-- Row -->
    <!-- Row -->

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap">
                            <div>
                                <h3>Total Message Sent per Month</h3>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="total-sms" style="height: 350px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Recent Contents Created</h4>
                    </div>
                    <!-- ============================================================== -->
                    <!-- Comment widgets -->
                    <!-- ============================================================== -->
                    <div class="comment-widgets">
                        <!-- Comment Row -->

                        @foreach ($contentcreats as $contentcreat)
                            <div class="d-flex flex-row comment-row">
                                <div class="p-2"><span><img
                                            src="https://ui-avatars.com/api/?rounded=true&bold=true&name= {{ $contentcreat->message }}"
                                            alt="user"></span></div>
                                <div class="comment-text w-100">
                                    <p class="mb-1">{{ $contentcreat->message }}</p>
                                    <div class="comment-footer">
                                        <span class="text-muted float-right">{{ $contentcreat->created_at }}</span>
                                        <span class="label label-light-info">{{ $contentcreat->type['name'] }}</span>

                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Recent Messages</h4>
                        <div class="message-box">
                            <div class="message-widget message-scroll">

                                @foreach ($recentmesages as $recentmesage)
                                    <a href="#">

                                        <div class="user-img">
                                            <span
                                                class="round round-success">{{ substr($recentmesage->content['message'], 0, 4) }}</span>
                                        </div>
                                        <div class="mail-contnet">
                                            <h5>{{ $recentmesage->customer['msisdn'] }}</h5>
                                            <span class="mail-desc">{{ $recentmesage->content['message'] }}.</span>
                                            <span class="time">{{ $recentmesage->created_at }}</span>
                                        </div>
                                    </a>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>




        </div>


        <!-- Row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="row">
                        <!-- Column -->
                        <div class="col-lg-5 col-xlg-3 col-md-6">
			    <div class="card-body">
                                <h3 class="card-title mb-4">IVR Reviews and Rating</h3>
                                <span class="mt-5 display-6">{{$totalratings}}</span>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <!-- Column -->
                        <div class="col-lg-7 col-xlg-9 col-md-6 border-left pl-0">
                            <ul class="product-review">
                                <li>
                                    <span class="text-muted display-5"><i class="mdi mdi-emoticon-cool"></i></span>
                                    <div class="dl ml-2">
                                        <h3 class="card-title">Positive Reviews</h3>
                                        <h6 class="card-subtitle">{{$positiverating}} Reviews</h6>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar"
 style="width: {{$positiverating * 100 / $totalratings }}%; height:6px;" aria-valuenow="25" aria-valuemin="0"					 
                                            aria-valuemax="100"></div>
                                    </div>
				</li>



                                <li>
                                    <span class="text-muted display-5"><i class="mdi mdi-emoticon-sad"></i></span>
                                    <div class="dl ml-2">
                                        <h3 class="card-title">Negative Reviews</h3>
                                        <h6 class="card-subtitle">{{$negativerating}} Reviews</h6>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" role="progressbar"
                                            style="width: {{$negativerating * 100 / $totalratings }}%; height:6px;" aria-valuenow="25" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </li>
                                <li>
                                    <span class="text-muted display-5"><i class="mdi mdi-emoticon-neutral"></i></span>
                                    <div class="dl ml-2">
                                        <h3 class="card-title">Neutral Reviews</h3>
                                        <h6 class="card-subtitle">{{$neutralrating}}  Reviews</h6>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            style="width: {{$neutralrating * 100 / $totalratings }}%; height:6px;" aria-valuenow="25" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
				</li>


                            </ul>
                        </div>
                        <!-- Column -->
                    </div>
                </div>
            </div>
        </div>
    @endsection

