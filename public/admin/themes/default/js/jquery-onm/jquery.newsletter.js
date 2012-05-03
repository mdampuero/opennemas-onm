jQuery(document).ready(function($){
    $("#validate-button").on("click", function(event) {
        alert('updated');
        event.preventDefault();

        save_related_contents();
        alert('updated');
    });

    $("#save-button").on("click", function(event) {
        event.preventDefault();
        save_related_contents();
        alert('updated');
    }
});

function save_related_contents() {

    var els = get_related_contents('frontpage_related');
    jQuery('relatedFrontpage').val(els);

    els = get_related_contents('inner_related');
    jQuery('relatedInner').val(els);

    if($('home_related')) {
        els = get_related_contents('home_related');
        jQuery('relatedHome').val(els);
    }
}


function get_related_contents(container) {
    var els = [];

    jQuery(container).find('ul.content-receiver').each(function (index){
        els.push({
            'id' : jQuery(container).data('id'),
            'content_type': jQuery(container).data('type'),
            'position': index
        });
    });

    var encodedContents = JSON.stringify(els);
    return encodedContents;
}
