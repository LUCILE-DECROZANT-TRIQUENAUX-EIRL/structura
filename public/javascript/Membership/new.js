///////////////////////////////////
// -- Document ready listener -- //
///////////////////////////////////
$(document).ready(function() {
    // -- Initialisation at page loading -- //

    // Manually reseting the checkboxes to prevent autocomplete
    $('input[type=checkbox]').prop('checked', false);

    // Empty the payer selection list but keep the placeholder
    $('#app_membership_payer option').not(':first').remove();
    $('#check-issuer option').not(':first').remove();

    // Calculating default membership dates
    let now = new Date();
    let nextYear = new Date();
    nextYear.setFullYear(now.getFullYear() + 1);

    // And setting them
    $('#app_membership_membershipDate_start').val(now.toISOString().substr(0, 10));
    $('#app_membership_membershipDate_end').val(nextYear.toISOString().substr(0, 10));

    // We're also setting the fiscal year to the current year
    $('#app_membership_membershipFiscal_year').val(now.getFullYear());
});
