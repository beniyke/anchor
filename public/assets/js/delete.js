$(document).ready(function() {
    function openDeleteModal(target, callback, description, action, parameters) {
        $('#delete-target').val(target);
        $('#delete-callback').val(callback);
        $('#delete-description').html(description);
        $('#delete-parameters').val(parameters);
        $('#delete-action').html(action);
        $('#confirm-delete').modal('show');
    }

    $('body').on('click', '.delete', function(e) {
        e.preventDefault();
        const $this = $(this);
        const target = $this.data('delete');
        const callback = $this.data('callback');
        const description = $this.data('description');
        const action = $this.data('action');
        const parameters = $this.data('parameters');

        openDeleteModal(target, callback, description, action, parameters);
    });

    $('body').on('click', '.option', function() {
        const $this = $(this);
        const callback = $this.data('callback');
        const description = $this.data('description');
        const action = $this.data('action');
        const parameters = $this.data('parameters');

        const checkedTargets = $('.chk-box:checked').map(function() {
            return $(this).val();
        }).get().join(',');

        openDeleteModal(checkedTargets, callback, description, action, parameters);
    });
});