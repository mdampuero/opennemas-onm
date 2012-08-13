jQuery(document).ready(function($) {

    toggleCheckbox  = (function () {
        var toggleElement = $('#toggleallcheckbox');
        if (toggleElement !== null) {
            var allChecked = true;
            if ($('table.listing-table tbody input[type=checkbox]').size() > 0) {
                $('table.listing-table tbody input[type=checkbox]').each(function() {
                    allChecked = allChecked && $(this).prop("checked");
                });
                if (allChecked) {toggleElement.prop("checked", "checked");}
            }

            toggleElement.on('click',function () {
                var toggle = toggleElement.attr("checked") == "checked";
                $('table.listing-table tbody input[type=checkbox]').each(function() {
                    $(this).prop("checked", toggle);
                });
                if(toggle){
                    jQuery('.old-button .batch-actions').fadeIn('fast');
                } else {
                    jQuery('.old-button .batch-actions').fadeOut('fast');
                }

            });
        }
    })();

    // TODO: think about make this function autoexecutable and avoid call it from views
    toggleTiny = (function() {
        $(".toggle-tinymce").on('click', function (e) {
            var selector = $(this).data('selector-to-hide');
            OpenNeMas.tinyMceFunctions.toggle(selector);
            e.preventDefault();
        });
    })();

});

