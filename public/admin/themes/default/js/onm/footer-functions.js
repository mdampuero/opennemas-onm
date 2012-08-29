jQuery(document).ready(function($) {
    toggleCheckbox = (function() {
        var toggleElement = $('.table .toggleallcheckbox');
        if (toggleElement !== null) {

            // check element if all its dependent checkboxes are checked
            toggleElement.each(function() {
                var allChecked = true;

                $(this).closest('.table').find('tbody input[type=checkbox]').each(function() {
                    allChecked = allChecked && $(this).prop('checked');
                });
                if (allChecked) {$(this).prop('checked', 'checked');}
            });

            $('.table').on('click', '.toggleallcheckbox', function() {
                var toggle = $(this).attr('checked') == 'checked';
                $(this).closest('.table').find('tbody input[type=checkbox]').each(function() {
                    $(this).prop('checked', toggle);
                });
                if (toggle) {
                    jQuery('.old-button .batch-actions').fadeIn('fast');
                } else {
                    jQuery('.old-button .batch-actions').fadeOut('fast');
                }

            });
        }
    })();

    // TODO: think about make this function autoexecutable and avoid call it from views
    toggleTiny = (function() {
        $('.toggle-tinymce').on('click', function(e) {
            var selector = $(this).data('selector-to-hide');
            OpenNeMas.tinyMceFunctions.toggle(selector);
            e.preventDefault();
        });
    })();

    noentersubmit = (function() {
        $('.noentersubmit').keydown(function(event) {
            if (event.keyCode == 13) {
              event.preventDefault();
              return false;
            }
        });
    })();
});

