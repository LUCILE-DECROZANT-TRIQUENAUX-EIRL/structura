$(document).ready(function () {
    // Togle all datatables of the project
    $(function () {
        $('[data-toggle="datatable"]').each(function (index) {
            let $table = $(this);
            let buttons = [{
                    extend: 'copy',
                    text: '<i class="ion-md-clipboard"></i>',
                    attr: {
                        title: 'Copier dans le presse-papier',
                        id: 'copyButton',
                        'data-toggle': 'tooltip',
                    }
                }, {
                    extend: 'csv',
                    text: '<i class="ion-md-grid"></i>',
                    attr: {
                        title: 'Exporter au format CSV',
                        id: 'exportCsvButton',
                        'data-toggle': 'tooltip',
                    }
                }, {
                    extend: 'pdf',
                    text: '<i class="ion-md-document"></i>',
                    attr: {
                        title: 'Exporter au format PDF',
                        id: 'exportPdfButton',
                        'data-toggle': 'tooltip',
                    }
                },
            ];
            // Check if there is a create button wanted to add it if needed
            if ($table.data('create-label')) {
                if ($table.data('create-path')) {
                    let createButton = {
                        text: '<i class="ion-md-add"></i>',
                        attr: {
                            title: $(this).data('create-label'),
                            id: 'createButton',
                            'data-toggle': 'tooltip',
                            'class': 'btn btn-primary'
                        },
                        action: function () {
                            window.location.href = $(this).data('create-path');
                        }
                    };
                    buttons.push(createButton);
                } else {
                    console.warn('You must define the attribute `data-create-path` in your table.')
                }
            } else if ($table.data('create-path')) {
                console.warn('You must define the attribute `data-create-label` in your table.')
            }

            // Instanciate the DataTable
            $table.DataTable({
                buttons: buttons,
            });
        });
    });

    // Toggle all tooltips of the project
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
});