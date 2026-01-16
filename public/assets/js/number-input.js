$(".number-input").on("input", function() {
    $(this).val($(this).val().replace(/\D/g, ''));
    if ($(this).val().startsWith("0")) {
        $(this).val($(this).val().replace(/^0+/, ''));
    }
});
