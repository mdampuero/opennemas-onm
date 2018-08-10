$(document).ready(function() {
  'use strict';

  //**********************************BEGIN MAIN MENU********************************
  $('.page-sidebar li > a').on('click', function(e) {
    var item = $(this).parent();
    var visible = item.hasClass('open');
    var submenu = $(this).next();

    // Close all opened menus
    item.parent().find('li.open .arrow.open').removeClass('open');
    item.parent().find('li.open .sub-menu').slideUp(200);
    item.parent().find('li.open .sub-menu').removeClass('open');
    item.parent().find('li.open').removeClass('open');

    if ($(this).next().hasClass('sub-menu') === false) {
      return;
    }

    if (!visible) {
      // Open sub-menu
      item.addClass('open');
      item.find('.arrow').addClass('open');
      item.find('.arrow').addClass('active');
      submenu.slideDown(200);
    }

    e.preventDefault();
  });

  FastClick.attach(document.body);

  //***********************************BEGIN Lazyload images*****************************
  if ($.fn.lazyload) {
    $('img.lazy').lazyload({
      effect: 'fadeIn'
    });
  }
});

// Opennemas inclusions
(function($) {
  'use strict';
  $('.select2-multi').select2({
    dropdownAutoWidth: true,
    closeOnSelect: false
  });
  $('.select2').select2({
    dropdownAutoWidth: true,
    formatSelection: function(state) {
      var element = state.element;

      if ($(element).parents('.select2').data('label') !== null) {
        return $(element).parents('.select2').data('label') + ': ' + state.text;
      }

      return state.text;
    }
  });
  $('.select2-arrow').append('<i class="select2-arrow-down fa fa-angle-down"></i>');
})($);

// Do not close dropdowm-menus on click if they have the class .keepopen
$(document).on('click', '.dropdown-menu.keepopen', function() {
  event.stopPropagation();

  return false;
});
