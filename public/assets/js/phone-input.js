$('.phone-input').on('input', function() {
  var phoneValue = $(this).val();
  var cleanedValue = phoneValue.replace(/[^0-9]/g, '');
  $(this).val(cleanedValue);
});