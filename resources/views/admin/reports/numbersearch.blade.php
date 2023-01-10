@extends('layouts.app')


@push('before-styles')

<link href="{{asset('assets/plugins/timepicker/bootstrap-timepicker.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/plugins/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link href="{{asset('assets/plugins/tablesaw-master/dist/tablesaw.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Number Search</h4>
            <div class="row pt-3">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Number</label>
                        <input type="text" id="msisdn" class="form-control" placeholder="Enter a number">
                    </div>
                </div>
                <!--/span-->
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Date Range</label>
                            <input type='text' class="form-control shawCalRanges" />
                        </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Include</label>
                        <div class="checkbox"> <input id="checkbox0" type="checkbox"><label for="checkbox0"> Status </label></div>
                    </div>
                </div>
                <!--/span-->

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Search </label>
                            <button type="button" class="btn waves-effect waves-light btn-block btn-success" onclick="searchNumber()"><i class="fas fa-search"></i>  Search</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="center-block" id='loader' style='display: none; margin: -50px 0px 0px -50px;position: absolute;top: 50%;left: 50%;'>
        <img src='{{ asset('assets/images/loading.gif')}}' width='50px' height='50px'>
    </div> 

    <div class="alert alert-info" id='nodatafound' style='display: none;'>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">  </button>
        <h3 class="text-info"><i class="fa fa-exclamation-circle"></i> Information</h3> No results containing all your search terms were found.
    </div>


    <div class="card" id="showdata"  style='display: none;'>
        <div class="card">
            <div class="card-body">
                <div class="card-body">
                    <h4 class="card-title">Search Results </h4>
                    <div class="row m-t-40">
                        <!-- Column -->
                        <div class="col-md-6 col-lg-3 col-xlg-3">
                            <div class="card">
                                <div class="box bg-info text-center">
                                    <h1 class="font-light text-white" id="totalmessage"></h1>
                                    <h6 class="text-white">Total Message Sent</h6>
                                </div>
                            </div>
                        </div>
                        <!-- Column -->
                        <div class="col-md-6 col-lg-3 col-xlg-3">
                            <div class="card">
                                <div class="box bg-primary text-center">
                                    <h1 class="font-light text-white" id="totaltransaction"></h1>
                                    <h6 class="text-white">Total Transactions</h6>
                                </div>
                            </div>
                        </div>
                        <!-- Column -->
                        <div class="col-md-6 col-lg-3 col-xlg-3">
                            <div class="card">
                                <div class="box bg-success text-center">
                                    <h1 class="font-light text-white" id="totalsubscriptions"></h1>
                                    <h6 class="text-white">Total Subscriptions</h6>
                                </div>
                            </div>
                        </div>
                        <!-- Column -->
                        <div class="col-md-6 col-lg-3 col-xlg-3">
                            <div class="card">
                                <div class="box bg-dark text-center">
                                    <h1 class="font-light text-white" id="totalcalls"></h1>
                                    <h6 class="text-white">Total Calls</h6>
                                </div>
                            </div>
                        </div>
                        <!-- Column -->
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card" id="showtransactions"  style='display: none;'>
        <div class="card">
            <div class="card-body">
                <h4>Transaction Lists</h4>
                <table id="showtransactionstable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Transaction Date</th>
                            <th>Amount</th>
                            <th>Payment via</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card" id="subscriptionshow"  style='display: none;'>
        <div class="card">
            <div class="card-body">
                <h4>Subscription Lists</h4>
                <table id="showdsubscriptiontable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Subscription Date</th>
                            <th>Product</th>
                            <th>Keyword</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card" id="messageshow"  style='display: none;'>
        <div class="card">
            <div class="card-body">
                <h4>Messages Lists</h4>
                <table id="messageshowtable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Message Date</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('after-scripts')

<script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
<script src="{{asset('assets/plugins/timepicker/bootstrap-timepicker.min.js')}}"></script>
<script src="{{asset('assets/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('assets/plugins/datatables/datatables.min.js')}}"></script>
<script src="{{asset('assets/plugins/toast-master/js/jquery.toast.js')}}"></script>
<script>
    
    $('.shawCalRanges').daterangepicker({
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        alwaysShowCalendars: true,
    });
    
    function searchNumber() {
        
      var startDate = $('.shawCalRanges').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('.shawCalRanges').data('daterangepicker').endDate.format('YYYY-MM-DD');
      var msisdn = document.getElementById("msisdn").value;

      if (!validatePhone(msisdn)) {
        $.toast({
                heading: 'Mobile number provided is invalid.',
                position: 'top-right',
                loaderBg:'#ff6849',
                icon: 'error',
                hideAfter: 3500
            });
            return true;
        }

      $.ajax({
        type: 'GET',
        url:"{{ route('admin.report_numbersearch_results')}}",
        data:{startDate:startDate,endDate:endDate,msisdn:msisdn},
        beforeSend: function(){
            $("#loader").show();     
            $("#showdata").hide();
        },
        success:function(response){
          console.log(response)
          var resultData = response.data;
          if (resultData) {
            updateEmpytResult()
            updateResult(resultData);
          } else {
            updateEmpytResult();
          }
         
        },
        complete:function(data){
       
        }     
      });  
  }
  
  function validatePhone(txtPhone) {
    var filter = /[1-9]{1}[0-9]{9}/;
    if (filter.test(txtPhone)) {
        return true;
    }
    else {
        return false;
    }
}

function updateResult(data) {
    console.log(data);
    $("#showdata").show();
    $("#nodatafound").hide();
    $("#loader").hide();   
    $("#totaltransaction").text(data['transactions_count']);
    $("#totalcalls").text(data['ivrstatistics_count']);
    $("#totalmessage").text(data['logs_count']);
    $("#totalsubscriptions").text(data['opts_count']);

    if (data['transactions']) {
        $("#showtransactions").show();
        var dataTable = $("#showtransactionstable").DataTable({
                'destroy': true,
                columnDefs:[{targets:0, render:function(data){
                    return moment(data).format('LLL');
                }}]
              });
              dataTable.clear().draw();
              var resultData = data['transactions'];
              console.log(resultData)
              $.each(resultData,function(index,row){
                  dataTable.row.add([
                    row.created_at,
                    row.amount_IN,
                    row.currency,
                    row.status,
            ]).draw();
          })
    }

    if (data['opts'].length > 0) {
        $("#subscriptionshow").show();
        var dataTable = $("#showdsubscriptiontable").DataTable({
                'destroy': true,
                columnDefs:[{targets:0, render:function(data){
                    return moment(data).format('LLL');
                }}]
              });
              dataTable.clear().draw();
              var resultData = data['opts'];
              console.log(resultData)
              $.each(resultData,function(index,row){
                  dataTable.row.add([
                    row.created_at,
                    row.product['name'],
                    row.content,
                    row.opt_value
            ]).draw();
          })
    }

    if (data['logs'].length > 0) {
        $("#messageshow").show();
        var dataTable = $("#messageshowtable").DataTable({
                'destroy': true,
                columnDefs:[{targets:0, render:function(data){
                    return moment(data).format('LLL');
                }}]
              });
              dataTable.clear().draw();
              var resultData = data['logs'];
              console.log(resultData)
              $.each(resultData,function(index,row){
                  dataTable.row.add([
                    row.created_at,
                    row.content['message']
            ]).draw();
          })
    }


}

function updateEmpytResult(){
    $("#showdata").hide();
    $("#nodatafound").show();   
    $("#loader").hide();
    $("#showtransactions").hide();
    $("#subscriptionshow").hide();
    $("#messageshow").hide();
}

</script>
@endpush