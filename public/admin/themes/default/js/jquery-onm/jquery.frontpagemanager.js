jQuery(document).ready(function($){

    makeContentProviderAndPlaceholdersSortable = function () {
        // Make content providers sortable and allow to D&D over the placeholders
        jQuery('div#content-provider .ui-tabs-panel > div').sortable({
            connectWith: "div.placeholder div.content",
            placeholder: 'placeholder-element',
            update: function(event,ui) {
                jQuery('#warnings-validation').html('<div class="notice">{t}Please, remember save positions after finish.{/t}</div>');
            }
            //containment: '#content-with-ticker'
        }).disableSelection();

        // Make content providers sortable and allow to D&D over placeholders and content provider
        jQuery('div.placeholder div.content').sortable({
            connectWith: "div#content-provider .ui-tabs-panel > div, div.placeholder div.content",
            placeholder: 'placeholder-element',
            update: function(event,ui) {
                jQuery('#warnings-validation').html('<div class="notice">{t}Please, remember save positions after finish.{/t}</div>');
            }
            //containment: '#content-with-ticker'
        }).disableSelection();
    };

    makeContentProviderAndPlaceholdersSortable();

    // Toggle content-provider-element checkbox if all the content-provider-elemnt is clicked
    jQuery('div.placeholder div.content-provider-element').click(function() {
       checkbox = $(this).find('input[type="checkbox"]');
       checkbox.attr(
           'checked',
           !checkbox.is(':checked')
       );
    });


    // When get_ids button is clicked get all the contents inside any placeholder
    // and build some js objects with information about them
    jQuery('#get_ids').click(function() {

        var els = [];
        var category = jQuery("#frontpagemanager").data("category");
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

        jQuery.post("frontpagemanager.php?action=save_positions&category=" + category,
                { 'contents_positions': els }
        ).success(function(data) {
            jQuery('#warnings-validation').html("<div class='success'>"+data+"</div>");
        }).error(function(data) {
            jQuery('#warnings-validation').html("<div class='error'>"+data.responseText+"</div>");
        });

        return false;
    });

    jQuery('.selectButton, .settings-button, .home-button, .delete-button').click(function (){
        alert('Not implemented yet.');
        return false;
    });

    $( "#content-provider").dialog({ minWidth: 600, autoOpen: false, maxHeight: 500 });

    $('#button_addnewcontents').on('click', function() {
        $( "#content-provider").dialog('open');
    });

    $( "#content-provider .content-provider-block-wrapper").tabs({
        ajaxOptions: {
            error: function( xhr, status, index, anchor ) {
                $( anchor.hash ).html(
                    "<div>Couldn't load this tab. We'll try to fix this as soon as possible. " +
                    "If this wouldn't be a demo.</div>" );
            }
        },
        load: function(event,ui) {
            makeContentProviderAndPlaceholdersSortable();
        }
    }).disableSelection();

});