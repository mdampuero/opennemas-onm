
function delete_videos(id,page){

      new Ajax.Request( 'video.php?action=delete&id='+id+'&page='+page,
        {
            onSuccess: function(transport) {
                 var msg = transport.responseText;
              //   showMsg({'warn':[msg ]},'growl');
                showMsgContainer({ 'warn':[ msg ] },'inline','messageBoard');
             /* if(confirm(msg)) {
                      var ruta='video.php?action=yesdel&id='+id+'&page='+page;
                      location.href= ruta;
                   }
                   return false; */
            } 
        });


 }


function loadVideoInformation(url){

      new Ajax.Updater('video-information', 'video.php?action=getVideoInformation&url='+encodeURIComponent(url),
        {
             onSuccess: function(transport) {
 
                 get_metadata($('title').value);
             }
        });


 }
