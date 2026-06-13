$(document).ready(function () {
    $('[name^="filter_people"]').on('change', function(event) {
        let urlWithFilters = generateUrlWithFilters();
        window.location.href = urlWithFilters;
    });
});

function generateUrlWithFilters() {
    const urlWithFilters = new URL($('#dematerialized-newsletter').data('dematerializedNewsletterUrl'));

    let formData = $('#dematerialized-newsletter-form').serializeArray();

    formData.forEach(function(data) {
        if (data.name === 'filter_people[departments][]') {
            urlWithFilters.searchParams.append('departements[]', data.value);
        } else if (data.name === 'filter_people[membership_years][]') {
            urlWithFilters.searchParams.append('adhesion_annees[]', data.value);
        } else if (data.name === 'filter_people[donation_years][]') {
            urlWithFilters.searchParams.append('don_annees[]', data.value);
        } else if (data.name === 'filter_people[donation_origins][]') {
            urlWithFilters.searchParams.append('don_origine[]', data.value);
        }
    });

    return urlWithFilters.href;
}