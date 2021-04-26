var checkGenerationCompleteTimer = null;
$(document).ready(function () {
    // If the tag file doesn't exists
    if ($('#check-generation-tag-pdf').length) {
        // Run an ajax call each 5 sec to check if the generation is complete
        checkGenerationCompleteTimersIdentifier = setInterval(checkIfTagGenerationHasFinished, 3000);
    }

});

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
