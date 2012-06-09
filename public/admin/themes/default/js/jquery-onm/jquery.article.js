/***************************************************************************
* Jquery functions and helpers for manage article
***************************************************************************/

jQuery(function($){

    $('a#button_preview').on('click', function(e, ui){
        e.preventDefault();

        // Save tiny content to textarea
        OpenNeMas.tinyMceFunctions.saveTiny('summary');
        OpenNeMas.tinyMceFunctions.saveTiny('body');

        // Fetch related news and others
        recolectar();
        save_related_contents();

        previewArticle('formulario');

        return false;
    });
});

/**
 * Preview of an article
 */
function previewArticle(formID){
    if(!validateForm(formID))
        return false;

    var form = jQuery('#'+formID);
    var contents = form.serializeArray();

    jQuery.ajax({
        type: 'POST',
        url: "/controllers/preview_content.php?action=article",
        data: {
            'contents': contents
        },
        success: function(data) {
            previewWindow = window.open('','_blank','');
            previewWindow.document.write(data);
            previewWindow.focus();
        }
    });

    return true;
}
