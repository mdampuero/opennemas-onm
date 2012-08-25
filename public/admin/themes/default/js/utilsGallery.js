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

onImageKeyEnter = function(e, category, metadatas, page)
{
    eKey = (document.all) ? e.keyCode : e.which;
    if (eKey==13)
    {
        getGalleryImages('listByMetadatas', category, metadatas, page);
    }
};
