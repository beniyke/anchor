class FileUploader {
    constructor(selector, options = {}) {
        this.selector = selector;
        this.options = options;
        this.init();
    }

    init() {
        $(this.selector).on('change', (event) => this.handleFileChange(event));
    }

    handleFileChange(event) {
        const field = $(event.target);
        const formats = field.data('formats').split(',').map(item => item.trim().toLowerCase());
        const maxSize = this.toBytes(field.data('maxsize'));
        const $endpointInput = $('#attachment-endpoint');
        const endpoint = !$endpointInput.val()
          ? field.data('endpoint')
          : $endpointInput.val();
        const token = field.data('bearer');
        const infoContainer = $(field.data('info'));
        const progressBar = $(this.options.progressBar || '.myprogress');
        const progressText = $(this.options.progressText || '#progress');

        infoContainer.html('');

        const file = field.prop('files')[0];
        if (!file) return;

        const fileFormat = file.name.split('.').pop().toLowerCase();
        let proceed = true;

        if (file.size > maxSize) {
            proceed = false;
            field.val('');
            infoContainer.html('<span class="text-danger">File should not be more than ' + field.data('maxsize') + '</span>');
        }

        if (!formats.includes(fileFormat)) {
            proceed = false;
            field.val('');
            infoContainer.html('<span class="text-danger">File should be one of: ' + formats.join(', ') + '</span>');
        }

        if (proceed) {
            $('.status-label').html('Uploading file...');
            this.uploadFile(field, endpoint, token, infoContainer, progressBar, progressText);
        }
    }

    uploadFile(field, endpoint, token, infoContainer, progressBar, progressText) {
        const file = field.prop('files')[0];
        const formData = new FormData();
        
        if (this.options.formData) {
            $.each(this.options.formData, (key, value) => {
                formData.append(key, value);
            });
        }
        
        formData.append("attachment", file);
        $.ajax({
            url: endpoint,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'Authorization': `Bearer ${token}` },
            xhr: () => {
                var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
                xhr.upload.addEventListener('progress', (event) => {
                    if (event.lengthComputable) {
                        const percent = parseInt((event.loaded / event.total) * 100);
                        progressText.text(percent + '%');
                        progressBar.css('width', percent + '%');

                        if (percent === 100) {
                            $('.status-label').html('<strong>Please Wait ...</strong>');
                        }
                    }
                }, false);
                return xhr;
            },
            success: (response) => {
                field.val('');
                if (response.status) {
                    infoContainer.html('<span class="text-success">' + response.message + '</span>');
                    $($('#attachment-target').val()).val(response.data);
                    infoContainer.html('');
                    $('#attachment').modal('hide');
                } else {
                    infoContainer.html('<span class="text-danger">' + response.message + '</span>');
                }
                
                $('.upload-file-box').removeClass('d-none');
                $('#spinner').addClass('d-none');
            },
            beforeSend: () => {
                $('.upload-file-box').addClass('d-none');
                $('#spinner').removeClass('d-none');
            }
        });
    }

    toBytes(sizeString) {
        const units = { KB: 1024, MB: 1024 * 1024, GB: 1024 * 1024 * 1024 };
        const size = parseFloat(sizeString);
        const unit = sizeString.replace(/[^a-zA-Z]/g, '').toUpperCase();
        return size * (units[unit] || 1);
    }
}