///////////////////////////////////
// -- Document ready listener -- //
///////////////////////////////////
$(document).ready(function() {
    // Show bank select if payment type needs it
    let isPayedByCheck = $("option:selected", '#donation_payment_type').data('is-bank-needed') === undefined ? false : true;
    isPayedByCheck ? showCheckInformationForm() : hideCheckInformationForm();

    // Show/hide bank select picker depending on payment type
    $('#donation_payment_type').change(function(event) {
        let isPayedByCheck = $("option:selected", this).data('is-bank-needed') === undefined ? false : true;
        isPayedByCheck ? showCheckInformationForm() : hideCheckInformationForm();
    });
});

//////////////////////////////////
// -- Functions declarations -- //
//////////////////////////////////

/**
 * Add or remove the d-none class to an element
 * Depending on if it has it or not
 *
 * @param {string} elementId The DOM element's id property
 */
function toggleDisplayNone(elementId)
{
    let element = $('#' + elementId);

    if (element.hasClass('d-none'))
    {
        element.removeClass('d-none');
    }
    else
    {
        element.addClass('d-none');
    }
}

function showCheckInformationForm()
{
    removeDisplayNone('.check-information');
    $('.check-information input').prop('required', true)
    $('.check-information select').prop('required', true)
}

function hideCheckInformationForm()
{
    addDisplayNone('.check-information');
    $('.check-information input').prop('required', false)
    $('.check-information select').prop('required', false)
}
