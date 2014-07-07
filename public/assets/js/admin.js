var BackendAuthentication = {

    init: function() {
        var logo = $('#logo');
        var parent = this;

        $('#language').on('change', function(e, ui) {
            document.location.href = '?language=' + $(this).find('option:selected').val();
        });
    }
};


// Auxiliar functions for login backend actions
$(document).ready(function() {

    // Encode password in md5 on backend login
    $('#submit-button').on('click', function(e, ui) {
        e.preventDefault();

        var form = $('#loginform');
        var time = form.find('input[name="time"]').val();
        var password;

        if (form.find('input[name="_password"]').length > 0) {
            password = form.find('input[name="_password"]');
            password.val('md5:' + hex_md5(password.val()));
        } else {
            password = form.find('input[name="password"]');
            password.val('md5:' + hex_md5(hex_md5(password.val()) + time));
        }

        form.submit();
    });

    $('.social-network-connect').on('click', function(e) {
        var btn = $(this);
        var win = window.open(
            $(this).data('url'),
            $(this).attr('id'),
            'height=400, width=400'
        );

        var interval = window.setInterval(function() {
            if (win == null || win.closed) {
                window.clearInterval(interval);

                if (win.success) {
                    window.location.reload();
                }
            }
        }, 1000);
    });
});
