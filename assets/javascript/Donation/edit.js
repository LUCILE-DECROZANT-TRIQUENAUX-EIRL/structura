$(document).ready(function() {
    // On click on the button,
    // show the list of people or the profile of the donator
    $('#edit-donator-button').click(function() {
        $('#people-selected').addClass('d-none');
        $('#people-selector').removeClass('d-none');
    });
});