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
        order: [[ 3, "desc" ]],
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
        <h3 class="text-themecolor mb-0 mt-0">Sms content Type</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route("admin.home") }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Sms content Type</li>
        </ol>
    </div>
    <div class="col-md-6 col-4 align-self-center">

        <a class="btn float-right hidden-sm-down btn-success" href="{{ route("admin.contentstype.create") }}"><i class="mdi mdi-plus-circle"></i> Create Content Type</a>
       
    </div>
   
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">SMS CONTENTS TYPE LIST</h4>
                <div class="table-responsive">
                    <table id="example23" class="table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th style="width:40%">Name</th>
                            <th>Status</th>
                            <th>created at</th>
                            <th>created by</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($contentstypes as $key => $smscontent)
                            <tr >
                                <td>
                                   {{ $smscontent->id }}
                                </td>
                               
                                <td>
                                    {{ $smscontent->name ?? '' }}
                                </td>

                                <td>
                                    @if ($smscontent->is_active  == 1)
                                    <span class="badge badge-pill badge-success">Active</span> 
                                    @else
                                    <span class="badge badge-pill badge-danger">InActive</span>  
                                    @endif
                                </td>
                        
                                <td>
                                    {{ $smscontent->created_at ?? '' }}
                                </td>

                                <td>
                                    {{ $smscontent->user['name'] ?? '' }}
                                </td>
                                <td>
                                <a class="btn btn-xs btn-info" href="{{ route('admin.contentstype.edit', $smscontent->id) }}">
                                    <i class="fas fa-edit"></i>  edit
                                </a>
                                    {{-- <form action="{{ route('admin.contentstype.destroy', $smscontent->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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

@endsection