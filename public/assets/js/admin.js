// Auxiliar functions for login backend actions
$(document).ready(function() {
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

    $('.nav-pills, .nav-tabs').tabdrop();

    $('#formulario').on('submit', function(){
      var btn = $('.btn.btn-primary');
      btn.attr('disabled', true);
      $('.btn.btn-primary .text').html(btn.data('text'));
    });

    if ($('#formulario').length > 0) {
      // Bind the event
      $(window).bind('beforeunload', function() {
        if(unsaved){
          return leaveMessage;
        }
      });
      // Detect changes on form
      $(':input').on('change', function(){
        unsaved = true;
      });
      // Allow to save changes
      $('#formulario').on('submit', function(){
        unsaved = false;
      });
    }

    // Hide alerts after 5 seconds
    window.setInterval(function() {
      $('.messages .alert').slideDown(2000, function(){
        $(this).remove();
      });
    }, 5000);
});
