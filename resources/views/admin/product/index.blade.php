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
        <h3 class="text-themecolor mb-0 mt-0">Products</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
            <li class="breadcrumb-item active">Product</li>
        </ol>
    </div>
    @can('create_product')
    <div class="col-md-6 col-4 align-self-center">
        <a class="btn float-right hidden-sm-down btn-success"  href="{{ route("admin.products.create") }}"><i class="mdi mdi-plus-circle"></i> Create Product</a>
    </div>
    @endcan
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Product Lists</h4>
                <div class="table-responsive m-t-40">
                    <table id="subscriptiontable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>icG-Product</th> 
                            <th>ShortCode</th>
                            <th>Status</th> 
                            <th>Price</th> 
                            <th>Descriptions</th>
                            <th>Created at</th>
                            <th>Action</th>
                         
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $key => $product)
                            <tr data-entry-id="{{ $product->id }}">
                                <td>
                                    {{ $product->name ?? '' }}
                                </td>
                              

                                <td>
                                    {{ $product->product_ID ?? '' }}
                                </td>
                                <td>
                                    {{ $product->shortcode ?? '' }}
                                </td>

                                <td>
                                    @if ($product->status  == 1)
                                    <span class="badge badge-pill badge-success">Active</span> 
                                    @else
                                    <span class="badge badge-pill badge-danger">In Active</span>  
                                    @endif
                                </td>

                                <td>
                                    {{ $product->price ?? '' }}
                                </td>

                                <td>
                                    {{ $product->description ?? '' }}
                                </td>


                                <td>
                                  {{ $product->created_at ?? '' }}
                               </td>
                               <td>
                                <a class="btn btn-xs btn-info" href="{{ route('admin.products.edit', $product->id) }}">
                                    <i class="fas fa-edit"></i>  Edit
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="submit" class="btn btn-xs btn-danger"  value="delete">
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
@endsection