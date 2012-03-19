makeContentProviderAndPlaceholdersSortable = function () {
    // Make content providers sortable and allow to D&D over the placeholders
    jQuery('div#content-provider .ui-tabs-panel > div').sortable({
        connectWith: "div.placeholder div.content",
        placeholder: 'placeholder-element',
        update: function(event,ui) {
            jQuery('#warnings-validation').html('<div class="notice">'+frontpage_messages.remember_save_positions+'</div>');
        }
        //containment: '#content-with-ticker'
    }).disableSelection();

    // Make content providers sortable and allow to D&D over placeholders and content provider
    jQuery('div.placeholder div.content').sortable({
        connectWith: "div#content-provider .ui-tabs-panel > div, div.placeholder div.content",
        placeholder: 'placeholder-element',
        update: function(event,ui) {
            jQuery('#warnings-validation').html('<div class="notice">'+frontpage_messages.remember_save_positions+'</div>');
        }
        //containment: '#content-with-ticker'
    }).disableSelection();
};
jQuery(document).ready(function($){

    /***************************************************************************
    * Sortable handlers
    ***************************************************************************/
    makeContentProviderAndPlaceholdersSortable();

    /***************************************************************************
    * Content elements in frontpage code
    ***************************************************************************/
    // Toggle content-provider-element checkbox if all the content-provider-elemnt is clicked
    $('div.placeholder').on('click', 'div.content-provider-element .description', function() {
       checkbox = $(this).find('input[type="checkbox"]');
       checkbox.attr(
           'checked',
           !checkbox.is(':checked')
       );
    });
    $('div.placeholder').on('mouseleave', 'div.content-provider-element', function(e) {
        $(this).find('.content-action-buttons').removeClass('open');
    });


    $('div.placeholder').on('click', 'div.content-provider-element a.drop-element', function(e) {
        e.preventDefault();
        var parent = $(this).closest('.content-provider-element');
        parent.animate({'backgroundColor':'#fb6c6c'},300).animate({'opacity': 0, 'height': 0 }, 300, function() {
            parent.remove();
        });
        jQuery('#warnings-validation').html('<div class="notice">'+frontpage_messages.remember_save_positions+'</div>');
    });

    // suggest-home

    // arquive

    // send-to-trash


    /***************************************************************************
    * Content provider code
    ***************************************************************************/

    $( "#content-provider").dialog({ minWidth: 600, autoOpen: false, maxHeight: 500 });

    $( "#content-provider .content-provider-block-wrapper").tabs({
        ajaxOptions: {
            error: function( xhr, status, index, anchor ) {
                $( anchor.hash ).html(
                    "<div>"+frontpage_messages.error_tab_content_provider+"</div>" );
            },
            complete: function() {
                $('#content-provider .spinner').hide();
            },
            beforeSend: function(){
                $('#content-provider .spinner').show();

           }
        },
        load: function(event,ui) {
            makeContentProviderAndPlaceholdersSortable();
        },
        fx: { opacity: 'toggle', duration: 'fast' }
    });

    $( "#content-provider").on('click', '.pagination a', function(e, ui){
        e.preventDefault();
        var href   = $(this).attr('href');
        var parent = $(this).closest('.ui-tabs-panel');
        $.ajax({
            url: $(this).attr('href'),
            success: function(data){
                parent.html(data);
                makeContentProviderAndPlaceholdersSortable();
            },
            complete: function() {
                $('#content-provider .spinner').hide();
            },
            beforeSend: function(){
                $('#content-provider .spinner').show();

           }
        });
    });


    /***************************************************************************
    * General buttons actions code
    ***************************************************************************/

    $('#button_addnewcontents').on('click', function(e, ui) {
        $( "#content-provider").dialog('open');
        e.preventDefault();
    });

    $('#button_savepositions').on('click',function() {
        var els = get_contents_in_frontpage();
        var category = jQuery("#frontpagemanager").data("category");

        jQuery.post("frontpagemanager.php?action=save_positions&category=" + category,
                { 'contents_positions': els }
        ).success(function(data) {
            jQuery('#warnings-validation').html("<div class='success'>"+data+"</div>");
        }).error(function(data) {
            jQuery('#warnings-validation').html("<div class='error'>"+data.responseText+"</div>");
        });

        return false;
    });

    $('#button_clearcache').on('click', function(e, ui) {
        e.preventDefault();
        var category = $(this).data('category');
        $.ajax({
            url: "/admin/controllers/tpl_manager/refresh_caches.php?category=" + encodeURIComponent(category),
            success: function(data){
                $('#warnings-validation').html(data);
            }
        });
    });

    $('#button_previewfrontpage').on('click', function (e, ui){
        e.preventDefault();
        var contents = get_contents_in_frontpage();
        var encodedContents = JSON.stringify(get_contents_in_frontpage());
        jQuery.colorbox({
            href: "/admin/controllers/frontpagemanager/frontpagemanager.php?action=preview_frontpage&contents="+encodedContents,
            data: { 'contents': contents },
            title: 'Previsualización Portada',
            iframe: true,
            width: '90%',
            height: '90%'
        });
    });

    $('#button_moreactions').on('click', function (e, ui){
        e.preventDefault();
        alert('not implemented');
    });

});


function get_contents_in_frontpage() {
    var els = [];

    jQuery('div.placeholder').each(function (){
        var placeholder = jQuery(this).data('placeholder');
        jQuery(this).find('div.content-provider-element').each(function (index){
            els.push({
                'id' : jQuery(this).data('content-id'),
                'content_type': jQuery(this).data('class'),
                'placeholder': placeholder,
                'position': index,
                'params': {}
            });
        });

    });
    return els;
}