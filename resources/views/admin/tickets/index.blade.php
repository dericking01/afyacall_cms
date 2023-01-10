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
        <h3 class="text-themecolor mb-0 mt-0">Tickets</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#") }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Tickets</li>
        </ol>
    </div>
    <div class="col-md-6 col-4 align-self-center">
        <div class="dropdown float-right mr-2 hidden-sm-down">
            <button class="btn btn-secondary" type="button"  data-toggle="modal" data-target="#importModal" data-backdrop="static" data-keyboard="false" ><i class="mdi mdi-plus-circle"></i>  Create Ticket </button>
           
        </div>
    </div>
   
</div>

<div class="row">
    <div class="col-12">
        <div class="card">

            <div id="showdatatable" class="card-body">

                <h4 class="card-title">Ticket Lists</h4>

                <div class="row mt-4">
                    <!-- Column -->
                    <div class="col-md-6 col-lg-3 col-xlg-3">
                        <div class="card card-hover">
                            <div class="p-2 rounded bg-light-primary text-center">
                                <h1 class="fw-light text-primary">{{$totalticket}}</h1>
                                <h6 class="text-primary">Total Tickets</h6>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-3 col-xlg-3">
                        <div class="card card-hover">
                            <div class="p-2 rounded bg-light-warning text-center">
                                <h1 class="fw-light text-warning">{{$totalprogressticket}}</h1>
                                <h6 class="text-warning">In Progress</h6>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-3 col-xlg-3">
                        <div class="card card-hover">
                            <div class="p-2 rounded bg-light-success text-center">
                                <h1 class="fw-light text-success">{{$totalopenticket}}</h1>
                                <h6 class="text-success">Opened</h6>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-3 col-xlg-3">
                        <div class="card card-hover">
                            <div class="p-2 rounded bg-light-danger text-center">
                                <h1 class="fw-light text-danger">{{$totalclosedticket}}</h1>
                                <h6 class="text-danger">Closed</h6>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                </div>
                <div  class="table-responsive">
                    <table id="example23" class="table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>#Reference</th>
                            <th>Msisdn</th>
                            <th>Reasons</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>Resolution</th>
                            <th>Updated at</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $key => $ticket)
                            <tr >
                                <td>
                                    {{ $ticket->reference ?? '' }}
                                </td>

                                <td>
                                    {{ $ticket->msisdn ?? '' }}
                                </td>
                               
                                <td>
                                    {{ $ticket->message ?? '' }}
                                </td>

                                <td>
                                    {{ $ticket->product['name'] ?? '' }}
                                </td>

                                <td>
                                    @if ($ticket->status == '-1')
                                    <span class="badge badge-pill badge-success">Closed</span> 
                                    @elseif ($ticket->status  == '0')
                                    <span class="badge badge-pill badge-danger">Open</span>
                                    @elseif ($ticket->status == '1')
                                    <span class="badge badge-pill badge-warning">InProgress</span>
                                    @endif
                                </td>

                                               
                                <td>
                                    {{ $ticket->solution ?? '' }}
                                </td>

                                <td>
                                    {{ $ticket->updated_at ?? '' }}
                                </td>

                                <td>
                                    {{-- <a class="btn btn-xs btn-info"  href="{{ route('admin.ticket.show', $ticket->id) }}">
                                        view more
                                    </a> --}}

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.ticket.show',$ticket->id) }}">Assigned</a>
                                            {{-- <button class="btn btn-secondary" type="button"  data-toggle="modal" data-target="#importModal" data-backdrop="static" data-keyboard="false" ><i class="mdi mdi-plus-circle"></i>  Create Ticket </button> --}}
                                            <button class="dropdown-item" type="button"  data-id="{{ $ticket->id }}" data-toggle="modal" data-target="#ticketresolution" data-backdrop="static" data-keyboard="false" >Closed</button>
                                            <a class="dropdown-item" href="{{ route('admin.ticket.open',$ticket->id) }}">Re-Open</a>
                                        </div>
                                    </div>
                                   
                                </td>
                               
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div  id="importModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Open Ticket Here</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
    
                    <form action="{{ route("admin.ticket.store") }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="recipient-name" class="control-label">Mobile Number :</label>
                            <input type="number" placeholder="255xxxxxxxxx" class="form-control" name="msisdn" id="msisdn">
                        </div>

                        <div class="mb-3">
                            <label for="recipient-name" class="control-label">product:</label>
                            <select class="form-control select2" name="product_id" id="product_id">
                                @foreach($products as $key => $type)
                                  <option value="{{ $key }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="mb-3">
                            <label for="message-text" class="control-label">Message:</label>
                            <textarea class="form-control" id="message" name="message"></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                            <button class="btn btn-success">Submit</button>
                        </div>
                    </form>
    
                </div>
    
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>
    <!-- /.modal -->


    
    <div  id="ticketresolution" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Write resolution and close ticket</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
    
                    <form action="{{route('admin.ticketclose') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="ticket_id" name="ticket_id" value="">
                        <div class="mb-3">
                            <label for="message-text" class="control-label">Resolution:</label>
                            <textarea class="form-control" id="resolution" name="resolution"></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                            <button class="btn btn-success">Close Ticket</button>
                        </div>
                    </form>
    
                </div>
    
            <!-- /.modal-content -->
        </div>
       </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@push('after-scripts')
<script>
    $('#ticketresolution').on('show.bs.modal', function(e) {
        // get information to update quickly to modal view as loading begins
        var opener = e.relatedTarget; //this holds the element who called the modal

        //we get details from attributes
        var myArticleId = $(opener).attr('data-id');
        document.getElementById("ticket_id").value = myArticleId;

    });
</script>
@endpush
