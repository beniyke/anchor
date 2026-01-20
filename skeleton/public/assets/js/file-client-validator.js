function toBytes(input) {

	let number = parseInt(input);

	const units = {
		'bt': 1,
		'kb': 1024,
		'mb': 1048576,
		'gb': 1073741824
	};

	const unit = input.trim().toLowerCase().slice(-2);

	if (units[unit]) {
		number = number * units[unit];
	}

	return number;
}

$('.file').change(function() {
  var $this = $(this);
  var $info = $($this.data('info'));
  var formats = $this.data('formats').split(',').map(function(item) {
	  return item.trim();
	});
  var max_upload_size = $this.data('maxsize');
  var file_data = this.files[0];
  var file_format = file_data.name.split('.').pop().toLowerCase();

  if ((file_data.size) > toBytes(max_upload_size)) {
    $this.val('');
    $info.html(`<span class="form-hint small text-danger">File should not be more than ${max_upload_size}</span>`);
    return;
  }
  
  if (!formats.includes(file_format)) {
    $this.val('');
    $info.html(`<span class="form-hint small text-danger">File should be one of the following formats: ${formats}</span>`);
    return;
  }

  $info.html('<span class="form-hint small text-success">File is valid</span>');
});
