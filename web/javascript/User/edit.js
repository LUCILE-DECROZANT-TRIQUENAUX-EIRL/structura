$(document).ready(function() {
    $('.responsability-description').click(function() {
        let checkbox = $(this).parent('div').find('input');
        checkbox.prop("checked", !checkbox.prop("checked"));
    }); 
});