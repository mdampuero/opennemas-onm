function getVideoInformation(url) {
    jQuery.ajax({
        url: video_manager_url.get_information + '?url=' +encodeURIComponent(url),
        success: function(data) {
            jQuery('#video-information').html(data);
            get_metadata(jQuery('#title').val());
        }
    });
}

(function($) {

    jQuery('#video_url').on('change', function() {
        var url =  $(this).val();
        getVideoInformation(url);
    });

    jQuery('#video_url').on('click', function(e, ui) {
        e.preventDefault();
        var url =  $('#video_url').val();
        getVideoInformation(url);
    });

    jQuery('#save-widget-positions').on('click', function(e, ui){
        e.preventDefault();

        var items_id = [];
        jQuery( "tbody.sortable tr" ).each(function(){
            items_id.push(jQuery(this).data("id"));
        });

        jQuery.ajax(video_manager_urls.saveWidgetPositions, {
           type: "POST",
           data: { positions : items_id }
        }).done(function( msg ){

               jQuery('#warnings-validation').html("<div class=\"success\">"+msg+"</div>")
                                             .effect("highlight", {}, 3000);

       });
        return false;
    });

})(jQuery);