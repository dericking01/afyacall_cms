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
                'csv', 'excel'
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
                                    <th>Date</th>
                                    <th>Msisdn</th>
                                    <th>Product</th>
                                    <th>Response</th>
                                    <th>Channel</th>
                                </tr>
                            </thead>
                            <tbody>


                                @foreach ($enticements as $key => $enticement)
                                    <tr>
                                       
                                        <td>
                                           {{ $enticement->requested_at ?? '' }}
                                        </td>

                                        <td>
                                            {{ $enticement->msisdn ?? '' }}
                                        </td>

                                        <td>
                                            {{ $enticement->product['name'] ?? '' }}
                                        </td>
                                          <td>
                                            {{ $enticement->response_description ?? '' }}
                                        </td>
                                         <td>
                                            {{ $enticement->channel ?? '' }}
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