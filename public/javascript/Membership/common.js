//////////////////////////////////////
// -- Global vars initialisation -- //
//////////////////////////////////////
var currentMembershipType = null;
var selectedPeopleCount = 0;

///////////////////////////////////
// -- Document ready listener -- //
///////////////////////////////////
$(document).ready(function() {
    /*
     * Show bank select if payment type needs it
     */
    let isBankNeeded = $("option:selected", '#app_membership_paymentType').data('is-bank-needed') === undefined ? false : true;
    isBankNeeded ? showCheckInformationForm() : hideCheckInformationForm();

    // -- Changing the paymentAmount type by cloning it, to add html5 validation -- //
    // Note : In symfony 5.2 the type can be set in the FormType
    // We'll need TODO this when we will update our version
    $('#app_membership_paymentAmount')
            .clone()
            .attr('type','number')
            .insertAfter('#app_membership_paymentAmount')
            .prev()
            .remove();

    // -- Declaration of the event listeners -- //
    $('#app_membership_paymentAmount').keyup(function() {
        updatePaymentAmount();
    });
    $('#app_membership_membershipAmount').keyup(function() {
        updatePaymentAmount();
    });

    /*
     * Setting the payment amount to null on focus if value is 0
     */
    $('#app_membership_paymentAmount').focus(function() {
        if ($('#app_membership_paymentAmount').val() == 0) {
            $('#app_membership_paymentAmount').val('');
        }
    });

    /*
     * Update the form when user change the membership type
     */
    $('#app_membership_membershipType').change(function(event) {
        // If it's not the placeholder that have been selected
        if ($(this).val() > 0)
        {
            getMembershipType($(this).val());
            removeDisplayNone('#member-selection-part');
            addDisplayNone('#payment-part');
        }
        else
        {
            addDisplayNone('#member-selection-part');
            addDisplayNone('#payment-part');
        }
    });

    /*
     * Show or hide the check information depending on payment type
     */
    $('#app_membership_paymentType').change(function(event) {
        let isBankNeeded = $("option:selected", this).data('is-bank-needed') === undefined ? false : true;
        isBankNeeded ? showCheckInformationForm() : hideCheckInformationForm();
    });

    /*
     * Udpate the form when user select a new people for the membership
     */
    $('#app_membership_newMember').change(function() {
        let selectedPeopleId = $(this).val();

        // Prevent to trigger the handling for the blank option
        if (selectedPeopleId > 0)
        {
            let selectedPeopleName = $("#app_membership_newMember option:selected").html();
            let selectedPeopleCheckbox = $('#app_membership_members_' + selectedPeopleId);

            // We only add it if it's not already checked
            if (selectedPeopleCheckbox.prop('checked') != true)
            {
                selectPeople(selectedPeopleId, selectedPeopleName);
            }
        }
    });

    // -- Initialisation at page loading -- //

    // Adding two spans in the selection list help message
    // For easier value replacement when changing the membership type
    let helpMessage = $('#app_membership_newMember_help').html();
    helpMessage = '<span id="newMember-help-number"></span> ' + helpMessage + ' <span id="newMember-help-type"></span>';
    $('#app_membership_newMember_help').html(helpMessage);

    /*
     * Copy check date values into payment date input
     */
    $('#check-date').val($('#app_membership_paymentDate_received').val());
    $('#check-date').on('input', function () {
        $('#app_membership_paymentDate_received').val($(this).val());
    });
    $('#app_membership_paymentDate_received').on('input', function () {
        $('#check-date').val($(this).val());
    });

    /*
     * Copy check issuer values into payment payer input
     */
    $('#check-issuer').val($('#app_membership_payer').val());
    $('#check-issuer').on('change', function () {
        $('#app_membership_payer').val($(this).val());
    });
    $('#app_membership_payer').on('change', function () {
        $('#check-issuer').val($(this).val());
    });

    /*
     * Catch form submission to reenable select (readonly not available for this element)
     */
    $('form').on('submit', function () {
        $('#app_membership_payer').prop('disabled', false);
    });
});

//////////////////////////////////
// -- Functions declarations -- //
//////////////////////////////////

/**
 * Updates the donation and payment amount based on the membership amount.
 */
function updatePaymentAmount() {
    let membershipAmount = $('#app_membership_membershipAmount').val();
    membershipAmount = membershipAmount == '' ? 0 : parseInt(membershipAmount);

    let paymentAmount = $('#app_membership_paymentAmount').val();
    paymentAmount = paymentAmount == '' ? 0 : parseInt(paymentAmount);

    let donationAmount = paymentAmount - membershipAmount;

    // Update the donation amount
    $('#app_membership_donationAmount').val(donationAmount);
}

/**
 * Get the HTML of the information recap corresponding the to given People id.
 * And adding it to the recap list.
 *
 * @param {number} peopleId The id of the person which you want the recap.
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
        // Before the placeholders
        $('.people-placeholder').first().before(recapHtml);

        // Removing the placeholder
        removePeoplePlaceholder();
    }).fail(function() {
        // TODO: Handle error
    });
}

/**
 * Set the demanded amount of placeholders div representing a person to add.
 * If there is too much it removes some.
 * If there is too few it adds some.
 *
 * @param {number} placeholderQuantityWanted
 * @returns {Promise} A promise that resolves if the call worked as intended or rejects elsewhere.
 */
function setPeoplePlaceholders(placeholderQuantityWanted)
{
    return new Promise(function (resolve, reject)
    {
        if (placeholderQuantityWanted > 0)
        {
            // Making an ajax call to get the placeholder's HTML
            $.ajax({
                type: "GET",
                url: '/ajax/people/placeholder',
                cache: true,
                dataType: "html"
            }).done(function(placeholderHtml) {
                let placeholdersCount = $('.people-placeholder').length;
                let placeholdersNeeded = placeholderQuantityWanted - placeholdersCount;

                if (placeholdersNeeded > 0)
                {
                    for(let i = 0; i < placeholdersNeeded; i++)
                    {
                        // Adding the placeholder to the recap list
                        $('#people-recaps').append(placeholderHtml);
                    }
                }
                else if (placeholdersNeeded < 0)
                {
                    for(let i = placeholdersNeeded; i < 0; i++)
                    {
                        removePeoplePlaceholder();
                    }
                }

                // Instruction to signify that the function treatment is done
                resolve();
            }).fail(function() {
                // TODO: Handle error
                reject();
            });
        }
        else {
            // Unvalid quantity, we reject for the promise and do nothing
            reject();
        }
    });
}

/**
 * Get a membership type informations via an ajax call.
 * The retreived informations are then saved in global vars for reusability.
 * Also updates the form's fields that depends on theses informations.
 *
 * @param {number} membershipTypeId The id of the membership which you want the data.
 * @returns {Promise} A promise that resolves if the call worked as intended or rejects elsewhere.
 */
function getMembershipType(membershipTypeId)
{
    return new Promise(function (resolve, reject)
    {
        // Making an ajax call to get the MembershipType json
        $.ajax({
            type: "GET",
            url: '/ajax/membership-type/' + membershipTypeId,
            cache: false,
            dataType: "json"
        }).done(function(membershipType) {
            // Saving the donation amount
            let oldMembershipAmount, donationAmount;
            let paymentAmount = $('#app_membership_paymentAmount').val();

            if (currentMembershipType != null)
            {
                oldMembershipAmount = currentMembershipType.default_amount;
                donationAmount = paymentAmount - oldMembershipAmount;
            }
            else
            {
                donationAmount = paymentAmount - membershipType.default_amount;
            }

            currentMembershipType = membershipType;

            // Updating the help message
            $('#newMember-help-number').html(membershipType.number_max_members);
            $('#newMember-help-type').html(membershipType.label);

            // Setting the membership and payment default amount
            $('#app_membership_membershipAmount').val(membershipType.default_amount);
            $('#app_membership_paymentAmount').val(membershipType.default_amount + donationAmount);

            // Updating the payment amount
            updatePaymentAmount();

            // Setting placeholders
            setPeoplePlaceholders(membershipType.number_max_members).then(function (result) {
                // Then reset the selected people
                resetSelectedPeople();
                resolve();
            });

        }).fail(function() {
            // TODO: Handle error
            reject();
        });
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

/**
 * Removes one people placeholder from the recap list
 */
function removePeoplePlaceholder()
{
    $('.people-placeholder').first().remove();
}

/**
 * Handler for the manual removal of a person form the membership.
 *
 * @param {number} peopleId The id of the person you want to remove from the membership.
 */
function handlePeopleDeletion(peopleId)
{
    let placeholderQuantityWanted = currentMembershipType.number_max_members - selectedPeopleCount + 1;

    // We wait until the placeholders are added
    setPeoplePlaceholders(placeholderQuantityWanted).then(function(result) {
        // Then we deselect people
        deselectPeople(peopleId);
    });
}

/**
 * Select a person and adds it to the future membership.
 *
 * @param {number} selectedPeopleId The id of the person you want to select.
 * @param {string} selectedPeopleName The fullname of the person you want to select.
 */
function selectPeople(selectedPeopleId, selectedPeopleName)
{
    let selectedPeopleCheckbox = $('#app_membership_members_' + selectedPeopleId);

    // Showing the recap to the user
    getPeopleRecap(selectedPeopleId);

    // Ticking the hidden checkbox so we know that
    // this People will be added to the membership
    selectedPeopleCheckbox.prop('checked', true);

    // Updating the selection list by removing the selected people
    removePeopleFromSelectionList(selectedPeopleId);

    // We're adding the selected people to the payer list
    $('#app_membership_payer').append('<option value="' + selectedPeopleId + '">' + selectedPeopleName + '</option>');
    $('#app_membership_payer').trigger('change');
    $('#check-issuer').append('<option value="' + selectedPeopleId + '">' + selectedPeopleName + '</option>');
    $('#check-issuer').trigger('change');

    // Increasing the counter.
    selectedPeopleCount++;

    // If we have the maximum people selected, we disable the selection list.
    // And update the title to help the user
    if (selectedPeopleCount == currentMembershipType.number_max_members)
    {
        $('#app_membership_newMember').select2({
            placeholder: $('#app_membership_newMember').data('disabled-placeholder'),
            templateSelection: function () {
                return $('#app_membership_newMember').data('disabled-placeholder');
            }
        });
        $('#app_membership_newMember').prop('disabled', true);

        removeDisplayNone('#payment-part');
        removeDisplayNone('#member-creation-submit-button');
    }
    $('#app_membership_newMember').prop('selectedIndex', 0);
}

/**
 * Deselect a person and removes it from the future membership.
 *
 * @param {number} peopleId The id of the person you want to deselect.
 */
function deselectPeople(peopleId)
{
    // Adding back the people into the selection list
    addPeopleToSelectionList(peopleId);

    // Removing the recap div
    removePeopleRecap(peopleId);

    // Unticking the hidden checkbox so we know that
    // this People will not be added to the membership
    $('#app_membership_members_' + peopleId).prop('checked', false);

    // Decreasing the counter of selected people
    selectedPeopleCount--;

    // If we have less than the maximum people selected, we enable the selection list.
    // And update the title to help the user
    if (selectedPeopleCount < currentMembershipType.number_max_members)
    {
        $('#app_membership_newMember').prop('disabled', false);
        $('#app_membership_newMember').select2({
            placeholder: $('#app_membership_newMember').data('placeholder'),
            templateSelection: function () {
                return $('#app_membership_newMember').data('placeholder');
            }
        });

        addDisplayNone('#payment-part');
        addDisplayNone('#member-creation-submit-button');
    }

    // We're removing the selected people from the payer list
    $('#app_membership_payer option[value="' + peopleId + '"]').remove();
    $('#app_membership_payer').trigger('change');
    $('#check-issuer option[value="' + peopleId + '"]').remove();
    $('#check-issuer').trigger('change');
}

/**
 * Add a person to the member selection list.
 *
 * @param {number} peopleId The id of the person you want to add.
 */
function addPeopleToSelectionList(peopleId)
{
    $('#app_membership_newMember option[value="' + peopleId + '"]')
            .prop('disabled', false);
}

/**
 * Remove a person from the member selection list.
 *
 * @param {number} peopleId The id of the person you want to remove.
 */
function removePeopleFromSelectionList(peopleId)
{
    $('#app_membership_newMember option[value="' + peopleId + '"]')
            .prop('disabled', true);
}

/**
 * Deselect all the people and remove their recap.
 */
function resetSelectedPeople()
{
    let peopleRecaps = $('#people-recaps').children();

    peopleRecaps.each(function(index) {
        let peopleId = $(this).data('people-id');

        // If it's a recap
        if (peopleId != undefined)
        {
            deselectPeople(peopleId);
        }
    });
}

function showCheckInformationForm()
{
    removeDisplayNone('.check-information');
    $('.check-information input').prop('required', true)
    $('.check-information select').prop('required', true)
    $('#app_membership_paymentDate_received').prop('readonly', 'readonly');
    $('#app_membership_payer').prop('disabled', 'disabled');
}

function hideCheckInformationForm()
{
    addDisplayNone('.check-information');
    $('.check-information input').prop('required', false)
    $('.check-information select').prop('required', false)
    $('#app_membership_paymentDate_received').prop('readonly', false);
    $('#app_membership_payer').prop('disabled', false);
}
