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

            let tableDomDescription = '';

            // Check if there is a filter bar wanted to add it if needed
            if ($table.data('filter-bar')) {
                tableDomDescription = '<"datatable-header"<"datatable-filter"f><"datatable-buttons"B>>t<"datatable-footer"p>';
            } else {
                // If no buttons wanted, remove the empty header
                if (buttons.length > 0) {
                    tableDomDescription = '<"datatable-header"<"datatable-buttons"B>>t<"datatable-footer"p>';
                } else {
                    tableDomDescription = 't<"datatable-footer"p>';
                }
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

            // Setup columns using settings
            let columns = [];
            $table.find('th').each(function (index) {
                let column = {};

                // Get AJAX data id if set
                if ($(this).data('json-id')) {
                    column.data = $(this).data('json-id');
                } else if ($table.data('ajax-url')) {
                    // If data is loaded using AJAX but column does not use it to display data,
                    // setup column as empty
                    column.data = null;
                    column.defaultContent = '';
                }

                // Setup column as sortable
                column.orderable = $(this).data('sortable') === true;

                // Add custom classes
                column.className = $(this).data('class');

                columns.push(column);
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
            let isRowsOpenable = $table.data('rows'); // Get the custom setting
            // Compute all settings
            let datatableSettings = {
                colReorder: {
                    fixedColumnsRight: countFixedColumnsRight,
                },
                order: orderedColumn,
                columns: columns,
                processing: true,
                language: {
                    url: '/json/datatable/fr_FR.json',
                },
                buttons: buttons,
                dom: tableDomDescription,
                pageLength: countRowsDisplayed,
                initComplete: function (settings, json) {
                    // We show the table that was hidden while datatable was initializing
                    // This prevents the table's raw HTML to be visible before the datatable is fully loaded
                    $table.children('tbody').removeClass('d-none');
                },
                createdRow: function (row, data, dataIndex) {
                    if (isRowsOpenable) {
                        $(row).attr('title', 'Cliquer pour afficher plus de détails');
                        $(row).attr('data-title-opened', 'Cliquer pour cacher les détails');
                        $(row).attr('data-title-closed', 'Cliquer pour afficher plus de détails');
                        $(row).attr('data-toggle', 'tooltip');
                        $(row).tooltip();

                        $('td:last-of-type', row).html('<i class="icon ion-ios-arrow-down"></i>');
                    }
                },
            };

            // Add AJAX url if specified
            if ($table.data('ajax-url')) {
                datatableSettings.ajax = $table.data('ajax-url');
            }

            // Instanciate the DataTable
            let table = $table.DataTable(datatableSettings);

            // Open rows if needed
            if (isRowsOpenable) {
                if (isRowsOpenable === 'openable') {
                    // Add event listener for opening and closing details
                    $table.on('click', 'tr:not(.child-row)', function () {
                        let tr = $(this);
                        let row = table.row(tr);

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
                            if (table.row('.shown').length) {
                                $(table.row('.shown').node()).click();
                            }
                            // Open this row
                            row.child(formatTableChildRow(row.data()), 'child-row').show();
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
                } else {
                    openable = false;
                    console.debug('The rows will not be openable on click.');
                }
            } else {
                openable = false;
                console.debug('The rows will not be openable on click.');
            }
        });
    });

    // Toggle all tooltips of the project
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    // Toggle all Select2 dropdown of the project
    $(function () {
        $('[data-toggle="select2"]').each(function (index) {
            let $select = $(this);

            // Check if there is a selected option placeholder to add it if needed
            let placeholder = null;
            if ($select.data('placeholder')) {
                placeholder = $select.data('placeholder');
            }

            $select.select2({
                'placeholder': placeholder,
            });
        });
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

/**
 * Add or remove the d-none class to an element
 * Depending on if it has it or not
 *
 * @param {string} elementSelector The DOM element's id property
 */
global.toggleDisplayNone = function(elementSelector)
{
    let element = $(elementSelector);
    if (element.hasClass('d-none'))
    {
        element.removeClass('d-none');
    }
    else
    {
        element.addClass('d-none');
    }
}

/**
 * Add the d-none class to an element if it doesn't have it already.
 *
 * @param {string} elementSelector The DOM element's id property
 */
global.addDisplayNone = function(elementSelector)
{
    let element = $(elementSelector);
    if (!element.hasClass('d-none'))
    {
        element.addClass('d-none');
    }
}

/**
 * Remove the d-none class to an element if it already has it.
 *
 * @param {string} elementSelector The DOM element's id property
 */
global.removeDisplayNone = function(elementSelector)
{
    let element = $(elementSelector);
    if (element.hasClass('d-none'))
    {
        element.removeClass('d-none');
    }
}

/**
 * Format HTML using in child rows of an openable DataTable row
 *
 * @param DataTable table
 * @param Object data
 * @returns raw HTML
 */
global.formatTableChildRow = function(data) {
    // Get HTML structure and clone it to fill it with data
    let childRowContent = $('div.childrow-structure').clone();

    // Get settings for each column needing to be filled
    childRowContent.find('*').each(function (index) {
        if ($(this).data('json-id')) {
            let dataId = $(this).data('json-id').split('.');

            let content = data;
            dataId.forEach(function (identifier) {
                content = content[identifier];
            })
            if (content !== null && !$(this).data('is-url')) {
                $(this).html(content);
            } else if (content !== null && $(this).data('is-url')) {
                $(this).attr('href', content);
            } else if ($(this).data('empty-field-needed')) {
                $(this).html('-');
            } else {
                $(this).remove();
            }
        }
    });
    return childRowContent.html();
}
