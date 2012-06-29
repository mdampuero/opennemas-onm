/*
* jquery functions
* Get specials contents for save in database.
*/
saveSpecialContent = function() {

    var els = [];
    jQuery('#column_right').find('ul.content-receiver li').each(function (index, item) {

        els.push({
            'id' : jQuery(item).data('id'),
            'content_type': jQuery(item).data('type'),
            'position': index
        });
    });

    jQuery('input#noticias_right').val(JSON.stringify(els));

    els = [];

    jQuery('#column_left').find('ul.content-receiver li').each(function (index, item) {

        els.push({
            'id' : jQuery(item).data('id'),
            'content_type': jQuery(item).data('type'),
            'position': index
        });
    });

    jQuery('input#noticias_left').val(JSON.stringify(els));

}
