$(document).ready(function() {
    function formatChildRowData(data) {
        let structure = $('#childrow-structure').clone();
        structure
            .attr('id', 'membership-data-' + data.id)
            .removeClass('d-none');

        if (data.members.length > 1) {
            // show member card
            structure.find('#members-container').removeClass('d-none');

            // add all members data in the card
            data.members.forEach(function (member) {
                let memberContainer = structure.find('#member-information-container')
                    .clone()
                    .removeAttr('id')
                ;
                memberContainer.find('.denomination').html(member.denomination);
                memberContainer.find('.firstname').html(member.firstname);
                memberContainer.find('.lastname').html(member.lastname);
                structure.find('#members-list').append(memberContainer);
            });
        }

        let paymentContainer = structure.find('#payment-container');
        paymentContainer.find('.date-received').html(data.payment.date_received);
        paymentContainer.find('.date-cashed').html(data.payment.date_cashed);
        paymentContainer.find('.made-by').html(data.payment.payer.denomination + ' ' + data.payment.payer.firstname + ' ' + data.payment.payer.lastname);

        let fiscalReceiptContainer = structure.find('#fiscal-receipt-container');
        fiscalReceiptContainer.find('.fiscal-year').html(data.payment.fiscal_receipt.fiscal_year);
        fiscalReceiptContainer.find('.order-code').html(data.payment.fiscal_receipt.order_code);

        structure.find('#comment-container').html(data.comment === null ? '-' : data.comment);

        return structure.prop('outerHTML');
    }

    let membershipsTable = $('#memberships-list');
    let membershipsDataTable = membershipsTable.DataTable({
        'autoWidth': false,
        'dom': '<t>',
        'ajax': {
            'url': membershipsTable.data('ajax-url'),
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

            $('td:last-of-type', row).html('<i class="icon ion-ios-arrow-down"></i>');
        },
        columns: [
            {
                data: 'type_label',
                orderable: false,
                render: function (data, type, row) {
                    let currentLabel = row.is_current ? ' (en cours)' : '';
                    let membershipType = $('<div>')
                        .data('toggle', 'tooltip')
                        .attr('title', row.type_description)
                        .html(data + currentLabel)
                        .prop('outerHTML');
                    return membershipType;
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
                data: 'membershipDates',
                orderable: false,
                render: function (data, type, row) {
                    return 'Du ' + row.date_start + '<br>au ' + row.date_end;
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
                data: 'comment',
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return data === null ? '-' : data;
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

    membershipsDataTable.on('click', 'tr:not(.child-row)', function () {
        let tr = $(this);
        let row = membershipsDataTable.row(tr);

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
            if (membershipsDataTable.row('.shown').length) {
                $(membershipsDataTable.row('.shown').node()).click();
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
