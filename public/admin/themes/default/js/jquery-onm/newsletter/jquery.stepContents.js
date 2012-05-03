
//STEPS
jQuery('#buttons').on('click','#next-button', function() {

    var contents = saveNewsletter();
     jQuery("textarea#newsletter").val(contents);


});


function saveNewsletter() {

    var els = [];

    jQuery('div#newsletter-container').find('ul.content-receiver li').each(function (index, item) {

        els.push({
            'id' : jQuery(item).data('id'),
            'content_type': jQuery(item).data('type'),
            'position': (index+1),
        });
    });

    var encodedContents = JSON.stringify(els);

    return encodedContents;

}


//OPERATIONS
//jQuery('.newsletter-contents').on('click','a#button-add-text',function(event)) {
    //$("div.column-receiver ul.content-receiver").append(document.createTextNode("Hello"));

//});


