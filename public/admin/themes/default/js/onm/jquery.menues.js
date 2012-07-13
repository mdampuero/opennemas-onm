/*
* jquery functions
* Management menu: drag-drop items, delete item, add item.
*/
jQuery(document).ready(function($){

    jQuery('#menuelements').on('click', '.delete-menu-item', function(e, ui){
        e.preventDefault();
        var element = $(this);
        element.closest('li')
            .animate({ 'backgroundColor':'#fb6c6c' },300)
            .animate({ 'opacity': 0, 'height': 0 }, 300, function() {
                $(this).remove();
            });
    });

    jQuery('#elements-provider').on('click', '.add-item', function(e, ui){
        e.preventDefault();
        var element = $(this).closest('li');
        var clone = element.clone(true);
        jQuery('#menuelements').append(clone);

    });

    jQuery('#menuelements').on('click', '.edit-menu-item', function(e, ui){
        e.preventDefault();
        var element = $(this).closest('li.menuItem');
        $('#modal-add-item #itemTitle').attr('value', element.attr('title'));
        $('#modal-add-item #itemID').attr('value', element.attr('pk_item'));
        $("#modal-add-item").modal('show');
        alert('not-implemented');
    });

    nestable = $('ol#menuelements').nestedSortable({
        disableNesting: 'no-nest',
        forcePlaceholderSize: true,
        handle: 'div',
        helper: 'clone',
        items: 'li',
        maxLevels: 2,
        opacity: 0.6,
        placeholder: 'placeholder',
        revert: 250,
        tabSize: 25,
        tolerance: 'pointer',
        toleranceElement: '> div'
    });
});