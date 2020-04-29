// Add shadow under top navbar if scrolled down
$(window).scroll(function () {
    if ($(window).scrollTop() > 10) {
        $('#top-navbar').addClass('navbar-shadowed');
    } else {
        $('#top-navbar').removeClass('navbar-shadowed');
    }
});

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

            let buttons = [];

            // Check if there is a copy in clipboard button wanted to add it if needed
            if ($table.data('copy-button')) {
                let copyButton = {
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
                };
                buttons.push(copyButton);
            }

            // Check if there is a csv export button wanted to add it if needed
            if ($table.data('csv-button')) {
                let csvButton = {
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
                };
                buttons.push(csvButton);
            }

            // Check if there is a pdf export button wanted to add it if needed
            if ($table.data('pdf-button')) {
                let pdfButton = {
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
                };
                buttons.push(pdfButton);
            }

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

            // Check the number of rows wanted (default: 10)
            let countRowsDisplayed = 10; // Set default to 10 rows
            let customCountRowsDisplayed = $table.data('number-rows-display'); // Get the custom settings
            if (customCountRowsDisplayed) {
                if (Number.isInteger(customCountRowsDisplayed)) { // Check if the parameter is an integer before using it
                    countRowsDisplayed = customCountRowsDisplayed;
                } else {
                    console.warn('The attribute `data-number-rows-display` has to be an integer.');
                }
            } else {
                console.debug('There will be the default 10 rows displayed per page.');
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

            // Select which column will be sorted at start
            let orderedColumnIndex = 0; // Set default to 0 (first column)
            let customOrderedColumnIndex = $table.data('ordered-column-index'); // Get the custom setting

            if (customOrderedColumnIndex) {
                if (Number.isInteger(customOrderedColumnIndex)) { // Check if the parameter is an integer before using it
                    orderedColumnIndex = customOrderedColumnIndex;
                } else {
                    console.warn('The attribute `data-ordered-column-index` has to be an integer.');
                }
            } else {
                console.debug('The data will be sorted by default.');
                orderedColumnIndex = 0;
            }
            let orderedColumn = [[orderedColumnIndex, 'asc']];

            // Check if rows are openable
            let openable = {};
            let isRowsOpenable = $table.data('rows'); // Get the custom setting

            if (isRowsOpenable) {
                if (isRowsOpenable === 'openable') {
                    openable = {
                        details: {
                            type: 'column',
                            target: 'tr'
                        }
                    };
                    orderedColumnIndex = customOrderedColumnIndex;
                } else {
                    openable = false;
                    console.debug('The rows will not be openable on click.');
                }
            } else {
                openable = false;
                console.debug('The rows will not be openable on click.');
            }

            // Instanciate the DataTable
            $table.DataTable({
                colReorder: {
                    fixedColumnsRight: countFixedColumnsRight,
                },
                order: orderedColumn,
                columns: sortableColumns,
                responsive: openable,
                language: {
                    url: '/json/datatable/fr_FR.json',
                },
                buttons: buttons,
                dom: '<"datatable-header"<"datatable-filter"f><"datatable-buttons"B>>t<"datatable-footer"p>',
                pageLength: countRowsDisplayed,
                initComplete: function (settings, json) {
                    // We show the table that was hidden while datatable was initializing
                    // This prevents the table's raw HTML to be visible before the datatable is fully loaded
                    $table.children('tbody').removeClass('d-none');
                }
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