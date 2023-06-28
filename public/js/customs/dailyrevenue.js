$(document).ready(function () {
    var dataTable = initializeDataTable();
  
    $(".shawCalRanges").daterangepicker({
      ranges: {
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [
          moment().subtract(1, "month").startOf("month"),
          moment().subtract(1, "month").endOf("month")
        ]
      },
      alwaysShowCalendars: true
    });
  
    $(".shawCalRanges").on("apply.daterangepicker", function () {
      customerListSearch(dataTable);
    });
  });
  
  function initializeDataTable() {
    return $("#ticketreporttable").DataTable({
      dom: "Bfrtip",
      pageLength: 25,
      buttons: [
        {
          extend: "csv",
          titleAttr: "Click to download as a CSV",
          filename: "Revenue Reports"
        },
        {
          extend: "excel",
          titleAttr: "Click to download as an Excel",
          filename: "Revenue Reports"
        }
      ],
      columnDefs: [
        {
          targets: 1,
          render: function (data) {
            return moment(data).format("LL");
          }
        },
        {
          targets: 6,
          render: function (data) {
            var roundedTotal = Number(parseFloat(data).toFixed(2));
            if (isNaN(roundedTotal)) {
              return "TSh NaN";
            } else {
              return roundedTotal.toLocaleString("sw-TZ", {
                style: "currency",
                currency: "TZS"
              });
            }
          }
        }
      ]
    });
  }
  
  function customerListSearch(dataTable) {
    var startDate = $(".shawCalRanges").data("daterangepicker").startDate.format("YYYY-MM-DD");
    var endDate = $(".shawCalRanges").data("daterangepicker").endDate.format("YYYY-MM-DD");
  
    var ajaxSettings = {
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
      },
      type: "POST",
      url: "{{ route('admin.revenueSearch') }}",
      data: {
        startDate: startDate,
        endDate: endDate
      },
      beforeSend: function () {
        $("#loader").show();
        $("#showdata").hide();
      },
      success: function (response) {
        dataTable.clear().draw();
        var resultData = response.data;
  
        $.each(resultData, function (index, row) {
          dataTable.row.add([
            index + 1,
            row.DateCreated,
            row.sms,
            row.ivr,
            row.calls,
            row.doctor_subs,
            row.total
          ]).draw();
        });
      },
      error: function (xhr, status, error) {
        // Handle AJAX error
        console.error(error);
        // Display appropriate error message to the user
      },
      complete: function () {
        $("#loader").hide();
        $("#showdata").show();
      }
    };
  
    $.ajax(ajaxSettings);
  }
  