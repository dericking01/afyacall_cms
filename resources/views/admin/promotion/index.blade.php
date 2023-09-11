@extends('layouts.app')
@push('before-styles')
<link rel="stylesheet" type="text/css" href="/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">
@endpush

@push('after-scripts')
<script src="/assets/plugins/datatables/datatables.min.js"></script>
<!-- start - This is for export functionality only -->
<script src="{{asset('js/buttons.min.js')}}"></script>
<script src="{{asset('js/buttons.flash.min.js')}}"></script>
<script src="{{asset('js/jszip.min.js')}}"></script>
<script src="{{asset('js/pdfmake.min.js')}}"></script>
<script src="{{asset('js/vfs_fonts.js')}}"></script>
<script src="{{asset('js/buttons.html5.min.js')}}"></script>
<script src="{{asset('js/buttons.print.min.js')}}"></script>
<script>

    $('#subscriptiontable').DataTable({
        dom: 'Bfrtip',
        buttons: [
             'csv', 'excel', 'pdf'
        ]
    });
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
</script>

@endpush

@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Promotions</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
            <li class="breadcrumb-item active">promotion</li>
        </ol>
    </div>
    @can('create_product')
    <div class="col-md-6 col-4 align-self-center">
      <div class="dropdown float-right mr-2 hidden-sm-down">
         <button class="btn btn-success" type="button"  data-toggle="modal" data-target="#importModal" data-backdrop="static" data-keyboard="false"><i class="mdi mdi-plus-circle"></i>  Import Contacts </button>
       </div>
     </div>
    @endcan
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Promotion Lists</h4>
                <div class="table-responsive m-t-40">
                    <table id="subscriptiontable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>Promo Name</th>
                            <th>Msisdn</th> 
                            <th>Started at</th> 
                            <th>Ended at</th> 
                            <th>status</th> 
                            <th>Action</th>
                        </tr>
                        </thead>
                           <tbody>
                            @foreach($promotions as $key => $promotion)
                            <tr >

                                <td>
                                    {{ $promotion->name ?? '' }}
                                </td>
                               
                                <td>
                                    {{ $promotion->msisdn ?? '' }}
                                </td>

                                <td>
                                    {{ $promotion->startdate ?? 'Not Yet' }}
                                </td>

                                <td>
                                    {{ $promotion->enddate ?? 'Not Yet' }}
                                </td>
                                <td>
                                    @if ($promotion->status == '1')
                                        <span class="badge badge-pill badge-success">InProgress</span>
                                    @elseif ($promotion->status == '0')
                                        <span class="badge badge-pill badge-primary">Not Started</span>
                                    @endif
                                </td>
                                <td>

                                    <form action="{{ route('admin.promotions.destroy', $promotion->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger"  value="Remove Contact">
                                    </form>

                                 </td>
                               
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                     {!! $promotions->links() !!}
                </div>
            </div>
        </div>
    </div>

        <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <form action="{{ route("admin.promotion-import")}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Promotion Contacts</h5>
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