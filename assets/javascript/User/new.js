require('jquery-mask-plugin');

$(document).ready(function() {
    $('.responsibility-description').click(function() {
        let checkbox = $(this).parent('div').find('input');
        checkbox.prop("checked", !checkbox.prop("checked"));
    });
});
