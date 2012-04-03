
function loadVideoInformation(url){

      new Ajax.Updater('video-information', 'video.php?action=getVideoInformation&url='+encodeURIComponent(url),
        {
             onSuccess: function(transport) {

                 get_metadata($('title').value);
             }
        });


 }
