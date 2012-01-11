toggleCheckbox  = (
    function () {
        if ($('toggleallcheckbox') != null) {
            $('toggleallcheckbox').observe('click',function (e) {
                var toggle = $('toggleallcheckbox').checked;
                $$('table.listing-table tbody input[type=checkbox]').each(function(check) {
                    check.checked = toggle;
                });
            });
        }
    }
)();

