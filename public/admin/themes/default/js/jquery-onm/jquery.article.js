/***************************************************************************
* Jquery functions and helpers for manage article
***************************************************************************/

jQuery(function($){

    $('a#button_preview').on('click', function(e, ui){
        e.preventDefault();
        // Check for id
        var id = $('input#id').val();
        console.log(id);
        // Save tiny content to textarea
        OpenNeMas.tinyMceFunctions.saveTiny('summary');
        OpenNeMas.tinyMceFunctions.saveTiny('body');
        // Fetch related news and others
        recolectar();
        if (id != null) {
            previewArticle(id,'formulario');
        } else {
            previewNonSavedArticle('formulario');
        }
        return false;
    });
});

/**
 * Preview of a saved article
 */
function previewArticle(id,formID){
    if(!validateForm(formID))
        return false;

    $(formID).id.value = id;

    $('formulario').action.value = '';
    jQuery.colorbox({
        href: '/controllers/preview_content.php?id='+id+'&action=article',
        title: 'Previsualización Articulo',
        iframe: true,
        width: '90%',
        height: '90%'
    });

    return true;
}

/**
 * Preview of a NON saved article
 */
function previewNonSavedArticle(formID){
    if(!validateForm(formID))
        return false;

    var form = jQuery('#'+formID);
    var contents = form.serializeArray();

    var requestHTML = jQuery.ajax({
        url: '/controllers/preview_content.php?action=article_new',
        type: "POST",
        data: contents,
        dataType: "html"
    });


    requestHTML.done(function(response){
        jQuery.colorbox({
            html: response,
            width: '90%',
            height: '90%'
        });
    })

    return true;
}
