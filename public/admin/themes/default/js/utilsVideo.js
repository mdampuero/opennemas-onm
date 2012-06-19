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

    $('#video_url').on('change', function() {
        var url =  $(this).val();
        getVideoInformation(url);
    });

    $('#video_url').on('click', function(e, ui) {
        e.preventDefault();
        var url =  $('#video_url').val();
        getVideoInformation(url);
    });

})(jQuery);