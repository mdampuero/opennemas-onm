// jQuery FrontpageManager plugin
// A plugin for jQuery for managing ONM LayoutManager
// version 1.0, May 07th, 2011
// by Fran Diéguez

(function($) {

    $.fn.frontpageManager = function(method) {

        var methods = {

            init : function(options) {
                this.frontpageManager.settings = $.extend({}, this.frontpageManager.defaults, options);
                return this.each(function() {
                    var $element = $(this), // reference to the jQuery version of the current DOM element
                         element = this;      // reference to the actual DOM element

                    // console.log($.fn.frontpageManager.settings);
                    // code goes here
                    // console.log(this.frontpageManager.settings.foo);
                });

            },

            // a public method. for demonstration purposes only - remove it!
            foo_public_method: function() {
                // code goes here
            }

        };

        var helpers = {
            foo_private_method: function() {
                // code goes here
            }
        };

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error( 'Method "' +  method + '" does not exist in frontpageManager plugin!');
        }

    };

    $.fn.frontpageManager.defaults = {
        foo: 'bar'
    };

    $.fn.frontpageManager.settings = {};

})(jQuery);




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

        console.log(els);

        jQuery.post("frontpagemanager.php?action=save_positions&category=" + category,
                { 'contents_positions': els }
        ).success(function(data) {
            jQuery('#warnings-validation').html("<div class='success'>"+data+"</div>");
        }).error(function(data) {
            jQuery('#warnings-validation').html("<div class='error'>"+data.responseText+"</div>");
        });

        return false;
    });

    $(function() {
        $( "#dialog" ).dialog();
    });


    jQuery('.selectButton, .edit-button, .settings-button, .home-button, .delete-button').click(function (){
        alert('Not implemented yet.');
        return false;
    });

    $( "#content-provider").dialog({ position: 'bottom', minWidth: 600 });
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