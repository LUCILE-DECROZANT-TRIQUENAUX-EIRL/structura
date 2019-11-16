$(document).ready(function ($) {

    /*
     * -----------------------------------------
     * --- Configure the input helpers style ---
     * -----------------------------------------
     */

    /**
     * Add emphasis on the input helper text given in parameters
     *
     * @param Object inputHelper the input helper needing emphasis
     */
    function emphaseInputHelper(inputHelper) {
        inputHelper.css({"font-size":"110%"});
        inputHelper.removeClass('text-muted');
        inputHelper.addClass('text-primary');
    }

    /**
     * Reset the input helper text given in parameters to its original look
     *
     * @param Object inputHelper the input helper needing reset
     */
    function regularizeInputHelper(inputHelper) {
        inputHelper.css({"font-size":"80%"});
        inputHelper.addClass('text-muted');
        inputHelper.removeClass('text-primary');
    }

    /*
     * --------------------------------------------------
     * --- Configure the checker of cell phone number ---
     * --------------------------------------------------
     */
    // Add an animated transition on the help text
    $('#app_user_cellPhoneNumber_help').css({transition : 'all 0.1s ease-in-out'});

    // User is deleting things in the input
    // Reset of the helper classes
    $('[name="app_user[cellPhoneNumber]"]').keydown(function (e) {
        if (e.which == 8)
        {
            regularizeInputHelper($('#app_user_cellPhoneNumber_help'));
            return true;
        }
    });

    // User is quitting the input
    // There is no need anymore of emphasis
    $('[name="app_user[cellPhoneNumber]"]').focusout(function () {
        regularizeInputHelper($('#app_user_cellPhoneNumber_help'));
        return true;
    })

    $('[name="app_user[cellPhoneNumber]"]').keypress(function (e) {
        $(this).attr('maxlength', '14');

        // Check if the character is a number, prevent it if true
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
        {
            emphaseInputHelper($('#app_user_cellPhoneNumber_help'));
            e.preventDefault();
            return false;
        }

        var characterPressed = String.fromCharCode(e.which);
        var currentCharacterPosition = this.value.length;

        if (currentCharacterPosition == 0 && characterPressed !== '0')
        {
            // User is trying to enter an invalid phone number
            // Stop the user from writing the phone number
            emphaseInputHelper($('#app_user_cellPhoneNumber_help'));
            e.preventDefault();
            return false;
        }
        else if (currentCharacterPosition == 1 && (characterPressed == 6 || characterPressed == 7))
        {
            // Reset input helper to its default look
            regularizeInputHelper($('#app_user_cellPhoneNumber_help'));
            return true;
        }
        else if (currentCharacterPosition == 1 && (characterPressed != '6' || characterPressed != '7'))
        {
            // User is trying to enter an invalid phone number
            // Stop the user from writing the phone number
            emphaseInputHelper($('#app_user_cellPhoneNumber_help'));
            e.preventDefault();
            return false;
        }
        else
        {
            // Reset input helper to its default look
            regularizeInputHelper($('#app_user_cellPhoneNumber_help'));
            return true;
        }
    });

    // Setup the placeholder in the input
    $('[name="app_user[cellPhoneNumber]"]').mask('99 99 99 99 99', {
        placeholder: "__ __ __ __ __"
    });

    /*
     * --------------------------------------------------
     * --- Configure the checker of home phone number ---
     * --------------------------------------------------
     */
    // Add an animated transition on the help text
    $('#app_user_homePhoneNumber_help').css({transition : 'all 0.1s ease-in-out'});

    // User is deleting things in the input
    // Reset of the helper classes
    $('[name="app_user[homePhoneNumber]"]').keydown(function (e) {
        if (e.which == 8)
        {
            regularizeInputHelper($('#app_user_homePhoneNumber_help'));
            return true;
        }
    });

    // User is quitting the input
    // There is no need anymore of emphasis
    $('[name="app_user[homePhoneNumber]"]').focusout(function () {
        regularizeInputHelper($('#app_user_homePhoneNumber_help'));
        return true;
    })

    $('[name="app_user[homePhoneNumber]"]').keypress(function (e) {
        $(this).attr('maxlength', '14');

        // Check if the character is a number, prevent it if true
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
        {
            emphaseInputHelper($('#app_user_homePhoneNumber_help'));
            e.preventDefault();
            return false;
        }

        var characterPressed = String.fromCharCode(e.which);
        var currentCharacterPosition = this.value.length;

        if (currentCharacterPosition == 0 && characterPressed !== '0')
        {
            // User is trying to enter an invalid phone number
            // Stop the user from writing the phone number
            emphaseInputHelper($('#app_user_homePhoneNumber_help'));
            e.preventDefault();
            return false;
        }
        else if (currentCharacterPosition == 1 && (characterPressed == 6 || characterPressed == 7 || characterPressed == 0))
        {
            // User is trying to enter an invalid phone number
            // Stop the user from writing the phone number
            emphaseInputHelper($('#app_user_homePhoneNumber_help'));
            e.preventDefault();
            return false;
        }
        else if (currentCharacterPosition == 1 && (characterPressed != '6' || characterPressed != '7' || characterPressed != '0'))
        {
            // Reset input helper to its default look
            regularizeInputHelper($('#app_user_homePhoneNumber_help'));
            return true;
        }
        else
        {
            // Reset input helper to its default look
            regularizeInputHelper($('#app_user_homePhoneNumber_help'));
            return true;
        }
    });

    // Setup the placeholder in the input
    $('[name="app_user[homePhoneNumber]"]').mask('99 99 99 99 99', {
        placeholder: "__ __ __ __ __"
    });

    /*
     * --------------------------------------------------
     * --- Configure the checker of work phone number ---
     * --------------------------------------------------
     */
    // Add an animated transition on the help text
    $('#app_user_homePhoneNumber_help').css({transition : 'all 0.1s ease-in-out'});

    // User is deleting things in the input
    // Reset of the helper classes
    $('[name="app_user[homePhoneNumber]"]').keydown(function (e) {
        if (e.which == 8)
        {
            regularizeInputHelper($('#app_user_homePhoneNumber_help'));
            return true;
        }
    });

    // User is quitting the input
    // There is no need anymore of emphasis
    $('[name="app_user[workPhoneNumber]"]').focusout(function () {
        regularizeInputHelper($('#app_user_workPhoneNumber_help'));
        return true;
    })

    $('[name="app_user[workPhoneNumber]"]').keypress(function (e) {
        $(this).attr('maxlength', '14');

        // Check if the character is a number, prevent it if true
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
        {
            emphaseInputHelper($('#app_user_workPhoneNumber_help'));
            e.preventDefault();
            return false;
        }

        var characterPressed = String.fromCharCode(e.which);
        var currentCharacterPosition = this.value.length;

        if (currentCharacterPosition == 0 && characterPressed !== '0')
        {
            // User is trying to enter an invalid phone number
            // Stop the user from writing the phone number
            emphaseInputHelper($('#app_user_workPhoneNumber_help'));
            e.preventDefault();
            return false;
        }
        else if (currentCharacterPosition == 1 && (characterPressed == 0))
        {
            // User is trying to enter an invalid phone number
            // Stop the user from writing the phone number
            emphaseInputHelper($('#app_user_workPhoneNumber_help'));
            e.preventDefault();
            return false;
        }
        else if (currentCharacterPosition == 1 && (characterPressed != '0'))
        {
            // Reset input helper to its default look
            regularizeInputHelper($('#app_user_workPhoneNumber_help'));
            return true;
        }
        else
        {
            // Reset input helper to its default look
            regularizeInputHelper($('#app_user_workPhoneNumber_help'));
            return true;
        }
    });

    // Setup the placeholder in the input
    $('[name="app_user[workPhoneNumber]"]').mask('99 99 99 99 99', {
        placeholder: "__ __ __ __ __"
    });


    $('[name="app_user[workFaxNumber]"]').keypress(function (e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
        {
            return false;
        }
        var curchr = this.value.length;
        var curval = $(this).val();
        var phonenumber = "";
        phonenumber = phonenumber + curval;
        if (curchr == 2 && phonenumber[0] == 0)
        {
            $(this).attr('maxlength', '14');
        }
        else if (curchr == 2 && phonenumber[0] != 0)
        {
            e.preventDefault();
        }
    });

    $('[name="app_user[workFaxNumber]"]').mask('99 99 99 99 99', {
        placeholder: "__ __ __ __ __"
    });

    // Remove the masks on submit

    $("#form-create-member").submit(function (event) {
        $('[name="app_user[cellPhoneNumber]"]').unmask();
        $('[name="app_user[homePhoneNumber]"]').unmask();
        $('[name="app_user[workPhoneNumber]"]').unmask();
        $('[name="app_user[workFaxNumber]"]').unmask();
    });

    $("#form-edit-member").submit(function (event) {
        $('[name="app_user[cellPhoneNumber]"]').unmask();
        $('[name="app_user[homePhoneNumber]"]').unmask();
        $('[name="app_user[workPhoneNumber]"]').unmask();
        $('[name="app_user[workFaxNumber]"]').unmask();
    });

    $("#form-editcontact-profile").submit(function (event) {
        $('[name="app_user[cellPhoneNumber]"]').unmask();
        $('[name="app_user[homePhoneNumber]"]').unmask();
        $('[name="app_user[workPhoneNumber]"]').unmask();
        $('[name="app_user[workFaxNumber]"]').unmask();
    });
});
