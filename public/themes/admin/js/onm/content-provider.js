/***************************************************************************
* Sortable handlers
***************************************************************************/

makeContentProviderAndPlaceholdersSortable = function() {

    // Make content providers draggable and define ghost element
    jQuery('div#content-provider ul#contentList li').draggable({
        connectToSortable: 'div.column-receiver ul.content-receiver',
        helper: 'clone',
        revert: 'true'
    }).disableSelection();

    // Make content providers sortable and allow to D&D over placeholders and content provider
    jQuery('div.column-receiver ul.content-receiver').sortable({
        connectWith: 'div#content-provider ul#contentList, div.column-receiver ul.content-receiver',
        dropOnEmpty: true,
        placeholder: 'placeholder-element',
        tolerance: 'pointer',
        items: 'li:not(.container-label)'
    }).disableSelection();

    jQuery('div#content-provider ul#contentList').sortable({
        connectWith: 'div#content-provider ul#contentList, div.column-receiver ul.content-receiver',
        dropOnEmpty: true,
        placeholder: 'placeholder-element',
        tolerance: 'pointer'
    }).disableSelection();

};
jQuery(document).ready(function($) {


    /***************************************************************************
    * Content provider code
    ***************************************************************************/

    $('#content-provider').tabs({
        load: function(event,ui) {
            makeContentProviderAndPlaceholdersSortable();
        }
    });

//tabs click
    $('#content-provider').on('click', '.content-provider-block-wrapper a', function(e, ui) {
        e.preventDefault();
        var href = $(this).attr('href');
        var parent = $(this).closest('.ui-tabs-panel');
       // var parent = $('#container-content-list');
        $.ajax({
            url: $(this).attr('href'),
            success: function(data) {
                parent.html(data);
                makeContentProviderAndPlaceholdersSortable();
            }
        });

    });
//change select category
    $('#content-provider').on('change', '#contentTypeCategories.selector', function(e, ui) {
        e.preventDefault();

        var category = $(this).val();
        var href = $(this).data('href') + '?category=' + category;
        var parent = $(this).closest('.ui-tabs-panel');

        $.ajax({
            url: href,
            success: function(data) {
                parent.html(data);
                makeContentProviderAndPlaceholdersSortable();
            }
        });
    });
//change page
    $('#content-provider').on('click', 'div.ui-tabs-panel div.contents div.pagination a', function(e, ui) {
        e.preventDefault();
        var href = $(this).attr('href');
        var parent = $(this).closest('.ui-tabs-panel');

         $.ajax({
            url: $(this).attr('href'),
            success: function(data) {
                parent.html(data);
                makeContentProviderAndPlaceholdersSortable();
            }
        });
    });

    jQuery('.column-receiver').on('click', 'span.icon .icon-trash', function() {

        item = jQuery(this).parent().parent();
        jQuery('div.column-receiver ul.content-receiver').find(item).remove();

    });


});
