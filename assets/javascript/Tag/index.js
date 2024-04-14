var checkGenerationCompleteTimer = null;
$(document).ready(function () {
    let tagGenerateUrl = $('#generate-tag-button').data('tagGenerateUrl');

    // If the tag file doesn't exists
    if ($('#check-generation-tag-pdf').length) {
        // Run an ajax call each 5 sec to check if the generation is complete
        checkGenerationCompleteTimersIdentifier = setInterval(checkIfTagGenerationHasFinished, 3000);
    }

    $('[name^="generate_tag"]').on('change', function(event) {
        let urlWithFilters = generateUrlWithFilters();
        window.location.href = urlWithFilters;
    });

    $('#generate-tag-button').on('click', function(event) {
        $('#generate-tag-form').attr('action', tagGenerateUrl);
        $('#generate-tag-form').trigger('submit');
    });

});

function generateUrlWithFilters() {
    const urlWithFilters = new URL($('#generate-tag-button').data('tagIndexUrl'));

    let formData = $('#generate-tag-form').serializeArray();

    formData.forEach(function(data) {
        if (data.name === 'generate_tag[departments][]') {
            urlWithFilters.searchParams.append('departements[]', data.value);
        } else if (data.name === 'generate_tag[year]' && data.value != '') {
            urlWithFilters.searchParams.append('annee', data.value);
        }
    });

    return urlWithFilters.href;
}

function checkIfTagGenerationHasFinished()
{
    $(function () {
        let url = $('#check-generation-tag-pdf').data('url');
        $.getJSON(
            url,
            function (json) {
                if (json.isGenerationComplete) {
                    // Stop the check
                    clearInterval(checkGenerationCompleteTimer);
                    // Redirect
                    let redirectUrl = $('#tag-generation-complete').data('url');
                    window.location.href = redirectUrl;
                }
            }
        );
    });
}
