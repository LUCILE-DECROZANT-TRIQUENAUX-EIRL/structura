///////////////////////////////////
// -- Document ready listener -- //
///////////////////////////////////
$(document).ready(function() {
    // -- Initialisation at page loading -- //
    let selectedPeople = $('#app_membership_members').find('input:checked');

    let membershipTypeId = $('#app_membership_membershipType').val();

    // Updating payment amount to consider the donation amount


    getMembershipType(membershipTypeId).then(function(value) {
        // Manually reseting the checkboxes
        $('input[type=checkbox]').prop('checked', false);

        selectedPeople.each(function(index) {
            let selectedPeopleId = $(this).val();

            let selectedPeopleOption = $('#app_membership_newMember option[value="' + selectedPeopleId + '"]');
            let selectedPeopleName = selectedPeopleOption.html();

            selectPeople(selectedPeopleId, selectedPeopleName);
        });
    });
});