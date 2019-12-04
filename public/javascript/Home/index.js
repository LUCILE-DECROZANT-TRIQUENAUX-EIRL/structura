$(document).ready(function () {
    $(function () {
        $('#people-waiting-send-up').DataTable().on('responsive-display', function (e, datatable, row, showHide, update) {
            if (showHide) {
                $(datatable.row(row.index()).node()).addClass('row-opened');
            } else {
                $(datatable.row(row.index()).node()).removeClass('row-opened');
            }
        });
    });
});