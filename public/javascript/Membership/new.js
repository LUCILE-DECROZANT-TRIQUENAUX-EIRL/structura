// Global vars init
var currentMembershipType = null;
var selectedPeopleCount = 0;

$(document).ready(function() {
    // Bootstrap select options
    $.fn.selectpicker.Constructor.BootstrapVersion = '4';

    // Init the events
    $('#app_membership_create_paymentAmount').keyup(function() {
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

        // Prevent to trigger the handling for the blank option
        if (selectedPeopleId > 0)
        {
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
                // And update the title to help the user
                if (selectedPeopleCount == currentMembershipType.number_max_members)
                {
                    $('#app_membership_create_newMember').prop('disabled', true);
                    $('#app_membership_create_newMember').selectpicker({title: 'Nombre maximum d\'adhérent·e atteint'});
                    $('#app_membership_create_newMember').selectpicker('refresh');
                }
            }
        }
    });

    // -- Init -- //

    // Adding two spans in the selection list help message
    // For easier value replacement when changing the membership type
    let helpMessage = $('#app_membership_create_newMember_help').html();
    helpMessage = '<span id="newMember-help-number"></span> ' + helpMessage + ' <span id="newMember-help-type"></span>';
    $('#app_membership_create_newMember_help').html(helpMessage);

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

    // We're also doing that for the fiscal year
    $('#app_membership_create_membershipFiscal_year').val(now.getFullYear());
});

/**
 * Updates the payment amount based on the membership and the donation amount.
 */
function updatePaymentAmount() {
    let membershipAmount = $('#app_membership_create_membershipAmount').val();
    membershipAmount = membershipAmount == '' ? 0 : parseInt(membershipAmount);

    let paymentAmount = $('#app_membership_create_paymentAmount').val();
    paymentAmount = paymentAmount == '' ? 0 : parseInt(paymentAmount);

    let donationAmount = paymentAmount - membershipAmount;

    $('#app_membership_create_donationAmount').val(donationAmount);
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
        // Before the placeholders
        $('.people-placeholder').first().before(recapHtml);

        // Removing the placeholder
        removePeoplePlaceholder();
    }).fail(function() {
        // TODO: Handle error
    });
}

/**
 * Set the demanded amount of placeholders div representing a people to add.
 * If there is too much it removes some.
 * If there is too few it adds some.
 *
 * @param {number} placeholderQuantityWanted
 * @return A Promise that resolves or reject the treatment
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
        $('#newMember-help-number').html(membershipType.number_max_members);
        $('#newMember-help-type').html(membershipType.label);

        // Setting the membership and payment default amount
        $('#app_membership_create_membershipAmount').val(membershipType.default_amount);
        $('#app_membership_create_paymentAmount').val(membershipType.default_amount);

        // Updating the payment amount
        updatePaymentAmount();

        // Setting placeholders
        setPeoplePlaceholders(membershipType.number_max_members).then(function (result) {
            // Then reset the selected people
            resetSelectedPeople();
        });

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

/**
 * Removes one people placeholder from the recap list
 */
function removePeoplePlaceholder()
{
    $('.people-placeholder').first().remove();
}

/**
 *
 * @param {*} peopleId
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
 *
 * @param {*} peopleId
 */
function deselectPeople(peopleId)
{
    // Adding back the people into the selection list
    addPeopleToSelectionList(peopleId);

    // Removing the recap div
    removePeopleRecap(peopleId);

    // Unticking the hidden checkbox so we know that
    // this People will not be added to the membership
    $('#app_membership_create_members_' + peopleId).prop('checked', false);

    // Decreasing the counter of selected people
    selectedPeopleCount--;

    // If we have less than the maximum people selected, we enable the selection list.
    // And update the title to help the user
    if (selectedPeopleCount < currentMembershipType.number_max_members)
    {
        $('#app_membership_create_newMember').prop('disabled', false);
        $('#app_membership_create_newMember').selectpicker({title: 'Sélectionnez une personne pour l\'ajouter'});
        $('#app_membership_create_newMember').selectpicker('refresh');
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

/**
 *
 * @param {*} peopleId
 */
function addPeopleToSelectionList(peopleId)
{
    let peopleName = $('#people-recap-name-'+peopleId).html();

    // Adding the people in the select list
    $('#app_membership_create_newMember').append('<option value="'+peopleId+'">' + peopleName + '</option>');

    let selectList = $('#app_membership_create_newMember option');

    // Sorting by value (Aka, People's id)
    selectList.sort(function(a, b) {
        a = a.value;
        b = b.value;

        return a-b;
    });

    // Replacing the list by the sorted list
    $('#app_membership_create_newMember').html(selectList);

    // Selectiong the blank value
    $('.selectpicker').selectpicker('val', '');
}

/**
 *
 */
function removePeopleFromSelectionList()
{
    $('#app_membership_create_newMember option:selected').remove();
    $('.selectpicker').selectpicker('refresh');
}

/**
 *
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

    // Reset the payer selection list
    $('#app_membership_create_payer').empty();

    // We're disabling the payer field
    $('#app_membership_create_payer').attr('readonly', 'readonly');
}