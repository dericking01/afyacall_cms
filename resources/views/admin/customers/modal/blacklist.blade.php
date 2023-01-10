
<!-- sample modal content -->
<div  id="gfgmodal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel" name="myLargeModalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                <form action="{{route('admin.blacklistcustomer')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="customer_id" name="customer_id" >
                
                    <p>Are you sure you want to add this customer to blacklist</p>
                    <p>System will notify the customer on this action and the customer will no longer get service of Afyacall</p>

                    <div class="form-group" >
                        <textarea class="textarea_editor form-control" id="reason" name="reason" rows="5" placeholder="Enter Reason for this" ></textarea>
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
          