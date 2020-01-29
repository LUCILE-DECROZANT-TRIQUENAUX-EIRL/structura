// Global vars init
var currentMembershipType = null;
var selectedPeopleCount = 0;

$(document).ready(function() {
    // -- Events callback -- //
    $('#add-donation-btn').click(function() {
        // Showing hidden fields
        $('#payment-amount-group').removeClass('d-none');
        $('#donation-amount-group').removeClass('d-none');

        // Adding required attribute on the donation amount field
        $('#app_membership_create_donationAmount').prop('required', true);

        // Setting the isMembershipAndDonation to true
        $('#app_membership_create_isMembershipAndDonation').val(true);

        // Hiding the add donation button
        $('#add-donation-btn').addClass('d-none');
    });

    $('#app_membership_create_donationAmount').keyup(function() {
        updatePaymentAmount();
    });

    $('#app_membership_create_membershipAmount').keyup(function() {
        updatePaymentAmount();
    });

    $('#app_membership_create_membershipType').change(function() {
        getMembershipType($(this).val());
    });

    $('#app_membership_create_newMember').change(function() {
        let selectedPeopleId = $(this).val();
        let selectedPeopleName = $("#app_membership_create_newMember option:selected").html();

        let selectedPeopleCheckbox = $('#app_membership_create_members_' + selectedPeopleId);

        // We only add it if it's not already checked
        if (selectedPeopleCheckbox.prop('checked') != true)
        {
            // Showing the recap to the user
            getPeopleRecap(selectedPeopleId);

            // Ticking the hidden checkbox so we know that
            // this People will be added to the membership
            selectedPeopleCheckbox.prop('checked', true);

            // Updating the selection list by removing the selected people
            removePeopleFromSelectionList();

            // If it's the first add
            if (selectedPeopleCount == 0)
            {
                // We're enabling the payer field
                $('#app_membership_create_payer').removeAttr('readonly');
            }

            // We're adding the selected people to the payer list
            $('#app_membership_create_payer').append('<option value="' + selectedPeopleId + '">' + selectedPeopleName + '</option>');

            // Increasing the counter.
            selectedPeopleCount++;

            // If we have the maximum people selected, we disable the selection list.
            if (selectedPeopleCount == currentMembershipType.number_max_members)
            {
                $('#people-selector-container').addClass('d-none');
            }
        }
    });

    // -- Init -- //
    // Getting the membershipType id.
    let membershipTypeId = $('#app_membership_create_membershipType').val();

    // Getting the membershipType info
    getMembershipType(membershipTypeId);

    // Calculating default membership dates
    let now = new Date();
    let nextYear = new Date();
    nextYear.setFullYear(now.getFullYear() + 1);

    // And setting them
    $('#app_membership_create_membershipDate_start').val(now.toISOString().substr(0, 10));
    $('#app_membership_create_membershipDate_end').val(nextYear.toISOString().substr(0, 10));
});

/**
 * Updates the payment amount based on the membership and the donation amount.
 */
function updatePaymentAmount() {
    let membershipAmount = $('#app_membership_create_membershipAmount').val();
    membershipAmount = membershipAmount == '' ? 0 : parseInt(membershipAmount);

    let donationAmount = $('#app_membership_create_donationAmount').val();
    donationAmount = donationAmount == '' ? 0 : parseInt(donationAmount);

    let paymentAmount = membershipAmount + donationAmount;

    $('#app_membership_create_paymentAmount').val(paymentAmount);
}

/**
 * Get the HTML of the information recap corresponding the to given People id.
 * And adding it to the recap list
 *
 * @param {number} peopleId
 */
function getPeopleRecap(peopleId)
{
    // Making an ajax call to get the recap's HTML
    $.ajax({
        type: "GET",
        url: '/ajax/people/' + peopleId + '/recap',
        cache: false,
        dataType: "html"
    }).done(function(recapHtml) {
        // Adding the recap to the recap list
        $('#people-recaps').append(recapHtml);
    }).fail(function() {
        // TODO: Handle error
    });
}

function getMembershipType(membershipTypeId)
{
    // Making an ajax call to get the MembershipType json
    $.ajax({
        type: "GET",
        url: '/ajax/membership-type/' + membershipTypeId,
        cache: false,
        dataType: "json"
    }).done(function(membershipType) {
        currentMembershipType = membershipType;

        // Updating the help message
        let helpMessage = $('#app_membership_create_membershipType_help').html();
        helpMessage = helpMessage.substring(1);
        helpMessage = membershipType.number_max_members + helpMessage;
        $('#app_membership_create_membershipType_help').html(helpMessage);

        // Setting the membership default amount
        $('#app_membership_create_membershipAmount').val(membershipType.default_amount);

        // Updating the payment amount
        updatePaymentAmount();

        resetSelectedPeople();
    }).fail(function() {
        // TODO: Handle error
    });
}

/**
 * Removes the People's recap given a peopleId.
 *
 * @param {number} peopleId
 */
function removePeopleRecap(peopleId)
{
    $('#people-recap-' + peopleId).remove();
}

function handlePeopleDeletion(peopleId)
{
    // Adding back the people into the selection list
    let peopleName = $('#people-recap-name-'+peopleId).html();
    $('<option value="'+peopleId+'">' + peopleName + '</option>').insertAfter($('#app_membership_create_newMember option[value="'+(peopleId-1)+'"]'));

    // Removing the recap div
    removePeopleRecap(peopleId);

    // Unticking the hidden checkbox so we know that
    // this People will not be added to the membership
    $('#app_membership_create_members_' + peopleId).prop('checked', false);

    // Decreasing the counter.
    selectedPeopleCount--;

    // If we have less than the maximum people selected, we show the selection list.
    if (selectedPeopleCount < currentMembershipType.number_max_members)
    {
        $('#people-selector-container').removeClass('d-none');
    }

    // If there is no more selected people
    if (selectedPeopleCount == 0)
    {
        // We're disabling the payer field
        $('#app_membership_create_payer').attr('readonly', 'readonly');
    }

    // We're removing the selected people from the payer list
    $('#app_membership_create_payer option[value="' + peopleId + '"]').remove();
}

function removePeopleFromSelectionList()
{
    $('#app_membership_create_newMember option:selected').remove();
}

function resetSelectedPeople()
{
    let peopleRecaps = $('#people-recaps').children();

    $.each(peopleRecaps, function(index, peopleRecap) {
        let elementId = peopleRecap.attributes.id.nodeValue;
        let peopleId = elementId.substring(elementId.length - 1);

        handlePeopleDeletion(peopleId);
    });

    // Reset the payer selection list
    $('#app_membership_create_payer').empty();

    // We're disabling the payer field
    $('#app_membership_create_payer').attr('readonly', 'readonly');
}
