
<!-- sample modal content -->
<div id="representative" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">{{$customers->msisdn}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                <form action="{{route('admin.subscribecustomer')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="customer_id" name="customer_id" value="{{$customers->id}}">
                    <input type="hidden" id="status" name="status" value="{{$customers->status}}">
                    @if ($customers->status == 0)
                    <p>Are you sure you want to subscribe this customer</p>
                    <p>System will notify the customer on this action</p>
                    @else
                    <p>Are you sure you want to unsubscribe this customer</p>
                    <p>System will notify the customer on this action</p>
                    @endif
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
          