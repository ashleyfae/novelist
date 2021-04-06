(function ($) {

    /**
     * Trigger checkbox change
     *
     * Shows/hides a target div based on whether or not a checkbox
     * is selected.
     *
     * @param checkbox
     * @param target
     */
    function triggerChange(checkbox, target) {
        var isChecked = '';

        if (checkbox.attr('type') == 'checkbox') {
            isChecked = $('#' + checkbox.attr('id') + ':checked').length > 0;
        } else if ('test' == 'test') {
            var targetValue = checkbox.data('target-value');
            if (checkbox.val() == targetValue) {
                isChecked = true;
            }
        }

        // Show target if it's checked.
        if (isChecked) {
            checkbox.closest('.novelist-row').nextAll(target).slideDown();
        } else {
            checkbox.closest('.novelist-row').nextAll(target).slideUp();
        }
    }

    /**
     * Check to see if target elements should be hidden
     * or closed and execute that trigger.
     */
    function checkValueChange() {
        $('.novelist-checkbox-change').each(function () {
            triggerChange($(this), $(this).data('target'));

            $(this).change(function () {
                triggerChange($(this), $(this).data('target'));
            });
        });
    }

    checkValueChange();

    /**
     * This is needed to ensure we re-run checkValueChange() after
     * a widget is first added and then saved.
     */
    $(document).on('widget-updated widget-added', function (e, widget) {

        checkValueChange();

    });

})(jQuery);