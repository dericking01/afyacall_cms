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

@endpush
@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <!-- Row -->
    <div class="row">

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Total Customers</h4>
                    <div class="text-right">
                        <h2 class="font-light mb-0"><i class="ti-arrow-up text-info"></i> {{ $totalcustomers }}</h2>
                        <span class="text-muted">Customers</span>
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
        <!-- Column -->
        <!-- Column -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Total Subscriptions</h4>
                    <div class="text-right">
                        <h2 class="font-light mb-0"><i class="ti-arrow-up text-purple"></i> {{ $totalactive }} </h2>
                        <span class="text-muted">Today</span>
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
                    <h4 class="card-title">Today Customers</h4>
                    <div class="text-right">
                        <h2 class="font-light mb-0"><i class="ti-arrow-up text-info"></i> {{ $customertoday }}</h2>
                        <span class="text-muted">Joined Today</span>
                    </div>
                    <span class="text-danger">%</span>
                    <div class="progress">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{$percentageChange}}%; height: 6px;"
                            aria-valuenow="{{$percentageChange}}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Yesterday Customers</h4>
                    <div class="text-right">
                        <h2 class="font-light mb-0"><i class="ti-arrow-up text-info"></i> {{ $customeryesterday }}</h2>
                        <span class="text-muted">Joined Yesterday</span>
                    </div>
                    <span class="text-danger">%</span>
                    <div class="progress">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 0%; height: 6px;"
                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row -->
    <!-- Row -->
        <!-- Row -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Total Subscriber</h4>
                     <div class="row">
                        <div class="text-center col-md-6">
                            <h2 class="font-light center"> {{ $totalsmscustomer }}</h2>
                            <span class="text-muted">SMS</span>

                        </div>
                        <div class="text-center col-md-6">
                            <h2 class="font-light center"> {{ $totalivrcustomer }}</h2>
                            <span class="text-muted">IVR</span>
                        </div>
                     </div>

                </div>
            </div>
        </div>
        <!-- Column -->
        <!-- Column -->
        <!-- Column -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Active  Subscriber</h4>
                        <div class="row">
                        <div class="text-center col-md-6">
                            <h2 class="font-light center"> {{ $activesmssubsriber }}</h2>
                            <span class="text-muted">SMS</span>

                        </div>
                        <div class="text-center col-md-6">
                            <h2 class="font-light center"> {{ $activeivrsubsriber }}</h2>
                            <span class="text-muted">IVR</span>
                        </div>
                     </div>
                </div>
            </div>
        </div>
        <!-- Column -->
        <!-- Column -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Charged Successful</h4>
                       <div class="row">
                        <div class="text-center col-md-6">
                            <h2 class="font-light center"> {{ $chargedsmssubsriber }}</h2>
                            <span class="text-muted">SMS</span>
                        </div>
                        <div class="text-center col-md-6">
                            <h2 class="font-light center"> {{ $chargedivrsubsriber }}</h2>
                            <span class="text-muted">IVR</span>
                        </div>
                     </div>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Unsub</h4>
                       <div class="row">
                        <div class="text-center col-md-6">
                            <h2 class="font-light center"> {{ $unsubsmssubsriber }}</h2>
                            <span class="text-muted">SMS</span>
                        </div>
                        <div class="text-center col-md-6">
                            <h2 class="font-light center"> {{ $unsubivrsubsriber }}</h2>
                            <span class="text-muted">IVR</span>
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
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


    @endsection

