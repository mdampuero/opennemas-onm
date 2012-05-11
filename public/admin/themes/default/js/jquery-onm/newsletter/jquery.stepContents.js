
//STEPS
jQuery('#buttons').on('click','#next-button', function() {

    var contents = saveNewsletter();
    jQuery('#newsletterForm').submit();

});

jQuery('#buttons').on('click','#clean-button', function() {

    jQuery("div#newsletter-container").find('ul:not(:first)').remove();

    jQuery("div#newsletter-container").find('ul li:not(.container-label)').remove();

    jQuery.cookie("data-newsletter", '');
});

jQuery('#savedNewsletter').on('click','#load-saved', function() {

    jQuery.cookie("data-newsletter", '');
    jQuery("#action").val('loadSavedNewsletter');
    jQuery('#newsletterForm').submit();


});

saveNewsletter = (function() {

    var els = [];

    jQuery('div#newsletter-container').find('ul.content-receiver').each(function (index, cont) {

        var lis = [];
        jQuery(cont).find('li').each(function(i, item) {

            lis.push({
                'id' : jQuery(item).data('id'),
                'title': jQuery(item).data('title'),
                'content_type': jQuery(item).data('type'),
                'position': (index+1),
            });
        });


        els.push({
            'id' : jQuery(cont).data('id'),
            'title': jQuery(cont).find('li:first').data('title'),
            'content_type': 'container',
            'position': (index+1),
            'items': lis,
        });
    });

    var encodedContents = JSON.stringify(els);
    jQuery.cookie("data-newsletter", encodedContents);

    return encodedContents;

});


addSelectedItems  = (function () {

    jQuery('ul#contentList li').find('input:checked').each(function() {

        item =  jQuery(this).parent();
        jQuery('div#newsletter-container ul:last-child').append(item);

    });
    jQuery('input#toggleallcheckbox').prop("checked", false);

});

toggleProviderCheckbox  = (function (item) {
    var toggleElement = jQuery('input#toggleallcheckbox');

    if (toggleElement) {

        var toggle = toggleElement.prop("checked");
        if (jQuery(item).attr('id') != 'toggleallcheckbox') {
            toggle = !toggle;
            toggleElement.prop("checked",  toggle);
        }

        jQuery('ul#contentList li input[type=checkbox]').each(function() {
            jQuery(this).prop("checked", toggle);
        });
    }

});

//OPERATIONS
jQuery(function($){


    jQuery('div.newsletter-contents').on('click','#button-check-all', function(event) {

        toggleProviderCheckbox(event.target);

    });

    jQuery('div.newsletter-contents').on('click','#add-selected', function(event) {

        addSelectedItems();

    });

    jQuery('div.newsletter-contents').on('click','#button-add-container', function(event) {
        jQuery("#modal-add-label").modal('show');

    });

    jQuery("modal-add-label").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false,
    });

    jQuery('#modal-add-label a.btn.save').on('click', function(e) {

 //open modal

        var label = jQuery("#modal-add-label input#container_label").val();
        var id = jQuery("div.column-receiver ul:last-child").data('id') +1;

        jQuery("div.column-receiver").append( '<ul class="content-receiver" data-id="'+id+'" >'+
            '<li class="container-label" data-id="'+id+'" data-title="'+label+'" data-type="label">'+
            label+'</li></ul>');

        jQuery('div.column-receiver ul.content-receiver').sortable({
            connectWith: "div#content-provider ul#contentList, div.column-receiver ul.content-receiver",
            dropOnEmpty: true,
            placeholder: 'placeholder-element',
            tolerance: 'pointer',
            items: "li:not(.container-label)",
        }).disableSelection();

        jQuery("#modal-add-label input#container_label").val('');
        jQuery("#modal-add-label").modal('hide');

        e.preventDefault();
    });

});