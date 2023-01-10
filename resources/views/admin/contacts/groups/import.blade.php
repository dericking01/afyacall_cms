@extends('layouts.app')

@push('before-styles')

<link href="{{asset('assets/plugins/bootstrap-switch/bootstrap-switch.min.css')}}" rel="stylesheet">

<link rel="stylesheet" href="{{asset('assets/plugins/dropify/dist/css/dropify.min.css')}}">

@endpush

@push('after-scripts')
<script>
    $(document).ready(function() {
 
         $("#inputupload").click(function () {
 
             if ($(this).is(":checked")) {
                 $("#inputview").hide();
                 $("#uploadview").show();
             } else {
                $("#inputview").show();
                 $("#uploadview").hide();
             }
         });
     });
 
</script>
<script>
  const messageEle = document.getElementById('message');
  const counterEle = document.getElementById('counter');

  const receipts = document.getElementById('receipts');
  const receipts_counter = document.getElementById('counterreceipts');

  messageEle.addEventListener('input', function (e) {
    const target = e.target;

    // Get the `maxlength` attribute
    const maxLength = target.getAttribute('maxlength');

    // Count the current number of characters
    const currentLength = target.value.length;

    counterEle.innerHTML = `${currentLength}/${maxLength}`;
});

    receipts.addEventListener('input', function (e) {
    const target = e.target;

    // values = textArea.value.split(',');
    // Count the current number of characters
    const totalnumber = target.value.split(',').length;

    receipts_counter.innerHTML = `${totalnumber}`;
});

</script>

<!-- bt-switch -->
<script src="{{asset('assets/plugins/bootstrap-switch/bootstrap-switch.min.js')}}"></script>
<!-- jQuery file upload -->
<script src="{{ asset('assets/plugins/dropify/dist/js/dropify.min.js')}}"></script>
<script>
    $(document).ready(function() {
        // Basic
        $('.dropify').dropify();

        // Used events
        var drEvent = $('#input-file-events').dropify();

        drEvent.on('dropify.beforeClear', function(event, element) {
            return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
        });

        drEvent.on('dropify.afterClear', function(event, element) {
            alert('File deleted');
        });

        drEvent.on('dropify.errors', function(event, element) {
            console.log('Has Errors');
        });

        var drDestroy = $('#input-file-to-destroy').dropify();
        drDestroy = drDestroy.data('dropify')
        $('#toggleDropify').on('click', function(e) {
            e.preventDefault();
            if (drDestroy.isDropified()) {
                drDestroy.destroy();
            } else {
                drDestroy.init();
            }
        })
    });
</script>

@endpush

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Group Imports [{{ $group->name }}]</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.contact.groups') }}">Group Lists</a></li>
                <li class="breadcrumb-item active">Import Contacts</li>
            </ol>
        </div>

    </div>

    <!-- Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0 text-white">Group Import Contacts for [{{ $group->name }}] </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.contact.group.store.contacts') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-body">
 
                            <div class="form-group">
                                <input type="hidden" class="form-control" value="{{ $group->id }}" name="group_id" id="group_id">
                            </div>
                                <!--/span-->
                                <div class="col-md-12 bt-switch">
                                    <div class="form-group">
                                        <label class="control-label">Recipient<span class="text-danger">*</span>
                                        </label>
                                        <div class="mb-4">
                                            <label class="control-label">Did you want to upload via csv ?  </label>
                                            <input type="checkbox" id="inputupload" name="inputupload">
                                        </div>

                                        <div id="inputview">

                                            <textarea id="receipts" name="receipts" autocomplete="off" rows="4" placeholder="Please input recipients, COMMA required between numbers" class="form-control" spellcheck="false" style="min-height: 43px;"></textarea>
                                            {{-- <textarea type="text" id="address_one" name="address_one" class="form-control"></textarea> --}}
                                            <div class="num-des">
                                                <div>Total <span class="num-weight" id="counterreceipts"> 0 </span> recipients, recipients should begin with international country code(CC).<a data-v-153e71e6="" href="javascript:;" class="text-primary"> No CC in file</a>?</div>
                                            </div>

                                        </div>
                                       
                                    </div>
                                </div>
                                <div class="form-body" id="uploadview" style="display: none">
                                    <div class="col-md-12">
                                        <div class="alert alert-info"> <i class="ti-warning"></i> 
                                            Phone numbers should started from the 2th row of the EXCEL file. <a href="{{ asset('assets/images/samplescvimport.csv') }}" download> <u>Download Template</u></a>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="input-file-max-fs">Drag file or Click here to upload</label>
                                            <input type="file" name="file" id="input-file-max-fs" class="dropify" data-max-file-size="2M" />
                                            <div class="u-des2">Add file less than 2MB(csv supported)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Duplicate Checking</label>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="schedulenow" name="schedule" class="custom-control-input" checked>
                                                        <label class="custom-control-label" for="schedulenow">None</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="schedulelater" name="schedule" class="custom-control-input">
                                                        <label class="custom-control-label" for="schedulelater">Other Groups</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                
                                </div>


                                <div class="col-md-12 row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Import Options</label>
                                            <div class="form-group">
                                                <div class="col-sm-8">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="overwrite" name="overwrite">
                                                        <label class="custom-control-label" for="overwrite">Overwrite existing</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="removedout" name="removedout">
                                                        <label class="custom-control-label" for="removedout">Check Removed-Outs (*SMS)</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="errorreport" name="errorreport">
                                                        <label class="custom-control-label" for="errorreport">Generate error report</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                
                                </div>
                            </div> 
                            <div class="form-actions">
                                <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Submit </button>
                                <button type="button" class="btn btn-inverse">Cancel</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
