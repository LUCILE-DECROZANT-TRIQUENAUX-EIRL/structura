$(document).ready(function() {
    let membershipsTable = $('#memberships-list');
    membershipsTable.DataTable({
        'autoWidth': false,
        'dom': '<t>',
        'ajax': {
            'url': membershipsTable.data('ajax-url'),
            'method': "GET",
            'dataSrc': "data",
        },
        columns: [
            {
                data: 'type_label',
                orderable: false,
                render: function (data, type, row) {
                    let membershipType = $('<div>')
                        .data('toggle', 'tooltip')
                        .attr('title', row.type_description)
                        .html(data)
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
                    return data + '&nbsp;€';
                },
            },
            {
                data: 'payment_mean',
                orderable: false,
            },
            {
                data: 'paymentDates',
                orderable: false,
                render: function (data, type, row) {
                    let paymentDatesContainer = $('<div>');
                    let paymentReceived = $('<small>');
                    let warningIcon = $('<i>')
                        .addClass('icon')
                        .addClass('ion-md-warning')
                    ;
                    if (row.payment_date_received === null) {
                        paymentReceived.html(warningIcon.prop('outerHTML') + '&nbsp;Paiement pas encore reçu');
                    } else {
                        paymentReceived.html('Reçu le ' + row.payment_date_received);
                    }
                    let paymentCashed = $('<small>');
                    if (row.payment_date_cashed === null) {
                        paymentCashed.html(warningIcon.prop('outerHTML') + '&nbsp;Paiement pas encore encaissé');
                    } else {
                        paymentCashed.html('Encaissé le ' + row.payment_date_cashed);
                    }
                    paymentDatesContainer.html(paymentReceived.prop('outerHTML') + '<br>' + paymentCashed.prop('outerHTML'));
                    return paymentDatesContainer.prop('outerHTML');
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
            // {
            //     name: 'nom',
            //     data: 'nom',
            //     orderable: true,
            //     render: function (data, type, row) {
            //         let site = jQuery('<a>')
            //             .attr('href', row.url_show)
            //             .html(`<strong>${row.nom}</strong>`)
            //             .prop('outerHTML');
            //         return site;
            //     },
            // },
            // {
            //     data: 'etage',
            //     orderable: true,
            // },
            // {
            //     data: 'capacite',
            //     orderable: true,
            // },
            // {
            //     data: null,
            //     orderable: false,
            //     className: 'action',
            //     render: function (data, type, row) {
            //         let editButton = jQuery('<a>')
            //             .attr('href', row.url_edit)
            //             .attr('class', 'btn btn-success btn-sm')
            //             .html('<i class="fas fa-edit"></i>&nbsp;Modifier')
            //             .prop('outerHTML');
            //         return editButton;
            //     }
            // },
            // {
            //     data: null,
            //     orderable: false,
            //     className: 'action',
            //     render: function (data, type, row) {
            //         let deleteButton = jQuery('<a>')
            //             .attr('href', row.url_delete)
            //             .attr('class', 'btn btn-danger btn-sm')
            //             .html('<i class="fas fa-trash"></i>&nbsp;Supprimer')
            //             .prop('outerHTML');
            //         return deleteButton;
            //     }
            // },
        ],
    });
});
