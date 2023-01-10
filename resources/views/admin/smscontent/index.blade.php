@extends('layouts.app')

@push('before-styles')
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/plugins/datatables/media/css/dataTables.bootstrap4.css') }}">
    <link href="{{ asset('assets/plugins/bootstrap-switch/bootstrap-switch.min.css') }}" rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/buttons.min.js') }}"></script>
    <script src="{{ asset('js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('js/jszip.min.js') }}"></script>
    <script src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/buttons.print.min.js') }}"></script>
    <script>
        $('#example23').DataTable({
            dom: 'Bfrtip',
 pageLength: 50,
            buttons: [
                'csv', 'excel', 'pdf'
            ]
        });
        $('.buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
    </script>
    <script src="{{ asset('assets/plugins/bootstrap-switch/bootstrap-switch.min.js') }}"></script>
    <script type="text/javascript">
        $(".bt-switch input[type='checkbox']").bootstrapSwitch();
        var radioswitch = function() {
            var bt = function() {
                $(".radio-switch").on("switch-change", function() {
                    console.log("check this");
                    $(".radio-switch").bootstrapSwitch("toggleRadioState")
                }), $(".radio-switch").on("switch-change", function() {
                    $(".radio-switch").bootstrapSwitch("toggleRadioStateAllowUncheck")
                }), $(".radio-switch").on("switch-change", function() {
                    $(".radio-switch").bootstrapSwitch("toggleRadioStateAllowUncheck", !1)
                })
            };
            return {
                init: function() {
                    bt()
                }
            }
        }();
        $(document).ready(function() {
            radioswitch.init()
        });


        // var checkVal = $(':checkbox[name='+chkName+']').attr('checked');//true or fal
    </script>

@endpush

@section('content')
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Sms content</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Sms content</li>
            </ol>
        </div>
        <div class="col-md-6 col-4 align-self-center">

            <a class="btn float-right hidden-sm-down btn-success" href="{{ route('admin.contents.create') }}"><i
                    class="mdi mdi-plus-circle"></i> Create Content</a>
            <div class="dropdown float-right mr-2 hidden-sm-down">
                <button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#importModal"
                    data-backdrop="static" data-keyboard="false"><i class="mdi mdi-plus-circle"></i> Import Content
                </button>

            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">SMS CONTENTS LIST</h4>
                    <div class="table-responsive">
                        <table id="example23" class="table table-hover table-striped table-bordered" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="width:5%">Content</th>
                                    <th style="width:30%">SWH-Message</th>
                                    <th style="width:30%">ENG-Message</th>
                                    <th style="width:3%">Priority</th>
                                    <th style="width:15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contents as $key => $smscontent)
                                    <tr>
                                        <td>
                                            {{ $no++ }}
                                        </td>

                                        <td>
                                            {{ $smscontent->type['name'] ?? '' }}
                                        </td>

                                        <td>
                                            {{ $smscontent->message ?? '' }}
                                        </td>

                                        <td>
                                            {{ $smscontent->eng_message ?? '' }}
                                        </td>

                                        <td>
                                            {{ $smscontent->length ?? '' }}
                                        </td>

                                        <td>
                                            <a class="btn btn-xs btn-info"
                                                href="{{ route('admin.contents.edit', $smscontent->id) }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>

                                            {{-- <form action="{{ route('admin.contents.destroy', $smscontent->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="submit" class="btn btn-xs btn-danger"  value="delete">
                                </form> --}}
                                        </td>

                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('admin.content-import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="importModalLabel">Import Sms Contents</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="file" name="file" class="form-control file-import">
                            <br>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                            <button class="btn btn-success">Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

