
  $(function() {
  
  var start = moment().subtract(29, 'days');
  var end = moment();

  function cb(start, end) {
      $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

  });

  function updateButton() {
      var startDate = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
      var status = document.getElementById("status").value;

      $.ajax({
        type: 'GET',
        url:"{{ route('admin.daterange')}}",
        data:{startDate:startDate,endDate:endDate,status:status},
        beforeSend: function(){
            $("#loader").show();     
            $("#showdata").hide();
        },
        success:function(response){
          console.log(response)
          var dataTable = $("#reporttable").DataTable({
                'destroy': true,
                columnDefs:[{targets:0, render:function(data){
                    return moment(data).format('MMMM Do YYYY, h:mm:ss a');
                }}]
              });
              dataTable.clear().draw();
              var resultData = response.data;
              $.each(resultData,function(index,row){
                  dataTable.row.add([
                    row.created_at,
                    row.msisdn,
                    row.keyword,
                    row.content,
            ]).draw();
          })
        },
        complete:function(data){
          $("#loader").hide();
          $("#showdata").show();
        }     
      });  
  }


