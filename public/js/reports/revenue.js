$(".shawCalRanges").daterangepicker({
    ranges: {
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
        ],
    },
    alwaysShowCalendars: true,
});

function customerListSearch() {
    var startDate = $(".shawCalRanges")
        .data("daterangepicker")
        .startDate.format("YYYY-MM-DD");
    var endDate = $(".shawCalRanges")
        .data("daterangepicker")
        .endDate.format("YYYY-MM-DD");

    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        url: "{{ route('admin.revenueSearch') }}",
        data: {
            startDate: startDate,
            endDate: endDate,
        },
        beforeSend: function () {
            $("#loader").show();
            $("#showdata").hide();
        },
        success: function (response) {
            var dataTable = $("#ticketreporttable").DataTable({
                destroy: true,
                dom: "Bfrtip",
                pageLength: 25,
                buttons: [
                    {
                        extend: "csv",
                        titleAttr: "Click to download as a CSV",
                        filename: "Revenue Reports",
                    },
                    {
                        extend: "excel",
                        titleAttr: "Click to download as a Excel",
                        filename: "Revenue Reports",
                    },
                ],
                columnDefs: [
                    {
                        targets: 1,
                        render: function (data) {
                            return moment(data).format("LLL");
                        },
                    },
                ],
            });
            dataTable.clear().draw();
            var resultData = response.data;
            $.each(resultData, function (index, row) {
                dataTable.row
                    .add([
                        row.id,
                        row.DateCreated,
                        row.SMS,
                        row.IVR,
                        row.DOCTOR,
                        row.TOTAL,
                    ])
                    .draw();
            });
        },
        complete: function (data) {
            $("#loader").hide();
            $("#showdata").show();
        },
    });
}

