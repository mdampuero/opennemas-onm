
function makeContentProviderAndPlaceholdersSortable() {
  // Make content providers sortable and allow to D&D over the placeholders
  jQuery('div#content-provider .ui-tabs-panel > div:not(.pagination)').sortable({
    connectWith: 'div.placeholder div.content',
    placeholder: 'placeholder-element',
    handle: '.description',
    update: function(event) {
      initializePopovers();
    },
    stop: function(event) {
        showMessage(frontpage_messages.remember_save_positions, 'info', 3, 1234);
    },
    tolerance: 'pointer'
    //containment: '#content-with-ticker'
  }).disableSelection();

  // Make content providers sortable and allow to D&D over placeholders and content provider
  jQuery('div.placeholder div.content').sortable({
    connectWith: 'div#content-provider .ui-tabs-panel > div:not(.pagination), div.placeholder div.content',
    placeholder: 'placeholder-element',
    handle: '.description',
    update: function(event,ui) {
      initializePopovers();
    },
    stop: function(event,ui) {
      showMessage(frontpage_messages.remember_save_positions, 'info', 3, 1234);
    },
    tolerance: 'pointer'
  }).disableSelection();
}

function get_tooltip_content(elem) {
  var parent_content_div = elem.closest('div.content-provider-element');
  var content_html = '';

  if (parent_content_div.data('popover-content') === undefined) {
    var id = parent_content_div.data('content-id');
    var $url = frontpage_urls.quick_info + '?id=' + id;
    var content = '';

    content = content_states[id];
    if (content === undefined) {
      jQuery.ajax({
        url: $url,
        async: false
      }).done(function(data) {
        content_states[id] = data;
      });
    } else {
      content_html = tooltip_strings.state + content.state +
        '<br>' + tooltip_strings.views + content.views +
        '<br>' + tooltip_strings.category + content.category +
        "<br>" + tooltip_strings.schedule +
          "<span class='scheduled-state " + content.scheduled_state +
          "'>" + content.scheduled_state + '</span>'+
        '<br>' + tooltip_strings.starttime + content.starttime +
        '<br>' + tooltip_strings.last_author + content.last_author;
      parent_content_div.data('popover-content', content_html);
    }
  } else {
      content_html = parent_content_div.data('popover-content');
  }

  return content_html;
}

function get_tooltip_title(elem) {
  var ajaxdata;
  var id = elem.closest('div.content-provider-element').data('content-id');
  var $url = frontpage_urls.quick_info + '?id=' + id;
  var title = '';

  content = content_states[id];
  if (content === undefined) {
    jQuery.ajax({
      url: $url,
      async: false
    }).done(function(data) {
      content_states[id] = data;
      if (content_states[id].hasOwnProperty('title')) {
        title = content_states[id].title;
      }
    });
  } else {
      title = content.title;
  }

  return title;
}

function remove_element(element) {
  jQuery(element).each(function() {
    jQuery(this).fadeTo('slow', 0.01, function() { //fade
      jQuery(this).slideUp('slow', function() { //slide up
        jQuery(this).remove(); //then remove from the DOM
      });
     });
  });
}

function showMessage(message, type, time, id) {
  Messenger.options = {
    extraClasses: 'messenger-fixed messenger-on-bottom',
  };

  Messenger().post({
    message: message,
    type: type,
    hideAfter: time,
    showCloseButton: true,
    id: id
  });
}

/**
 * Initizlize Popovers
 */
function initializePopovers() {
  jQuery('div.placeholder div.content-provider-element .info').each(function() {
    var element = jQuery(this);

    jQuery(this).popover({
      placement: 'top',
      animation: false,
      title: get_tooltip_title(element),
      content: get_tooltip_content(element),
      html: true
    });
  });
}

jQuery(function($) {
  /*
   **************************************************************************
   * Sortable handlers
   **************************************************************************
   */
  makeContentProviderAndPlaceholdersSortable();

  /*
   **************************************************************************
   * Frontpage version control
   **************************************************************************
   */

  $('#modal-new-version').modal({
    backdrop: 'static',
    keyboard: true,
    show: false
  });
  $('#modal-new-version').on('click', 'a.btn.no', function(e) {
    e.preventDefault();
    $('#modal-new-version').modal('hide');
  });
  $('#modal-new-version').on('click', 'a.btn.yes', function(e) {
    e.preventDefault();
    location.reload();
  });

  /*
   **************************************************************************
   * Content elements in frontpage code
   **************************************************************************
   */
  $('div.placeholder').on('click', '.content-provider-element input[type="checkbox"]', function() {
    // Checkbox = $(this);
    var checked_elements = $('div.placeholder div.content-provider-element input[type="checkbox"]:checked').length;

    if (checked_elements > 0) {
      $('.old-button .batch-actions').fadeIn('fast');
    } else {
      $('.old-button .batch-actions').fadeOut('fast');
    }
  });
  $('div.content').on('mouseleave', 'div.placeholder div.content-provider-element', function() {
    $(this).find('.content-action-buttons').removeClass('open');
  });

  $('div.placeholder').on('mouseenter', 'div.content-provider-element .info', function() {
    $('div.placeholder div.content-provider-element .info').popover('show');
  });
  $('div.placeholder').on('mouseleave', 'div.content-provider-element .info', function() {
    $('div.placeholder div.content-provider-element .info').popover('hide');
  });

  initializePopovers();

  /*
   **************************************************************************
   * Dropdown menu content actions
   **************************************************************************
   */
  $('#modal-element-archive').modal({
    backdrop: 'static',
    keyboard: true,
    show: false
  });
  $('#frontpagemanager').on('click', 'div.placeholder div.content-provider-element a.arquive', function(e) {
    var element = $(this).closest('.content-provider-element');
    var elementID = element.data('content-id');

    $('body').data('element-for-archive', element);
    var modal = $('#modal-element-archive');

    modal.data('selected-for-archive', elementID);

    modal.find('.modal-body span.title').html('<strong>' + element.find('.title').html() + '</strong>');
    modal.modal('show');
    e.preventDefault();
  });

  $('#modal-element-archive').on('click', 'a.btn.yes', function(e) {
    var delId = $('#modal-element-archive').data('selected-for-archive');

    if (delId) {
      $.get(
        frontpage_urls.set_arquived,
        { ids: [ delId ] }
      ).done(function(data) {
        showMessage(data, 'success', 5, new Date().getTime());
        showMessage(frontpage_messages.remember_save_positions, 'info', 5, new Date().getTime());
      }).fail(function(data) {
        showMessage(data.responseText, 'error', 5, new Date().getTime());
      });
    }
    $('#modal-element-archive').modal('hide');
    remove_element($('body').data('element-for-archive'));
    e.preventDefault();
  });

  $('#modal-element-archive').on('click', 'a.btn.no', function(e) {
    $('#modal-element-archive').modal('hide');
    e.preventDefault();
  });

  // Drop element button
  $('#frontpagemanager').on('click', 'div.placeholder div.content-provider-element a.drop-element', function(e) {
    e.preventDefault();
    var parent = $(this).closest('.content-provider-element');

    remove_element(parent);
    showMessage(frontpage_messages.remember_save_positions, 'info', 5, new Date().getTime());
  });

  // Suggest-home
  $('#frontpagemanager').on('click', 'div.placeholder div.content-provider-element a.suggest-to-home', function(e) {
    var element = $(this).closest('.content-provider-element');
    var contentId = element.data('content-id');

    if (contentId) {
      $.get(
        frontpage_urls.toggle_suggested,
        { ids: [ contentId ] }
      ).done(function() {
        return null;
      }).fail(function() {
        return null;
      });
    }

    element.toggleClass('suggested');
    e.preventDefault();
  });

  // Send-to-trash
  $('#modal-element-send-trash').modal({
    backdrop: 'static',
    keyboard: true,
    show: false
  });

  $('#frontpagemanager').on('click', 'div.placeholder div.content-provider-element a.send-to-trash', function(e) {
    var element = $(this).closest('.content-provider-element');
    var elementID = element.data('content-id');

    $('body').data('element-for-del', element);
    $('#modal-element-send-trash').data('selected-for-del', elementID);

    $('#modal-element-send-trash .modal-body span.title').html('<strong>' + element.find('.title').html() + '</strong>');
    $('#modal-element-send-trash ').modal('show');
    e.preventDefault();
  });

  $('#modal-element-send-trash').on('click', 'a.btn.yes', function(e) {
    var delId = $('#modal-element-send-trash').data('selected-for-del');

    if (delId) {
      $.get(frontpage_urls.send_to_trash,
        { id: delId }
      );
    }
    showMessage(frontpage_messages.remember_save_positions, 'info', 5, new Date().getTime());
    $('#modal-element-send-trash').modal('hide');
    $('body').data('element-for-del').animate({ backgroundColor: '#fb6c6c' }, 300).animate({ opacity: 0, height: 0 }, 300, function() {
      $(this).remove();
    });
    e.preventDefault();
  });

  $('#modal-element-send-trash').on('click', 'a.btn.no', function(e) {
    $('#modal-element-send-trash').modal('hide');
    e.preventDefault();
  });

  /*
   **************************************************************************
   * Customized content
   **************************************************************************
   */
  $('#frontpagemanager').on('click', 'div.placeholder div.content-provider-element a.change-color', function(e) {
    var element   = $(this).closest('.content-provider-element');
    var elementID = element.data('content-id');

    var title = element.data('title');

    if (title.length > 0) {
      title = jQuery.parseJSON(title);
    }

    var modal = $('#modal-element-customize-content');

    modal.data('element-for-customize-content', element);

    modal.data('selected-for-customize-content', elementID);
    modal.find('.modal-header #content-title').html(element.find('.title').html());

    if (title['font-size'] !== 'undefined') {
      var size = title['font-size'].substring(0, 2);

      modal.find('.modal-body #font-size').val(size);
    } else {
      modal.find('.modal-body #font-size option[value=""]').attr('selected', 'selected');
    }
    if (title['font-family'] !== 'undefined') {
      modal.find('.modal-body #font-family').val(title['font-family']);
    } else {
      modal.find('.modal-body #font-family').val('Auto');
    }
    if (title['font-style'] !== 'undefined') {
      modal.find('.modal-body #font-style').val(title['font-style']);
    } else {
      modal.find('.modal-body #font-style').val('Normal');
    }
    if (title['font-weight'] !== 'undefined') {
      modal.find('.modal-body #font-weight').val(title['font-weight']);
    } else {
      modal.find('.modal-body #font-weight').val('Auto');
    }
    if (title.color !== 'undefined') {
      modal.find('.modal-body input#font-color').val(title.color);
      modal.find('.modal-body input#font-color').trigger('input');
    } else {
      modal.find('.modal-body input#font-color').val('#000000');
      modal.find('.modal-body input#font-color').trigger('input');
    }

    if (element.data('class') === 'Article' || element.data('class') === 'Opinion') {
      modal.find('.image-disposition').css('display', 'none');
    } else {
      modal.find('.image-disposition').css('display', 'none');
    }

    if (element.data('bg').length > 0) {
      var bgcolor = element.data('bg').substring(17, 24);

      modal.find('.modal-body input#bg-color').val(bgcolor);
      modal.find('.modal-body input#bg-color').trigger('input');
    } else {
      modal.find('.modal-body input#bg-color').val('#ffffff');
      modal.find('.modal-body input#bg-color').trigger('input');
    }
    modal.modal('show');
    e.preventDefault();
  });

  $('#modal-element-customize-content').on('click', 'a.btn.yes', function(e) {
    var elementID = $('#modal-element-customize-content').data('selected-for-customize-content');
    var element   = $('[data-content-id=' + elementID + ']');
    var url       = frontpage_urls.customize_content;

    var titleValues = {};

    var keys = [];
    var fontFamilyValue = $('#font-family').val();
    var fontSizeValue   = $('#font-size').val();
    var fontStyleValue  = $('#font-style').val();
    var fontWeightValue = $('#font-weight').val();
    var fontColorValue  = $('#font-color').val();

    if (fontFamilyValue.length > 0 && fontFamilyValue !== 'Auto') {
      titleValues['font-family'] = fontFamilyValue;
      keys[0] = 'font-family';
    }
    if (fontSizeValue.length > 0 && fontSizeValue !== 'Auto') {
      titleValues['font-size'] = fontSizeValue + 'px';
      keys[1] = 'font-size';
    }
    if (fontStyleValue.length > 0 && fontStyleValue !== 'Normal') {
      titleValues['font-style'] = fontStyleValue;
      keys[2] = 'font-style';
    }
    if (fontColorValue.length > 0 && fontColorValue !== 'Auto' && fontColorValue !== '#000000') {
      titleValues.color = fontColorValue;
      keys[3] = 'color';
    }

    if (fontWeightValue.length > 0 && fontWeightValue !== 'Auto') {
      titleValues['font-weight'] = fontWeightValue;
      keys[4] = 'font-weight';
    }

    var jsonTitle = JSON.stringify(titleValues, keys);
    var properties = {};
    var name = 'title_' + $('#frontpagemanager').data('category');

    if (!$.isEmptyObject(titleValues)) {
      properties[name] = jsonTitle;
    } else {
      properties[name] = '';
    }
    var bgcolor = $('#bg-color').val();
    var name2 = 'bgcolor_' + $('#frontpagemanager').data('category');

    if (bgcolor.length > 0 && bgcolor !== '#ffffff') {
      properties[name2] = bgcolor;
    } else {
      properties[name2] = '';
    }

    var format = $(".modal-body .radio input[type='radio']:checked").val();
    var vformat = 'format_' + $('#frontpagemanager').data('category');

    if (typeof format !== 'undefined' && (format.length > 0 && format !== 'auto')) {
      properties[vformat] = format;
    } else {
      properties[vformat] = '';
    }

    if (elementID) {
      $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {
          id: elementID,
          properties:   properties,
          content_type: element.data('class')
        }
      }).done(function() {
        element.css('color', '');
        element.css('font-weight', '');
        element.css('font-size', '');
        element.css('font-family', '');
        element.css('background-color', bgcolor);
        element.data('bg', 'background-color:' + bgcolor);
        element.data('format', format);
        element.data('title', jsonTitle);

        for (var key in titleValues) {
          if (key === 'color' || key === 'background-color') {
            element.css(key, titleValues[key]);
          }
        }
      }).fail(function() {
        // Data.message
      });
    }
    $('#modal-element-customize-content').modal('hide');

    e.preventDefault();
  });

  $('#modal-element-customize-content').on('click', 'a.btn.reset', function(e) {
    var modal = $('#modal-element-customize-content');

    modal.find('.modal-body #font-size option[value=""]').attr('selected', 'selected');
    modal.find('.modal-body #font-family').val('Auto');
    modal.find('.modal-body #font-style').val('Normal');
    modal.find('.modal-body #font-weight').val('Auto');
    modal.find('.modal-body .fontcolor span.simplecolorpicker').css('background-color', '#000000');
    modal.find('.modal-body #font-color').val('#000000');
    modal.find('.modal-body .background span.simplecolorpicker').css('background-color', '#ffffff');
    modal.find('.modal-body #bg-color').val('#ffffff');
    modal.find('.modal-body .radio input[value=""]').prop('checked', true);

    e.preventDefault();
  });

  $('#modal-element-customize-content').on('click', 'a.btn.no', function(e) {
    $('#modal-element-customize-content').modal('hide');
    e.preventDefault();
  });

  /*
   **************************************************************************
   * Content provider code
   **************************************************************************
   */
  $('#content-provider').dialog({
    minWidth: 800,
    autoOpen: false,
    maxHeight: 500
  });

  $('#content-provider .content-provider-block-wrapper').tabs({
    ajaxOptions: {
      error: function(xhr, status, index, anchor) {
        $(anchor.hash).html(
          '<div>' + frontpage_messages.error_tab_content_provider + '</div>');
      },
      complete: function() {
        $('#content-provider .spinner').hide();
      },
      beforeSend: function() {
        $('#content-provider .spinner').show();
      }
    },
    load: function() {
      makeContentProviderAndPlaceholdersSortable();
    },
    fx: { opacity: 'toggle', duration: 'fast' }
  });

  $('#content-provider').on('click', '.pagination a', function(e) {
    e.preventDefault();
    var parent = $(this).closest('.ui-tabs-panel');

    $.ajax({
      url: $(this).attr('href'),
      beforeSend: function() {
        $('#content-provider .spinner').show();
      }
    }).done(function(data) {
      parent.html(data);
      makeContentProviderAndPlaceholdersSortable();
    }).always(function() {
      $('#content-provider .spinner').hide();
    });
  });

  /*
   **************************************************************************
   * General buttons actions code
   **************************************************************************
   */
  $('#button_addnewcontents').on('click', function(e) {
    e.preventDefault();
    $('#content-provider').dialog('open');
  });
});
