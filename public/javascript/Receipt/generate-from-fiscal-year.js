$(document).ready(function () {
    // if there is a PDF generation not ended, disable the form
    // and display an explanation message
    if ($('#generate_tax_receipt_from_fiscal_year_fiscalYear option:selected').data('is-under-generation')) {
        disableForm();
    }
    // On fiscal year selection,
    // if there is a PDF generation not ended, disable the form
    // and display an explanation message
    $('#generate_tax_receipt_from_fiscal_year_fiscalYear').change(function () {
        if ($(this).find('option:selected').data('is-under-generation')) {
            disableForm();
        } else {
            enableForm();
        }
    });
});

function disableForm() {
    // Setup and show the message
    $('#fiscal-year').text($(this).find('option:selected').text());
    $('#message-generation-disabled').removeClass('d-none');
    // Disable the submit button
    $('#from-fiscal-year-submit-button').prop('disabled', true);
    $('#from-fiscal-year-submit-button').addClass('disabled');
}

function enableForm() {
    // Hide the message
    $('#message-generation-disabled').addClass('d-none');
    // Enable the button
    $('#from-fiscal-year-submit-button').prop('disabled', false);
    $('#from-fiscal-year-submit-button').removeClass('disabled');
}
