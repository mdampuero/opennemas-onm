
//Mensajes alerta para contenedores de noticias.

getGalleryImages = function(action, category, metadatas, page) {
    if (metadatas === 0){
		action = 'listByCategory';
	}
    new Ajax.Updater('photos', "/admin/controllers/image/imageGallery.php?action="+action+"&page="+page+"&category="+category+"&metadatas="+metadatas,
    {
        method: 'get',
        evalScripts: true
    } );
};

//unify function put div for update as param
loadGalleryImages = function(action, category, metadatas, page, div) {
    if (metadatas === 0){
		action = 'listByCategory';
	}
    new Ajax.Updater(
        div,
        "/admin/controllers/image/imageGallery.php?action="+action+"&page="+page+"&category="+category+"&metadatas="+metadatas,
        { method: 'get' }
    );
};

onGalleryKeyEnter = function(e, category, metadatas, page, div)
{
    eKey = (document.all) ? e.keyCode : e.which;
    if (eKey==13)
    {
        loadGalleryImages('listByMetadatas', category, metadatas, page, div);
    }
};


onImageKeyEnter = function(e, category, metadatas, page)
{
    eKey = (document.all) ? e.keyCode : e.which;
    if (eKey==13)
    {
        getGalleryImages('listByMetadatas', category, metadatas, page);
    }
};


function getGalleryVideos(action, category, metadatas, page, div)
{
    if (metadatas === 0) {
		action = 'listByCategory';
    }
    new Ajax.Updater(
        div,
        "/admin/controllers/video/videoGallery.php?action="+action+"&page="+page+"&category="+category+"&metadatas="+metadatas,
        { evalScripts: true }
    );
}

function onVideoKeyEnter(e, category, metadatas, page)
{
    ekey = (document.all) ? e.keyCode : e.which;
    if (ekey==13)
    {
        getGalleryVideos('listByMetadatas',category, metadatas, page);
    }
}