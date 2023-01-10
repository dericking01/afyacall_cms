@extends('layouts.app')

@push('before-styles')

    <link href="{{ asset('assets/plugins/bootstrap-switch/bootstrap-switch.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/switchery/dist/switchery.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css') }}"
        rel="stylesheet" />
    <link href="{{ asset('assets/plugins/multiselect/css/multi-select.css') }}" rel="stylesheet" type="text/css" />

@endpush

@push('after-scripts')
    <!-- bt-switch -->
    <script src="{{ asset('assets/plugins/bootstrap-switch/bootstrap-switch.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/switchery/dist/switchery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/dff/dff.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/multiselect/js/jquery.multi-select.js') }}"></script>
    <script>
        jQuery(document).ready(function() {

            // For select 2
            $(".select2").select2();
            $('.selectpicker').selectpicker();

        });
    </script>
    <script>
        $(function() {
            // For select 2

            $("input[name='schedule']").click(function() {
                if ($("#schedulelater").is(":checked")) {
                    $("#showscheduletime").show();
                    $("#showscheduletime2").show();
                } else {
                    $("#showscheduletime").hide();
                    $("#showscheduletime2").hide();
                }
            });
        });
    </script>


@endpush

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Campaigns Service</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.contentstype.index') }}">Contacts</a></li>
                <li class="breadcrumb-item active">Campaigns Service</li>
            </ol>
        </div>

    </div>

    <!-- Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0 text-white">Campaigns Service</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pushcompaignservice') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-body">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Campaign name<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="compaignname" name="compaignname" value="{{ $todaydate }}"
                                        class="form-control">
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-12 bt-switch">
                                <div class="form-group">
                                    <label class="control-label">Group Names<span class="text-danger">*</span>
                                    </label>
                                    <select class="select2 mb-2 select2-multiple" style="width: 100%" multiple="multiple"
                                        data-placeholder="Choose"  name="groups[]" id="groups">
                                        @foreach($groups as $key => $group)
                                         <option value="{{ $group->id }}">{{ $group->name }} </option>
                                      @endforeach
                 

                                    </select>
                                </div>

                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"
                                        style="display: flex; justify-content: space-between;"><span>Content <span
                                                class="text-danger">*</span></span><span class="text-dark-50"
                                            id="counter"> 0 / 160 characters | <span
                                                style="color: var(--color);">0message(s)</span><span><span
                                                    class="control-label"><span class="control-label" tabindex="0"><i
                                                            style="color: var(--color);"></i></span></span></span></span></label>
                                    <textarea name="message" id="message" autocomplete="off" maxlength="160"
                                        placeholder="Please input message content" class="form-control" spellcheck="false"
                                        style="min-height: 127px; height: 127px;"></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Sender Name<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control select2" name="sendername" id="sendername">
                                        <option value="afyacall">AFYACALL</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Planned Time</label>
                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="schedulenow" name="schedule"
                                                        class="custom-control-input" checked>
                                                    <label class="custom-control-label" for="schedulenow">Now</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="schedulelater" name="schedule"
                                                        class="custom-control-input">
                                                    <label class="custom-control-label" for="schedulelater">Schedule</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3" id="showscheduletime" style="display: none">
                                    <div class="form-group">
                                        <label class="control-label">Time Zone <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control select2" name="vatregister" id="vatregister">
                                            <option value="Africa/Nairobi">Africa/Nairobi</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6" id="showscheduletime2" style="display: none">
                                    <div class="form-group">
                                        <label class="control-label">Date/Time <span class="text-danger">*</span>
                                        </label>
                                        <input type="datetime-local" id="datetimeschedule" name="datetimeschedule"
                                            class="form-control">
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Submit </button>
                            <button type="button" class="btn btn-inverse">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
