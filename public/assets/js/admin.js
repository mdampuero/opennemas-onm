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
        var password;

        if (form.find('input[name="password"]').length > 0) {
            $('#_password').val('md5:' + hex_md5($('#password').val()));
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
