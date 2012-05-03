/* jQuery("#formulario").on('click', '#save-button', function(event) {
     event.preventDefault();
    save_related_contents();

});

jQuery("#formulario").on('click', '#validate-button', function(event) {
    event.preventDefault();
    save_related_contents();

});
*/
function save_related_contents() {

    var els = get_related_contents('frontpage_related');
    jQuery('#relatedFrontpage').val(els);
    els = get_related_contents('inner_related');
    jQuery('#relatedInner').val(els);
    if($('#home_related')) {
        els = get_related_contents('home_related');
        jQuery('#relatedHome').val(els);
    }
}

function get_related_contents(container) {
    var els = [];

    jQuery('#'+container).find('ul.content-receiver li').each(function (index, item) {

        els.push({
            'id' : jQuery(item).data('id'),
            'content_type': jQuery(item).data('type'),
            'position': (index+1)
        });
    });

    var encodedContents = JSON.stringify(els);

    return encodedContents;
}
