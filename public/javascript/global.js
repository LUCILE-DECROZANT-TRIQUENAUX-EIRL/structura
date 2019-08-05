$(document).ready(function () {
    // Togle all datatables of the project
    $(function () {
        $('[data-toggle="datatable"]').each(function (index) {
            let $table = $(this);

            // Check which columns are exportable
            let exportableColumns = [];
            $table.find('th').each(function (index) {
                if ($(this).data('exportable') === true) {
                    exportableColumns.push(index);
                }
            });

            let buttons = [{
                    extend: 'copy',
                    text: '<i class="ion-md-clipboard"></i>',
                    attr: {
                        title: 'Copier dans le presse-papier',
                        id: 'copyButton',
                        'data-toggle': 'tooltip',
                    },
                    exportOptions: {
                        columns: exportableColumns,
                    }
                }, {
                    extend: 'csv',
                    text: '<i class="ion-md-grid"></i>',
                    attr: {
                        title: 'Exporter au format CSV',
                        id: 'exportCsvButton',
                        'data-toggle': 'tooltip',
                    },
                    exportOptions: {
                        columns: exportableColumns,
                    }
                }, {
                    extend: 'pdf',
                    text: '<i class="ion-md-document"></i>',
                    attr: {
                        title: 'Exporter au format PDF',
                        id: 'exportPdfButton',
                        'data-toggle': 'tooltip',
                    },
                    exportOptions: {
                        columns: exportableColumns,
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
                            window.location.href = $table.data('create-path');
                        }
                    };
                    buttons.push(createButton);
                } else {
                    console.warn('You must define the attribute `data-create-path` in your table.')
                }
            } else if ($table.data('create-path')) {
                console.warn('You must define the attribute `data-create-label` in your table.')
            }

            // Check if there is a specific configuration wanted to fix columns
            let countFixedColumnsRight = 2; // Set default to 2 columns
            let customCountFixedColumnsRight = $table.data('fixed-columns-right'); // Get the custom settings
            if (customCountFixedColumnsRight) {
                if (Number.isInteger(customCountFixedColumnsRight)) { // Check if the parameter is an integer before using it
                    countFixedColumnsRight = customCountFixedColumnsRight;
                } else {
                    console.warn('The attribute `data-fixed-columns-right` has to be an integer.');
                }
            }

            // Check which columns are sortable
            let sortableColumns = [];
            $table.find('th').each(function (index) {
                sortableColumns.push({orderable: $(this).data('sortable') === true});
            });

            // Instanciate the DataTable
            $table.DataTable({
                colReorder: {
                    fixedColumnsRight: countFixedColumnsRight,
                },
                columns: sortableColumns,
                language: {
                    url: '/json/datatable/fr_FR.json',
                },
                buttons: buttons,
                dom: '<"datatable-header"<"datatable-filter"f><"datatable-buttons"B>>t<"datatable-footer"p>',
            });
        });
    });

    // Toggle all tooltips of the project
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    // Toggle all flip cards of the project
    $(function () {
        $('[data-toggle="flip-card"]').each(function (index) {
            // Get all the elements used
            let $flipCard = $(this);
            let $inner = $flipCard.find('.flip-card-inner');
            let $front = $inner.find('.flip-card-front');
            let $back = $inner.find('.flip-card-back');

            // Get the max height between front and back of the card
            let flipCardHeight = Math.max($front.outerHeight(), $back.outerHeight());

            // Set the height of all the card to the good one
            $front.outerHeight(flipCardHeight);
            $back.outerHeight(flipCardHeight);
            $inner.outerHeight(flipCardHeight);
            // Move the back of the card to the right place
            $back.css('top', -flipCardHeight);

            // Show the card (avoid visible artefacts of resizing of the card)
            $front.addClass('visible');
            $back.addClass('visible');
        });
    });
});