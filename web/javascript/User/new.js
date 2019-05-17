function selectUserResponsability(event) {
    event.preventDefault();

    let checkbox = $(event).find('input');

    checkbox.prop("checked", !checkbox.prop("checked"));
}