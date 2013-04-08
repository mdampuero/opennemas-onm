/***************************************************************************
* Jquery functions and helpers for manage article
***************************************************************************/
function save_related_contents() {

    var els = get_related_contents('frontpage_related');
    jQuery('#relatedFrontpage').val(els);
    els = get_related_contents('inner_related');
    jQuery('#relatedInner').val(els);
    if (jQuery('#related-contents').find('#home_related')) {
        els = get_related_contents('home_related');
        jQuery('#relatedHome').val(els);

        els = get_gallery('gallery-Frontpage');
        jQuery('#withGallery').val(els);
        els = get_gallery('gallery-Inner');
        jQuery('#withGalleryInt').val(els);
        els = get_gallery('gallery-Home');
        jQuery('#withGalleryHome').val(els);
    }
}

function get_related_contents(container) {
    var els = [];

    jQuery('#' + container).find('ul.content-receiver li').each(function(index, item) {

        els.push({
            'id' : jQuery(item).data('id'),
            'content_type': jQuery(item).data('type'),
            'position': (index + 1)
        });
    });

    var encodedContents = JSON.stringify(els);

    return encodedContents;
}

function get_gallery(container) {

    var item = jQuery('#' + container).find('ul.content-receiver li:first');
    if (jQuery(item).data('type') == 'Album') {

        var id = jQuery(item).data('id');

        return id;
    }
    return null;
}

jQuery(function($) {

    $('#button_preview').on('click', function(e, ui) {
        e.preventDefault();

        CKEDITOR.instances.body.updateElement();
        CKEDITOR.instances.summary.updateElement();

        // // Fetch related news and others
        save_related_contents();

        var form = jQuery('#formulario');
        var contents = form.serializeArray();

        jQuery.ajax({
            type: 'POST',
            url: article_urls.preview,
            data: {
                'contents': contents
            },
            success: function(data) {
                $.colorbox({href: article_urls.get_preview, iframe : true, width: '95%', height: '95%'});
                $('#warnings-validation').html('');
            }
        });

        return false;
    });
});
