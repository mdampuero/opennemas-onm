(function() {
  jQuery('#save-widget-positions').on('click', function(e) {
    e.preventDefault();
    var itemsId = [];

    jQuery('tbody.sortable tr').each(function() {
      itemsId.push(jQuery(this).data('id'));
    });

    jQuery.ajax(video_manager_urls.saveWidgetPositions, {
      type: 'POST',
      data: { positions: itemsId }
    }).done(function(msg) {
      jQuery('#warnings-validation')
        .html('<div class="success">' + msg + '</div>')
        .effect('highlight', {}, 3000);
    });
    return false;
  });
})(jQuery);
