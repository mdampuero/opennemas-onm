
//STEPS
jQuery('#buttons').on('click','#next-button', function() {

    var contents = saveNewsletter();
    jQuery('#newsletterForm').submit();


});

jQuery('#buttons').on('click','#clean-button', function() {

    jQuery("div#newsletter-container").find('ul.content-receiver li').remove();

});

jQuery('#savedNewsletter').on('click','#load-saved', function() {

    jQuery("#action").val('loadSavedNewsletter');
    jQuery('#newsletterForm').submit();


});


function saveNewsletter() {

    var els = [];

    jQuery('div#newsletter-container').find('ul.content-receiver li').each(function (index, item) {

        els.push({
            'id' : jQuery(item).data('id'),
            'title': jQuery(item).data('title'),
            'content_type': jQuery(item).data('type'),
            'position': (index+1),
        });
    });

    var encodedContents = JSON.stringify(els);
    jQuery.cookie("data-newsletter", encodedContents);

    return encodedContents;

}


//OPERATIONS
//jQuery('.newsletter-contents').on('click','a#button-add-text',function(event)) {
    //$("div.column-receiver ul.content-receiver").append(document.createTextNode("Hello"));

//});


