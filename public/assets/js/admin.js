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

    $('.sidebar li > a').on('click', function (e) {
      var item = $(this).parent();
      var visible = item.hasClass('open');
      var submenu = $(this).next();

      // Close all opened menus
      item.parent().find('li.open .arrow.open').removeClass('open');
      item.parent().find('li.open .sub-menu').slideUp(200, function() {
        item.parent().find('li.open').removeClass('open');
      });

      if ($(this).next().hasClass('sub-menu') === false) {
          return;
      }

      if (!visible) {
          item.find('.arrow').first().addClass('open');

          // Open sub-menu
          submenu.slideDown(200, function() {
            item.addClass('open');
          });
      }

      e.preventDefault();
    });
});
