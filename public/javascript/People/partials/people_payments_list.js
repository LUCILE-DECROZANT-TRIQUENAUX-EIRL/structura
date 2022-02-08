$(document).ready(function() {
    let paymentsTable = $('#payments-list');
    paymentsTable.DataTable({
        'autoWidth': false,
        'dom': '<t>',
        'ajax': {
            'url': paymentsTable.data('ajax-url'),
            'method': "GET",
            'dataSrc': "data",
        },
        language: {
            url: '/json/datatable/fr_FR.json',
        },
        columns: [
            {
                data: 'usage',
                orderable: false,
            },
            {
                data: 'amount',
                orderable: false,
                render: function (data, type, row) {
                    return data + '&nbsp;€';
                },
            },
            {
                data: 'mean',
                orderable: false,
            },
            {
                data: 'date_received',
                orderable: false,
            },
            {
                data: 'date_cashed',
                orderable: false,
            },
            {
                data: 'fiscal_year',
                orderable: false,
            },
            {
                data: 'order_code',
                orderable: false,
            },
            {
                data: 'comment',
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return data === null ? '-' : data;
                },
            },
        ],
    });
});
