$(document).ready(function () {
    $(function () {
        /* Displaying revenues graph */
        var chart = $('#revenues-chart');
        var labels = ['','','','','','','','','','','','']
        var donationRevenues = [0,0,0,0,0,0,0,0,0,0,0,0]
        var membershipRevenues = [0,0,0,0,0,0,0,0,0,0,0,0]
        var cumulatedRevenues = [0,0,0,0,0,0,0,0,0,0,0,0]
        $.ajax({
            url: chart.data('url-data'),
            datatype: 'json'
        })
        .done(function (jsonResponse) {
            labels = jsonResponse.data.labels;
            donationRevenues = jsonResponse.data.donation_revenues;
            membershipRevenues = jsonResponse.data.membership_revenues;
            cumulatedRevenues = jsonResponse.data.cumulated_revenues;

            var myChart = new Chart(chart, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Adhésions (€)',
                            data: membershipRevenues,
                            borderColor: 'rgba(1, 186, 239, 1)',
                            backgroundColor: 'rgba(1, 186, 239, 0.3)',
                        }, {
                            label: 'Dons (€)',
                            data: donationRevenues,
                            borderColor: 'rgba(37, 78, 112, 1)',
                            backgroundColor: 'rgba(37, 78, 112, 0.3)',
                        },
                        {
                            label: 'Cumulé (€)',
                            data: cumulatedRevenues,
                            borderColor: 'rgba(175, 78, 122, 1)',
                            backgroundColor: 'rgba(175, 78, 122, 0.3)',
                        }
                    ]
                },
                options: {
                    scales: {
                        yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                    },
                    elements: {
                        point: {
                            radius: 2
                        }
                    }
                }
            })
        });

        /* Opening and closing datatable rows */
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