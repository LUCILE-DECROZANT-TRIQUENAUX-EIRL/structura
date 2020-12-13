///////////////////////////////////
// -- Document ready listener -- //
///////////////////////////////////
$(document).ready(function() {
    // Show bank select if payment type needs it
    let isBankNeeded = $("option:selected", '#donation_payment_type').data('is-bank-needed') === undefined ? false : true;
    console.debug(isBankNeeded);
    if (isBankNeeded)
    {
        removeDisplayNone('payment-bank');
    }
    else
    {
        addDisplayNone('payment-bank');
    }

    // Show/hide bank select picker depending on payment type
    $('#donation_payment_type').change(function(event) {
        let isBankNeeded = $("option:selected", this).data('is-bank-needed') === undefined ? false : true;
        if (isBankNeeded)
        {
            removeDisplayNone('payment-bank');
        }
        else
        {
            addDisplayNone('payment-bank');
        }
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

/**
 * Add the d-none class to an element if it doesn't have it already.
 *
 * @param {string} elementId The DOM element's id property
 */
function addDisplayNone(elementId)
{
    let element = $('#' + elementId);

    if (!element.hasClass('d-none'))
    {
        element.addClass('d-none');
    }
}

/**
 * Remove the d-none class to an element if it already has it.
 *
 * @param {string} elementId The DOM element's id property
 */
function removeDisplayNone(elementId)
{
    let element = $('#' + elementId);

    if (element.hasClass('d-none'))
    {
        element.removeClass('d-none');
    }
}