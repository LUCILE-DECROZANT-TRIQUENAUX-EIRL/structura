///////////////////////////////////
// -- Document ready listener -- //
///////////////////////////////////
$(document).ready(function() {
    // -- Initialisation at page loading -- //

    // newMember not empty
    if ($('#app_membership_newMember').val() !== '') {
        // Getting pre-selected member
        let selectedPeople = $('#app_membership_members').find('input:checked');
        let membershipTypeId = $('#app_membership_membershipType').val();

        getMembershipType(membershipTypeId).then(function(value) {
            // Manually reseting the checkboxes
            $('input[type=checkbox]').prop('checked', false);

            selectedPeople.each(function(index) {
                let selectedPeopleId = $(this).val();

                let selectedPeopleOption = $('#app_membership_newMember option[value="' + selectedPeopleId + '"]');
                let selectedPeopleName = selectedPeopleOption.html();

                selectPeople(selectedPeopleId, selectedPeopleName, true);
            });

            // Remove the default d-none
            removeDisplayNone('#member-selection-part');
        });
    } else {
        // Manually reseting the checkboxes to prevent autocomplete
        $('input[type=checkbox]').prop('checked', false);
    }

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
