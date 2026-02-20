<script type="text/javascript">
    $(document).ready(function() {
        $('.permission-checkbox').on('change', function() {
            const isChecked = $(this).prop('checked');
            const row = $(this).closest('tr');

            if (isChecked) {
                if ($(this).hasClass('permission-section')) {
                    row.find('.permission-manage, .permission-action').prop('checked', true);
                } else if ($(this).hasClass('permission-manage')) {
                    row.find('.permission-action').prop('checked', true);
                    row.find('.permission-section').prop('checked', true);
                } else if ($(this).hasClass('permission-action')) {
                    row.find('.permission-manage, .permission-section').prop('checked', true);
                }
            } else {
                if ($(this).hasClass('permission-section')) {
                    row.find('.permission-manage, .permission-action').prop('checked', false);
                } else if ($(this).hasClass('permission-manage')) {
                    row.find('.permission-action').prop('checked', false);
                }

                if (!$(this).hasClass('permission-section')) {
                    const hasActiveChild = row.find('.permission-manage:checked, .permission-action:checked').length > 0;
                    if (!hasActiveChild) {
                        row.find('.permission-section').prop('checked', false);
                    }
                }
            }
        });
    });
</script>