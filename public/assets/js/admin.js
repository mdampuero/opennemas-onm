var BackendAuthentication = {

    init: function () {
        var logo = $('#logo');
        var parent = this;

        $('#language').on('change', function(e,ui){
            document.location.href = '?language='+$(this).find('option:selected').val();
        });

        parent.redrawLogo(logo);
        $(window).resize(function() {
            parent.redrawLogo(logo);
        });
    },

    redrawLogo: function(logoEl) {
        var window_height = $(window).height();
        var amount = window_height * 0.1;
        logoEl.css({
            'padding-top': amount * 1.8,
            'padding-bottom' : amount
        });
        var message_height = $('.alert').height();
        var logo_height = logoEl.height();
        var form_height = $('.form-wrapper').height();
        var footer_height = $('footer').height();
        if ((message_height + logo_height + form_height + footer_height + 100) >= window_height) {
            $('footer').css({
                'position' : 'relative'
            });
        } else {
            $('footer').css({
                'position' : 'absolute'
            });
        }
    }
};


// Auxiliar functions for login backend actions
$(document).ready(function () {

    // Encode password in md5 on backend login
    $('#submit-button').on('click', function(e, ui){
        e.preventDefault();

        var form = $('#loginform');
        var time = form.find('input[name="time"]').val();
        var password = form.find('input[name="password"]');

        password.val('md5:' + hex_md5(hex_md5(password.val()) + time));

        form.submit();
    });

});