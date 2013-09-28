/*
* jquery functions
* Management menu: drag-drop items, delete item, add item.
*/
jQuery(document).ready(function($) {

    jQuery('#elements-provider').accordion({
        autoHeight: false,
        navigation: true
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
        toleranceElement: '> div',
        stop: function(e, ui) {
            jQuery('#warnings-validation').html(
                '<div class="alert alert-notice"><button class="close" data-dismiss="alert">×</button>'
                    + menu_messages.remember_save +
                '</div>'
            );
        }
    });

    jQuery('#menuelements').on('click', '.delete-menu-item', function(e, ui) {
        e.preventDefault();
        var element = $(this);
        element.closest('li')
            .animate({ 'backgroundColor': '#fb6c6c' },300)
            .animate({ 'opacity': 0, 'height': 0 }, 300, function() {
                $(this).remove();
            });

        jQuery('#warnings-validation').html(
            '<div class="alert alert-notice"><button class="close" data-dismiss="alert">×</button>'
                + menu_messages.remember_save +
            '</div>'
        );
    });

    jQuery('#elements-provider').on('click', '.add-item', function(e, ui) {
        e.preventDefault();
        var element = $(this).closest('li');
        var name = element.attr('data-title');
        var clone = element.clone(true);

        jQuery(
            '<div class="form-horizontal edit-menu-form">' +
                '<fieldset>' +
                    '<div class="control-group">' +
                        '<label class="control-label">Title</label>' +
                        '<div class="controls">' +
                            '<input type="text" class="title" value="' + name + '">' +
                        '</div>' +
                    '</div>' +
                    '<div class="send-button-wrapper">' +
                        '<button type="submit" class="btn save-menuitem-button">Update</button>' +
                    '</div>' +
                '</fieldset>' +
            '</div>'
        ).insertAfter(clone.find('.btn-group'));

        clone.closest('li').addClass("menuItem");

        jQuery('#menuelements').append(clone);

        jQuery('#warnings-validation').html(
            '<div class="alert alert-notice"><button class="close" data-dismiss="alert">×</button>'
                + menu_messages.remember_save +
            '</div>'
        );
    });

    jQuery('#menuelements').on('click', '.edit-menu-item', function(e, ui) {
        e.preventDefault();
        var element = $(this).closest('li.menuItem');
        element.find('> div >.edit-menu-form').slideToggle('fast');
    });

    jQuery('#menuelements').on('click', '.save-menuitem-button', function(e, ui) {
        e.preventDefault();
        var form = $(this).closest('.edit-menu-form');
        var menuItem = $(this).closest('li.menuItem');

        var title = form.find('.title').val();
        menuItem.data('title', title);
        menuItem.find('> div > .menu-title').html(title);

        var link = form.find('.link');
        if (link) {
            var url = link.val();
            menuItem.data('link', url);
        }

        jQuery('#warnings-validation').html(
            '<div class="alert alert-notice"><button class="close" data-dismiss="alert">×</button>'
                + menu_messages.remember_save +
            '</div>'
        );
    });



    jQuery('#formulario').on('submit', function(e, ui) {
        var items = [];
        var menu_elements = jQuery('#menuelements li');
        var items_hierarchy = jQuery('#menuelements').nestedSortable('toArray');

        menu_elements.each(function(pos, item) {
            if ($(item).attr('id')) {
                var parent_id = 0;
                $.each(items_hierarchy, function(index, item_hierarchy) {
                    if ($(item).data('item-id') == item_hierarchy['item_id']) {
                        parent_id = parseInt(item_hierarchy['parent_id']);
                    }
                });
                var newitem = {
                    'id': $(item).data('item-id'),
                    'title': $(item).data('title'),
                    'type': $(item).data('type'),
                    'link': $(item).data('link'),
                    'parent_id': parent_id
                };
                items.push(newitem);
            }
        });
        jQuery('#items').attr('value', JSON.stringify(items));
    });

    jQuery('#add-external-link').on('click', function(e, ui) {
        e.preventDefault();
        var name = jQuery('#external-link-title').val();
        var link = jQuery('#external-link-link').val();
        if (name && link) {
            jQuery('#menuelements').append(
                '<li data-title="' + name + '" data-link="' + link +
                    '" class="menuItem" data-name="' + name + '" id ="' + name +
                    '" data-item-id="" data-type="external">' +
                    '<div>' +
                        '<span class="menu-title">' +
                        name +
                        '</span>' +
                        '<div class="btn-group actions" style="float:right;">' +
                            '<a href="#" class="edit-menu-item"><i class="icon-pencil"></i></a> ' +
                            '<a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>' +
                        '</div>' +
                        '<div class="form-horizontal edit-menu-form">' +
                            '<fieldset>' +
                            '<div class="control-group">' +
                                    '<label class="control-label">Title</label>' +
                                    '<div class="controls">' +
                                        '<input type="text" class="title" value="' + name + '">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="control-group">' +
                                    '<label class="control-label">Link</label>' +
                                    '<div class="controls">' +
                                        '<input type="text"class="link" value="' + link + '">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="send-button-wrapper">' +
                                    '<button type="submit" class="btn save-menuitem-button">Update</button>' +
                                '</div>' +
                            '</fieldset>' +
                        '</div>' +
                    '</div>' +
                '</li>');

            jQuery('#external-link-title').attr('value', '');
            jQuery('#external-link-link').attr('value', '');

            jQuery('#warnings-validation').html(
                '<div class="alert alert-notice"><button class="close" data-dismiss="alert">×</button>'
                    + menu_messages.remember_save +
                '</div>'
            );
        }
    });
});
