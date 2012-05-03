jQuery(document).ready(function($){

});


function saveNewsletter() {

    var els = [];

    jQuery('div.newsletter-container').find('ul.content-receiver li').each(function (index, item) {
        console.log(item );
        els.push({
            'id' : jQuery(item).data('id'),
            'content_type': jQuery(item).data('type'),
            'position': (index+1)
        });
    });

    var encodedContents = JSON.stringify(els);

    return encodedContents;

}
