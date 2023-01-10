@extends('layouts.app')

@push('before-styles')

    <link rel="stylesheet" type="text/css" href="/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">

@endpush

@push('after-scripts')
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/toast-master/js/jquery.toast.js') }}"></script>
    <script src="{{ asset('js/toastr.js')}}"></script>
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

            buttons: [
                'csv', 'excel', 'pdf'
            ]
        });
        $('.buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
    </script>
@endpush

@section('content')
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Enticements</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Enticements</li>
            </ol>
        </div>
        <div class="col-md-6 col-4 align-self-center">
            @can('push_enticement')
            <a class="btn float-right hidden-sm-down btn-success" id="enticementbutton"><i
                    class="mdi mdi-plus-circle"></i>Enticement all</a>
            @endcan
            @can('import_contacts')
            <div class="dropdown float-right mr-2 hidden-sm-down">
                <button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#importModal"
                    data-backdrop="static" data-keyboard="false"><i class="mdi mdi-plus-circle"></i> Import Contacts
                </button>

            </div>
            @endcan
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">


                <div class="center-block" id='loader'
                    style='display: none; margin: -50px 0px 0px -50px;position: absolute;top: 50%;left: 50%;'>
                    <img src='{{ asset('assets/images/loading.gif') }}' width='50px' height='50px'>
                </div>


                <div id="showdatatable" class="card-body">

                    <h4 class="card-title">ENTICEMENTS LIST</h4>
                    <div class="table-responsive">
                        <table id="example23" class="table table-hover table-striped table-bordered" cellspacing="0"
                            width="100%">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="master"></th>
                                    <th>#</th>
                                    <th>Contact</th>
                                    <th>Enticement Count</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>


                                @foreach ($enticements as $key => $enticement)
                                    <tr id="tr_{{ $enticement->id }}">
                                        <td><input type="checkbox" class="sub_chk" data-id="{{ $enticement->id }}">
                                        </td>
                                        <td>
                                            {{ $no++ }}
                                        </td>

                                        <td>
                                            {{ $enticement->msisdn ?? '' }}
                                        </td>

                                        <td>
                                            {{ $enticement->count ?? '' }}
                                        </td>
                                        <td>

                                            <form action="{{ route('admin.enticement.destroy', $enticement->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                                style="display: inline-block;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="submit" class="btn btn-xs btn-danger" value="Remove Contact">
                                            </form>

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
                    <form action="{{ route('admin.contant-import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="importModalLabel">Import Contacts</h5>
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

    @push('after-scripts')

        <script type="text/javascript">
            $(document).ready(function() {
                $('#master').on('click', function(e) {
                    if ($(this).is(':checked', true)) {
                        $(".sub_chk").prop('checked', true);
                    } else {
                        $(".sub_chk").prop('checked', false);
                    }
                });
            });

            var enticementbutton = document.getElementById('enticementbutton');
            enticementbutton.onclick = function() {

                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    $.toast({
                            heading: 'Select row',
                            text: 'Please you have atleast select one item',
                            position: 'top-right',
                            loaderBg:'#ff6849',
                            icon: 'warning',
                            hideAfter: 3500, 
                            stack: 6
                        });
                } else {

                    var join_selected_values = allVals.join(",");
                    console.log(join_selected_values);

                    $.ajax({
                        url: '{{ route('admin.contant-enticeallcontacts') }}',
                        type: 'GET',
                        data: 'ids=' + join_selected_values,
                        beforeSend: function() {
                            $("#loader").show();
                            $("#showdatatable").hide();
                        },
                        success: function(data) {
                            console.log(data)
                        },
                        error: function(data) {
                            console.log(data)
                        },
                        complete: function(data) {
                            $("#loader").hide();
                            $("#showdatatable").show();
                        }
                    });

                }
            };
        </script>


    @endpush
