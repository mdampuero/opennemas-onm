
//Mensajes alerta para contenedores de noticias.

getGalleryImages = function(action, category, metadatas, page) {
    if(metadatas == 0){
		action = 'listByCategory';
	}
    new Ajax.Updater('photos', "/admin/controllers/image/imageGallery.php?action="+action+"&page="+page+"&category="+category+"&metadatas="+metadatas,
    {
        method: 'get',
        evalScripts: true,
        onComplete: function() {
            var photos = $('photos').select('img');
            for(var i=0; i< photos.length; i++) {
                try {
                    new Draggable(photos[i].id, { revert:true, scroll: window, ghosting:true }  );
                } catch(e) {
                 //   console.debug( e );
                }
            }
        }
    } );
}
//unify function put div for update as param 
loadGalleryImages = function(action, category, metadatas, page, div) {
    if(metadatas == 0){
		action = 'listByCategory';
	}
    new Ajax.Updater(div, "/admin/controllers/image/imageGallery.php?action="+action+"&page="+page+"&category="+category+"&metadatas="+metadatas,
    {
        method: 'get',
        evalScripts: true,
        onComplete: function() {
            var photos = $('photos').select('img');
            for(var i=0; i< photos.length; i++) {
                try {
                    new Draggable(photos[i].id, { revert:true, scroll: window, ghosting:true }  );
                } catch(e) {
                 //   console.debug( e );
                }
            }
        }
    } );
}
onGalleryKeyEnter = function(e, category, metadatas, page, div)
{
    eKey = (document.all) ? e.keyCode : e.which;
    if (eKey==13)
    {
        loadGalleryImages('listByMetadatas', category, metadatas, page, div);
    }
}

onImageKeyEnter = function(e, category, metadatas, page)
{
    eKey = (document.all) ? e.keyCode : e.which;
    if (eKey==13)
    {
        getGalleryImages('listByMetadatas', category, metadatas, page);
    }
}


function getGalleryVideos(action, category, metadatas, page)
{
   if(metadatas == 0){
		action = 'listByCategory';
   }
   new Ajax.Updater('videos', "/admin/controllers/video/videoGallery.php?action="+action+"&page="+page+"&category="+category+"&metadatas="+metadatas,
        {
            evalScripts: true,
            onComplete: function() {
                var videos = $('videos').select('img');
                for(var i=0; i<videos.length; i++) {
                    try {
                        new Draggable(videos[i].id, { revert:true, scroll: window, ghosting:true }  );
                    } catch(e) {
                    //    console.debug( e );
                    }
                }
            }
        } );
}

function onVideoKeyEnter(e, category, metadatas, page)
{
    ekey = (document.all) ? e.keyCode : e.which;
    if (ekey==13)
    {
        getGalleryVideos('listByMetadatas',category, metadatas, page);
    }
}