// Auxiliar functions for login backend actions
function fill_tags_improved(raw_info, tags_input, url) {
    jQuery.ajax({
        url: url + '?data=' + raw_info,
        async: false,
        success: function(data){
            tags_input.importTags(data);
        }
    });
}

function fill_tags(raw_info, target_element, url) {
    jQuery.ajax({
        url: url + '?data=' + raw_info
    }).done(function(data) {
        var tags = data.split(',');
        for (var i = 0; i < tags.length; i++) {
          jQuery(target_element).tagsinput('add', tags[i]);
        }
    });
}

function load_ajax_in_container(url, container) {
    jQuery.ajax({
        url: url,
        async: true,
        beforeSend: function() {
            container.html('<div class="spinner"></div>Loading request...');
        },
        success: function(data) {
            container.html(data);
        }
    });
}

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

    $('.nav-tabs-tabdrop .nav-pills, .nav-tabs-tabdrop .nav-tabs').tabdrop();

    $('#formulario').on('submit', function(){
      var btn = $('.btn.btn-primary');
      btn.attr('disabled', true);
      $('.btn.btn-primary .text').html(btn.data('text'));
    });

    var unsaved = false;
    if ($('#formulario').length > 0) {
      unsaved = true;
      // Get form values and set unsaved
      var ov = $('#formulario').serialize();
      // Check for CKEditor changes
      for (var i in CKEDITOR.instances) {
        CKEDITOR.instances[i].on('key', function() {
          ov = null;
        });
      }
      // Bind the event
      $(window).bind('beforeunload', function(e) {
        var nv = $('#formulario').serialize();
        if((ov != nv) && unsaved){
            return leaveMessage;
        }
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
