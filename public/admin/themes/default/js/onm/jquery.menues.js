/*
* jquery functions
* Management menu: drag-drop items, delete item, add item.
*/
jQuery(document).ready(function($){
    makeSortable = function(){

        jQuery( 'ul.elementsContainer' ).sortable({
            connectWith: ".menuelements",
            placeholder: 'placeholder-element',
            tolerance: 'pointer'
        }).disableSelection();

        jQuery( '.menuelements' ).sortable({
            connectWith: 'ul.elementsContainer',
            placeholder: 'placeholder-element',
            tolerance: 'pointer'
        }).disableSelection();

    }();

    jQuery('#menuelements').on('click', '.delete-menu-item', function(e, ui){
        e.preventDefault();
        var element = $(this);
        element.closest('li.menuItem')
            .animate({ 'backgroundColor':'#fb6c6c' },300)
            .animate({ 'opacity': 0, 'height': 0 }, 300, function() {
                $(this).remove();
            });
    });

    jQuery('#menuelements').on('click', '.edit-menu-item', function(e, ui){
        e.preventDefault();
        var element = $(this).closest('li.menuItem');
        $('#modal-add-item #itemTitle').attr('value', element.attr('title'));
        $('#modal-add-item #itemID').attr('value', element.attr('pk_item'));
        $("#modal-add-item").modal('show');
        alert('not-implemented');
    });
});