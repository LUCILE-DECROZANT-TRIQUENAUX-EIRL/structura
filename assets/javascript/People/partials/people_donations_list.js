$(document).ready(function() {
    function formatChildRowData(data) {
        let structure = $('#childrow-structure').clone();
        structure
            .attr('id', 'donation-data-' + data.id)
            .removeClass('d-none');

        let paymentContainer = structure.find('#payment-container');
        paymentContainer.find('.date-received').html(data.payment.date_received);
        paymentContainer.find('.date-cashed').html(data.payment.date_cashed);
        paymentContainer.find('.made-by').html(data.payment.payer.denomination + ' ' + data.payment.payer.firstname + ' ' + data.payment.payer.lastname);

        let fiscalReceiptContainer = structure.find('#fiscal-receipt-container');
        fiscalReceiptContainer.find('.fiscal-year').html(data.payment.fiscal_receipt.fiscal_year);
        fiscalReceiptContainer.find('.order-code').html(data.payment.fiscal_receipt.order_code);

        structure.find('#comment-container').html(data.payment.comment === null ? '-' : data.payment.comment);

        return structure.prop('outerHTML');
    }

    let donationsTable = $('#donations-list');
    let donationsDataTable = donationsTable.DataTable({
        'autoWidth': false,
        'dom': '<t>',
        'ajax': {
            'url': donationsTable.data('ajax-url'),
            'method': "GET",
            'dataSrc': "data",
        },
        language: {
            url: '/json/datatable/fr_FR.json',
        },
        createdRow: function (row, data, dataIndex) {
            $(row).attr('title', 'Cliquer pour afficher plus de détails');
            $(row).attr('data-title-opened', 'Cliquer pour cacher les détails');
            $(row).attr('data-title-closed', 'Cliquer pour afficher plus de détails');
            $(row).attr('data-toggle', 'tooltip');
            $(row).tooltip();

            $('td:last-of-type', row).html('<i class="ri-arrow-down-s-line"></i>');
        },
        columns: [
            {
                data: 'origin',
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return row.origin === null ? '-' : row.origin;
                },
            },
            {
                data: 'price',
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return data + '&nbsp;€';
                },
            },
            {
                data: 'date',
                orderable: false,
                render: function (data, type, row) {
                    return data;
                },
            },
            {
                data: 'payment_amount',
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return row.payment.amount + '&nbsp;€';
                },
            },
            {
                data: 'payment_mean',
                orderable: false,
                render: function (data, type, row) {
                    return row.payment.mean;
                },
            },
            {
                data: 'payment_comment',
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return row.payment.comment === null ? '-' : row.payment.comment;
                },
            },
            {
                data: 'details_control',
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return '';
                },
            },
        ],
    });

    donationsDataTable.on('click', 'tr:not(.child-row)', function () {
        let tr = $(this);
        let row = donationsDataTable.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
            tr.removeClass('bg-secondary');
            $(tr).find('.ion-ios-arrow-up').addClass('ion-ios-arrow-down');
            $(tr).find('.ion-ios-arrow-up').removeClass('ion-ios-arrow-up');
            $(tr).attr('title', $(tr).data('title-closed'));
            $(tr).tooltip('dispose');
            $(tr).tooltip();
        } else {
            // Close other rows opened
            if (donationsDataTable.row('.shown').length) {
                $(donationsDataTable.row('.shown').node()).click();
            }
            // Open this row
            row.child(formatChildRowData(row.data()), 'child-row').show();
            tr.addClass('shown');
            tr.addClass('bg-secondary');
            $(tr).find('.ion-ios-arrow-down').addClass('ion-ios-arrow-up');
            $(tr).find('.ion-ios-arrow-down').removeClass('ion-ios-arrow-down');
            $(tr).attr('title', $(tr).data('title-opened'));
            $(tr).tooltip('dispose');
            $(tr).tooltip();
            // Toggle tooltips on generated child row items
            $(row.child()).find('[data-toggle="tooltip"]').tooltip('dispose');
            $(row.child()).find('[data-toggle="tooltip"]').tooltip();
        }
    });
});
