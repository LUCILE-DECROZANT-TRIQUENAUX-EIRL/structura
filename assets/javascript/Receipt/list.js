var checkGenerationCompleteTimersIdentifier = [];
$(document).ready(function () {
    // For each file waiting its generation, run an ajax call each 60sec
    // to check if the generation is complete
    $('.waiting-generation').each(function () {
        checkGenerationCompleteTimersIdentifier[$(this).data('file-id')] = setInterval(checkIfGenerationHasFinished, 30000, $(this));
    });
});

function checkIfGenerationHasFinished(generatingFileDiv)
{
    $(function () {
        let url = generatingFileDiv.data('url-check-generation-complete');
        $.getJSON(
                url,
                function (json) {
                    if (json.isGenerationComplete) {
                        let row = generatingFileDiv.closest('tr');
                        let downloadButton = row.find('.btn');
                        // Change row color
                        row.addClass('table-info');
                        // Hide progress bar
                        generatingFileDiv.find('.progress').addClass('d-none');
                        // Show generation complete message
                        generatingFileDiv.find('.message-generation-complete').removeClass('d-none');
                        // Setup download button
                        downloadButton.attr('href', json.downloadUrl);
                        downloadButton.removeClass('disabled');
                        downloadButton.attr('title', downloadButton.data('generate-title'));
                        downloadButton.tooltip();
                        downloadButton.parent().tooltip('dispose');
                        // Add fake flashbag message
                        let fakeFlashbag = $('#' + generatingFileDiv.data('confirmation-message-id')).clone();
                        fakeFlashbag.find('#year').text($(row.find('td')[0]).text());
                        fakeFlashbag.find('#from-date').text($(row.find('td')[0]).text());
                        fakeFlashbag.find('#to-date').text($(row.find('td')[1]).text());
                        fakeFlashbag.removeClass('d-none');
                        $('#container-alert-messages').append(fakeFlashbag);
                        // Stop the check
                        clearInterval(checkGenerationCompleteTimersIdentifier[generatingFileDiv.data('file-id')]);
                    }
                }
        );
    });
}