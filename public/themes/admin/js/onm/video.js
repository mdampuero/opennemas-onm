function getVideoInformation(url) {
    var container = jQuery('#video-information');
    jQuery.ajax({
        url: video_manager_url.get_information + '?url=' + encodeURIComponent(url),
        async: true,
        beforeSend: function() {
            container.html('<div class="spinner"></div>Loading request...');
        },
        success: function(data) {
            container.html(data);
            fill_tags(
                jQuery('#title').val(),
                '#metadata',
                video_manager_url.fill_tags
            );
        }
    });
}

(function($) {
    jQuery('#video_url_button').on('click', function() {
        var url = jQuery('#video_url').val();
        getVideoInformation(url);
    });

    jQuery('#title').on('change', function() {
        fill_tags(
            jQuery('#title').val(),
            '#metadata',
            video_manager_url.fill_tags
        );
    });

    jQuery('#save-widget-positions').on('click', function(e) {
        e.preventDefault();

        var items_id = [];
        jQuery('tbody.sortable tr').each(function() {
            items_id.push(jQuery(this).data('id'));
        });

        jQuery.ajax(video_manager_urls.saveWidgetPositions, {
           type: 'POST',
           data: { positions: items_id }
        }).done(function(msg) {
            jQuery('#warnings-validation')
                .html('<div class=\"success\">' + msg + '</div>')
                .effect('highlight', {}, 3000);
       });
        return false;
    });
})(jQuery);
