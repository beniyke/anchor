$(document).ready(function() {
    function updateActionButtonVisibility(actionClass) {
        const allItemCheckboxes = $(".associated-checkbox");
        const checkedCount = allItemCheckboxes.filter(':checked').length;
        const $actionButtons = $('.' + actionClass);

        if (checkedCount === 0) {
            $actionButtons.addClass('d-none');
        } else {
            $actionButtons.removeClass('d-none');
        }
    }

    $("#all-checkbox").on('click', function() {
        const isChecked = $(this).prop('checked');
        const actionClass = $(this).data('action');
        $(".associated-checkbox").prop('checked', isChecked);
        updateActionButtonVisibility(actionClass);
    });

    $(".associated-checkbox").on('change', function() {
        const $ckbCheckAll = $("#all-checkbox");
        const actionClass = $(this).data('action');
        if (!$(this).prop("checked")) {
            $ckbCheckAll.prop("checked", false);
        }

        updateActionButtonVisibility(actionClass);
        if ($(".associated-checkbox:checked").length === $(".associated-checkbox").length) {
            $ckbCheckAll.prop("checked", true);
        }
    });
});