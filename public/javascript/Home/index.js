$(document).ready(function () {
    $(function () {
        $('#people-waiting-send-up').DataTable().on('responsive-display', function (e, datatable, row, showHide, update) {
            var row = $(datatable.row(row.index()).node());
            if (showHide) {
                // Add class to show the row is expanded
                row.addClass('row-opened');
                // Change tooltip
                var openedRowTitle = row.data('title-openened')
                row.attr('title', openedRowTitle)
                        .tooltip('_fixTitle')
                        .tooltip('show');
                // Set the icon as chevron up
                row.find('.expand-icon > i').removeClass('ion-ios-arrow-down')
                row.find('.expand-icon > i').addClass('ion-ios-arrow-up')
            } else {
                // Add class to show the row is collapsed
                row.removeClass('row-opened');
                // Change tooltip
                var closedRowTitle = row.data('title-closed')
                row.attr('title', closedRowTitle)
                        .tooltip('_fixTitle')
                        .tooltip('show');
                // Set the icon as chevron down
                row.find('.expand-icon > i').removeClass('ion-ios-arrow-up')
                row.find('.expand-icon > i').addClass('ion-ios-arrow-down')
            }
        });
    });
});