function hide_alerts () {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove();
    });
}

function toggleCheckbox()  {
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
}

jQuery(document).ready(function($) {

    toggleCheckbox();

    // Hide alerts after 5 seconds
    window.setInterval(function() {
        hide_alerts()
    }, 5000);

    noentersubmit = (function() {
        $('.noentersubmit').keydown(function(event) {
            if (event.keyCode == 13) {
              event.preventDefault();
              return false;
            }
        });
    })();

    jQuery('.navbar ul.nav li.dropdown').hover(function() {
        $(this).addClass('open');
        jQuery(this).find('.dropdown-menu:first').show();
    }, function() {
        $(this).removeClass('open');
        jQuery(this).find('.dropdown-menu:first').hide();
    });



    jQuery(window).scroll(function() {
        var $this = $(this);
        var html_tag = $('html');
        var top_offset = $this.scrollTop();

        if ((top_offset >= 20)) {
            html_tag.addClass('scrolled');
        } else {
            html_tag.removeClass('scrolled');
        }
    });

});

