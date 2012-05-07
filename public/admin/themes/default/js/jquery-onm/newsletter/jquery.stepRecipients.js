//STEPS
jQuery('#buttons').on('click','#next-button', function() {

    var contents = saveRecipients();
    jQuery('#newsletterForm').submit();

});


jQuery('#buttons').on('click','#prev-button', function() {

    jQuery("#action").val('preview');
    jQuery('#newsletterForm').submit();

});


jQuery('#buttons').on('click','#clean-button', function() {

    jQuery("div#recipients").find('ul#items-recipients li').remove();

});

jQuery(document).ready(function($){
    makeRecipientsListSortables();
});


makeRecipientsListSortables = function () {


    jQuery('div#dbList ul#items-dbList').sortable({
            connectWith: "ul#items-recipients",
            dropOnEmpty: true,
            placeholder: 'placeholder-element',
            tolerance: 'pointer',
        }).disableSelection();

    jQuery('div#mailList ul#items-mailList').sortable({
            connectWith: "ul#items-recipients",
            dropOnEmpty: true,
            placeholder: 'placeholder-element',
            tolerance: 'pointer',
        }).disableSelection();

     jQuery('div#recipients ul#items-recipients').sortable({
            connectWith: "div.mailList ul.items-mailList, div.dbList ul.items-dbList",
            dropOnEmpty: true,
            placeholder: 'placeholder-element',
            tolerance: 'pointer',
        }).disableSelection();

}

function saveRecipients() {

    var rec = [];

    jQuery('div#recipients').find('ul#items-recipients li').each(function (index, item) {

        rec.push({
            'name': jQuery(item).data('name'),
            'email': jQuery(item).data('email'),
        });
    });


    var othersMails = jQuery('div#manualList').find('textarea#othersMails').val();

    if (othersMails.length >0) {
        var list = othersMails.replace(',', '\n').split('\n');
        list.each(function (item) {
            rec.push({
                'name': item,
                'email':item,
            });
        });

    }

    var encodedContents = JSON.stringify(rec);
    jQuery.cookie("data-recipients", encodedContents);

    return encodedContents;

}


//OPERATIONS
//jQuery('.newsletter-contents').on('click','a#button-add-text',function(event)) {
    //$("div.column-receiver ul.content-receiver").append(document.createTextNode("Hello"));

//});


