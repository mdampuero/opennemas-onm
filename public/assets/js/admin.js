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
    $('#loginform').on('submit', function(e, ui) {
        var form = $('#loginform');

        if (form.find('input[name="_password"]').length > 0) {
            var password = form.find('input[name="_password"]').val();

            if (password.indexOf('md5:') === -1) {
                password = 'md5:' + hex_md5(password);
            }

            form.find('input[name="_password"]').val(password);
        }
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
