//STEP 1 -Add Contents

//STEPS
jQuery('#buttons-contents').on('click','#next-button', function() {

    var contents = saveNewsletter();
    jQuery('#newsletterForm').submit();

});

jQuery('#buttons-contents').on('click','#clean-button', function() {

    jQuery("div#newsletter-container").find('ul li').remove();

    jQuery.cookie("data-newsletter", null);
});

jQuery('#savedNewsletter').on('click','#load-saved', function() {

    jQuery.cookie("data-newsletter", '');
    jQuery("#action").val('loadSavedNewsletter');
    jQuery('#newsletterForm').submit();


});

saveNewsletter = (function() {

    var els = new Array();

   jQuery('div#newsletter-container').find('div.container-receiver').each(function (index, cont) {
       var lis = new Array();
        jQuery(cont).find('li').each(function(i, item) {
            lis.push({
                'id' : jQuery(item).data('id'),
                'title': jQuery(item).data('title'),
                'content_type': jQuery(item).data('type'),
                'position': (i+1),
            });
        });

        els.push({
            'id' : jQuery(cont).data('id'),
            'title': jQuery(cont).data('title'),
            'content_type': 'container',
            'position': (index+1),
            'items': lis,
        });

    });

    var encodedContents = JSON.stringify(els);
    jQuery('textarea#newsletterContent').text(encodedContents);

    return encodedContents;

});

addSelectedItems  = (function () {

    if((jQuery('div#newsletter-container div.active').length == 0)) {
        jQuery("#modal-container-active").modal('show');
    } else {
        jQuery('ul#contentList li').find('input:checked').each(function() {
            if(this.checked == true) {
                jQuery(this).prop("checked", false);
                item =  jQuery(this).parent();
                item.draggable("disable");
                item.removeClass('ui-state-disabled');
                jQuery('div#newsletter-container div.active ul.content-receiver').append(item);
            }
        });

        jQuery('ul#contentList li').find('input:checked').prop("checked", false);
        jQuery('input#toggleallcheckbox').prop("checked", false);

    }
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
jQuery(function($) {

    jQuery('div.newsletter-contents').on('click', ' div.container-receiver .container-label', function(event) {
        jQuery(this).closest('div.container-receiver').addClass('active').siblings().removeClass('active')
    });

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
        var id = 1;
        jQuery("div.column-receiver div.container-receiver").each(function (index, item) {
            if( jQuery(item).data('id') > id) {
                id = jQuery(item).data('id');
            }

        });
        id = id + 1;
        jQuery("div.column-receiver").find('.container-receiver').removeClass('active');
        jQuery("div.column-receiver").append( '<div data-title="' + label + '" data-id="' + id +
                '" class="container-receiver active"><div class="container-label"><span>' +
                label +'</span> <div class="container-buttons btn-group">' +
                ' <i class="icon-chevron-down"></i><i class="icon-pencil"></i>' +
                ' <i class="icon-trash"></i> <i class="icon-clean"></i> </div> </div>' +
                ' <ul class="content-receiver"> </ul> </div>');


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

    jQuery('div#newsletter-container').sortable({
        axis: "y",
        handle: "span",
        tolerance: 'pointer',
    }).disableSelection();

    jQuery("modal-update-label").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false,
    });

    jQuery('#modal-update-label a.btn.save').on('click', function(e) {
    //open modal
        var label = jQuery("#modal-update-label input#updated_label").val();


        var id = jQuery('#modal-update-label input#updated_id').val();

        var container = jQuery("div.container-receiver[data-id="+id+"]");
        container.attr('data-title', label);
        jQuery(container).find('div.container-label span').html(label);

        jQuery("#modal-update-label").modal('hide');

        e.preventDefault();
    });

    /* Containers operations  */
    jQuery("div#newsletter-container").on('click','.container-label .icon-pencil', function(e) {
        var container = jQuery(this).closest('div.container-receiver');

        jQuery('#modal-update-label input#updated_label').val(container.attr('data-title'));
        jQuery('#modal-update-label input#updated_id').val(container.data('id'));

        jQuery("#modal-update-label").modal('show');
        e.preventDefault();

    });

    jQuery("div#newsletter-container").on('click','.container-label .icon-trash', function(e) {
        jQuery(this).closest('div.container-receiver').remove();

        e.preventDefault();
    });

    jQuery("div#newsletter-container").on('click','.container-label .icon-chevron-down', function(i, item) {
        var ul = jQuery(this).closest('div.container-receiver').find('ul.content-receiver');
        if(jQuery(ul).css('display') == 'none') {
          jQuery(ul).show().sortable('enable')
        } else {
          jQuery(ul).hide().sortable('disable');
        }


    });

    jQuery('div#newsletter-container').on('click','.container-label .icon-clean', function() {

        container =  jQuery(this).parent().parent().parent();
        jQuery(container).find('ul li').remove();

    });

    jQuery('div#newsletter-container').on('click','span.icon .icon-trash', function() {

        item =  jQuery(this).parent().parent();
        jQuery("div#newsletter-container ul.content-receiver").find(item).remove();

    });

});


/*****************************************************************************/

//SETP 2 - Preview
jQuery('#buttons-preview').on('click','#next-button', function() {
    saveChanges();
    jQuery('#newsletterForm').submit();
});

jQuery('#buttons-preview').on('click','#prev-button', function() {
    saveChanges();
    jQuery("#modal-newsletter-accept").modal('show');
});

jQuery('#modal-newsletter-accept a.btn.accept').on('click', function(e){
    jQuery("#modal-newsletter-accept").modal('hide');
    jQuery("#action").val('updateContents');
    jQuery('#newsletterForm').submit();
    e.preventDefault();
});

jQuery('#modal-newsletter-accept a.btn.no').on('click', function(e){
    jQuery("#modal-newsletter-accept").modal('hide');
    e.preventDefault();

});

jQuery('#newsletterForm').on('click','#edit-button', function() {

    tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
    jQuery('#newsletterForm #li-save-button').show();

});

jQuery('#newsletterForm').on('click','#save-button', function() {
    saveChanges();
});

function saveChanges() {

    //Save subject
    var subject = jQuery('div#content').find('input#subject').val();
    jQuery.cookie("data-subject", JSON.stringify(subject));

    //Save updates
    if(tinyMCE.get('htmlContent')) {
        OpenNeMas.tinyMceFunctions.saveTiny( 'htmlContent' )
        OpenNeMas.tinyMceFunctions.destroy( 'htmlContent' );
    }
    var htmlContent = jQuery('div#content').find('div#htmlContent').html();

    setTimeout(function () {

        jQuery.ajax({
            url:  "/admin/controllers/newsletter/newsletter.php",
            type: "POST",
            data: { action:"saveNewsletterContent", html:htmlContent },
            error:function (xhr, ajaxOptions, thrownError){
                log(xhr.status + 'problem saving html code ');

            },
            success: function() {
                log('ok');
            }
        });
    }, 3000);

}

/*****************************************************************************/

//SETP 3 - ADD recipients

jQuery('div#dbList').on('click','#add-selected', function () {

    jQuery('ul#contentList li').find('input:checked').each(function() {

        item =  jQuery(this).parent();
        jQuery('div#recipients ul#items-recipients').append(item);

    });
    jQuery('input#toggleallcheckbox').prop("checked", false);


});


jQuery('div#dbList').on('click','#button-check-all', function(event) {

    toggleProviderCheckbox(event.target);

});

jQuery('#buttons-recipients').on('click','#next-button', function() {

    var contents = saveRecipients();
    jQuery('#newsletterForm').submit();

});


jQuery('#buttons-recipients').on('click','#prev-button', function() {

    jQuery("#action").val('preview');
    jQuery('#newsletterForm').submit();

});


jQuery('#buttons-recipients').on('click','#clean-button', function() {

    jQuery.cookie("data-recipients", null);
    jQuery("#action").val('listRecipients');
    jQuery('#newsletterForm').submit();

});

jQuery(document).ready(function($){
    makeRecipientsListSortables();
});


makeRecipientsListSortables = function () {


    jQuery('div#dbList ul#contentList').sortable({
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
            connectWith: "ul#contentList, ul#items-mailList",
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


/*****************************************************************************/

//SETP 4 - SEND

jQuery('#buttons-send').on('click','#prev-button', function() {

    jQuery("#action").val('updateContents');
    jQuery('#newsletterForm').submit();

});
