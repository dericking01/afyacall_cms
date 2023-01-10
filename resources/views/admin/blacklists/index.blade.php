@extends('layouts.app')

@push('before-styles')

<link rel="stylesheet" type="text/css" href="/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">

@endpush

@push('after-scripts')
<script src="{{asset('assets/plugins/datatables/datatables.min.js')}}"  type="text/javascript"></script>

<script src="{{asset('js/buttons.min.js')}}"></script>
<script src="{{asset('js/buttons.flash.min.js')}}"></script>
<script src="{{asset('js/jszip.min.js')}}"></script>
<script src="{{asset('js/pdfmake.min.js')}}"></script>
<script src="{{asset('js/vfs_fonts.js')}}"></script>
<script src="{{asset('js/buttons.html5.min.js')}}"></script>
<script src="{{asset('js/buttons.print.min.js')}}"></script>
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
        <h3 class="text-themecolor mb-0 mt-0">Blacklists</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#") }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Blacklists</li>
        </ol>
    </div>
    <div class="col-md-6 col-4 align-self-center">
        <div class="dropdown float-right mr-2 hidden-sm-down">
            <button class="btn float-right hidden-sm-down btn-success" data-toggle="modal" data-target=".bs-example-modal-lg" style="margin:5px;"><i class="mdi mdi-plus-circle"></i> Add Blacklist </button>
            <button class="btn btn-secondary" type="button"  data-toggle="modal" data-target="#importModal" data-backdrop="static" data-keyboard="false" style="margin:5px;" ><i class="mdi mdi-plus-circle"></i>  Import Contacts </button>
           
        </div>
    </div>
   
</div>

<div class="row">
    <div class="col-12">
        <div class="card">

                            
            <div class="center-block" id='loader' style='display: none; margin: -50px 0px 0px -50px;position: absolute;top: 50%;left: 50%;'>
                <img src='{{ asset('assets/images/loading.gif')}}' width='50px' height='50px'>
            </div> 
            

            <div id="showdatatable" class="card-body">

		<h4 class="card-title">Blacklists Lists</h4>

                    <form action="{{ route('admin.blacklist-search') }}" method="post" role="search">
                        @csrf
                        <div class="row">
                            <div class="col-10">
                                <input type="text" placeholder="Search.." name="q" class="form-control">
                            </div>

                            <div class="col-2">
                                <button type="submit" class="btn btn-primary">Search Number <i
                                        class="fa fa-search fa-sm"></i></button>
                            </div>
                        </div>

                    </form>
                    <br>


                <div  class="table-responsive">
                    <table id="example236" class="table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Contact</th>
                            <th>Reasons</th>
                            <th>Added by</th>
                            <th>Added on</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($blacklists as $key => $blacklist)
                            <tr >
                                <td>
                                   {{ $no++ }}
                                </td>

                                <td>
                                    {{ $blacklist->msisdn ?? '' }}
                                </td>
                               
                                <td>
                                    {{ $blacklist->reason ?? '' }}
                                </td>

                                <td>
                                    {{ $blacklist->user['name'] ?? '' }}
                                </td>

                                <td>
                                    {{ $blacklist->created_at ?? '' }}
                                </td>
                                <td>

                                    <form action="{{ route('admin.blacklist.destroy', $blacklist->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger"  value="Remove Contact">
                                    </form>

                                 </td>
                               
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {!! $blacklists->links() !!}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <form action="{{ route("admin.blacklist-import")}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Blacklist Contacts</h5>
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

    
<!-- sample modal content -->
<div id="representative" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                <form action="{{route('admin.blacklist.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="recipient-name" class="control-label">Number:</label>
                        <input type="number" placeholder="2557xxxxxxxx" class="form-control" name="msisdn" id="msisdn">
                    </div>

                    <div class="mb-3">
                        <label for="message-text" class="control-label">Reason:</label>
                        <textarea class="form-control" id="reason" name="reason"></textarea>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-success">Comfirm</button>
                    </div>
                </form>

            </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection
