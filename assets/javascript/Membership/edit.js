import { getMembershipType, selectPeople } from './common';

///////////////////////////////////
// -- Document ready listener -- //
///////////////////////////////////
$(document).ready(function() {
    // Setup payment amount input to avoid NaN error
    let paymentAmount = parseInt($('#app_membership_paymentAmount').attr('value'));
    $('#app_membership_paymentAmount').val(paymentAmount);

    // -- Initialisation at page loading -- //
    let selectedPeople = $('#app_membership_members').find('input:checked');

    let membershipTypeId = $('#app_membership_membershipType').val();

    // Empty the payer selection list but keep the placeholder
    $('#app_membership_payer option').not(':first').remove();
    $('#check-issuer option').not(':first').remove();

    getMembershipType(membershipTypeId).then(function(value) {
        // Manually reseting the checkboxes
        $('input[type=checkbox]').prop('checked', false);

        selectedPeople.each(function(index) {
            let selectedPeopleId = $(this).val();

            let selectedPeopleOption = $('#app_membership_newMember option[value="' + selectedPeopleId + '"]');
            let selectedPeopleName = selectedPeopleOption.html();

            selectPeople(selectedPeopleId, selectedPeopleName, true);
        });
    });
});
