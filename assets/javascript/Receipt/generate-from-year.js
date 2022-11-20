$(document).ready(function () {
    var selectedElement = $('#generate_tax_receipt_from_year_year option:selected');
    // If a PDF has been generated for this fiscal year,
    // display an information message
    if (selectedElement.data('last-generation-date')) {
        showLastGeneratedDateMessage(new Date(selectedElement.data('last-generation-date')));
    }
    // If there is a PDF generation not ended, disable the form
    // and display an explanation message
    if (selectedElement.data('is-under-generation')) {
        disableForm();
        hideLastGeneratedDateMessage();
    }
    // On fiscal year selection,
    // setup info message
    $('#generate_tax_receipt_from_year_year').change(function () {
        if ($(this).find('option:selected').data('last-generation-date')) {
            showLastGeneratedDateMessage(new Date($(this).find('option:selected').data('last-generation-date')));
        } else {
            hideLastGeneratedDateMessage();
        }
        if ($(this).find('option:selected').data('is-under-generation')) {
            hideLastGeneratedDateMessage();
            disableForm();
        } else {
            enableForm();
        }
    });
});

function disableForm() {
    // Setup and show the message
    $('#year').text($(this).find('option:selected').text());
    $('#message-generation-disabled').removeClass('d-none');
    // Disable the submit button
    $('#from-year-submit-button').prop('disabled', true);
    $('#from-year-submit-button').addClass('disabled');
}

function enableForm() {
    // Hide the message
    $('#message-generation-disabled').addClass('d-none');
    // Enable the button
    $('#from-year-submit-button').prop('disabled', false);
    $('#from-year-submit-button').removeClass('disabled');
}

function showLastGeneratedDateMessage(lastGenerationDate) {
    // Setup data
    $('#message-last-generation #last-generation-date').text(
            lastGenerationDate.toLocaleDateString()
    );
    $('#message-last-generation #last-generation-hour').text(
            lastGenerationDate.toLocaleTimeString()
    );
    // Show the message
    $('#message-last-generation').removeClass('d-none');
    // Change button label
    $('#from-year-submit-button').text(
            $('#from-year-submit-button').data('regeneration-label')
    );
}

function hideLastGeneratedDateMessage() {
    // Hide the message
    $('#message-last-generation').addClass('d-none');
    // Change button label
    $('#from-year-submit-button').text(
            $('#from-year-submit-button').data('first-generation-label')
    );
}