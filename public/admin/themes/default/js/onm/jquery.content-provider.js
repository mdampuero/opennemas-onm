jQuery(document).ready(function($){


    /***************************************************************************
    * Content provider code
    ***************************************************************************/

    $( "#content-provider").tabs({
        load: function(event,ui) {
            makeContentProviderAndReceiverSortable();
        }
    });

//tabs click
    $( "#content-provider").on('click', '.content-provider-block-wrapper a', function(e, ui){
        e.preventDefault();
        var href   = $(this).attr('href');
        var parent = $(this).closest('.ui-tabs-panel');
       // var parent = $('#container-content-list');
        $.ajax({
            url: $(this).attr('href'),
            success: function(data){
                parent.html(data);
                makeContentProviderAndReceiverSortable();
            }
        });

    });
//change select category
    $( "#content-provider").on('change', '#contentTypeCategories', function(e, ui){
        e.preventDefault();

        var category = $(this).val();
        var href   = $(this).data('href')+'&category='+category;
        var parent = $(this).closest('.ui-tabs-panel');
        $.ajax({
            url: href,
            success: function(data){
                parent.html(data);
                makeContentProviderAndReceiverSortable();
            }
        });
    });
//change page
    $( "#content-provider").on('click', 'div.ui-tabs-panel div.contents div.pagination a', function(e, ui){
        e.preventDefault();
        var href   = $(this).attr('href');
        var parent = $(this).closest('.ui-tabs-panel');

         $.ajax({
            url: $(this).attr('href'),
            success: function(data){
                parent.html(data);
                makeContentProviderAndReceiverSortable();
            }
        });
    });


    /***************************************************************************
    * Sortable handlers
    ***************************************************************************/

    makeContentProviderAndReceiverSortable = function () {
        // Make content providers sortable and allow to D&D over the placeholders
        var before ='';
        jQuery('div#content-provider ul#contentList').sortable({
            connectWith: "div.column-receiver ul.content-receiver",
            dropOnEmpty: true,
            placeholder: 'placeholder-element',
            tolerance: 'pointer',
            forcePlaceholderSize: true,
            start:function(event,ui){
                $(ui.item).show();
                clone = $(ui.item).clone();
                clone.removeAttr( 'style' );
                before = $(ui.item).prev();
                parent = $(ui.item).parent();

            },
            stop:function(event, ui){
                if(before.length>0) {
                    before.after(clone);
                } else {
                    parent.prepend(clone);
                  //  this
                }
            }
        }).disableSelection();

        // Make content providers sortable and allow to D&D over placeholders and content provider
        jQuery('div.column-receiver ul.content-receiver').sortable({
            connectWith: "div#content-provider ul#contentList, div.column-receiver ul.content-receiver",
            dropOnEmpty: true,
            placeholder: 'placeholder-element',
            tolerance: 'pointer',
        }).disableSelection();

    };


});