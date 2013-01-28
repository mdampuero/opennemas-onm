function makeContentProviderAndPlaceholdersSortable() {
    // Make content providers sortable and allow to D&D over the placeholders
    jQuery('div#content-provider .ui-tabs-panel > div:not(.pagination)').sortable({
        connectWith: 'div.placeholder div.content',
        placeholder: 'placeholder-element',
        handle: '.description',
        update: function(event,ui) {
            initializePopovers();
            show_save_frontpage_dialog();
            frontpage_info.changed=true;
        },
        tolerance: 'pointer'
        //containment: '#content-with-ticker'
    }).disableSelection();

    // Make content providers sortable and allow to D&D over placeholders and content provider
    jQuery('div.placeholder div.content').sortable({
        connectWith: 'div#content-provider .ui-tabs-panel > div:not(.pagination), div.placeholder div.content',
        placeholder: 'placeholder-element',
        handle: '.description',
        update: function(event,ui) {
            initializePopovers();
            show_save_frontpage_dialog();
            frontpage_info.changed=true;
        },
        tolerance: 'pointer'
        //containment: '#content-with-ticker'
    }).disableSelection();
}

function check_available_new_version() {
    var $version = frontpage_info.last_saved;
    var category = $('#frontpagemanager').data('category');
    var exists_version = true;
    $.ajax({
        url: frontpage_urls.check_version + '?date=' + encodeURIComponent($version) + '&category=' + category,
        method: 'get',
        async: false,
        type: 'json'
    }).done(function(data) {
        exists_version = (data == 'true');
    });
    return exists_version;
}



function get_tooltip_content(elem) {
    var parent_content_div = elem.closest('div.content-provider-element');
    var content_html = '';

    if (parent_content_div.data('popover-content') === undefined) {
        var id = parent_content_div.data('content-id');
        var $url = frontpage_urls.quick_info + '?id=' + id;
        var content = '';

        content = content_states[id];
        if (content === undefined) {
            jQuery.ajax({
                url: $url,
                async: false
            }).done(function(data) {
                content_states[id] = data;
            });
        } else {
            content_html = 'Estado: '+ content.state +
                '<br>Vistas: '+ content.views +
                '<br>Categoría: '+ content.category +
                "<br>Programación: <span class='scheduled-state " + content.scheduled_state + "'>" + content.scheduled_state + '</span>'+
                '<br>Hora inicio: '+ content.starttime +
                '<br>Último autor: '+ content.last_author;
            parent_content_div.data('popover-content', content_html);
        }
    } else {
        content_html = parent_content_div.data('popover-content');
    }

    return content_html;
}

function get_tooltip_title(elem) {
    var ajaxdata;
    var id = elem.closest('div.content-provider-element').data('content-id');
    var $url = frontpage_urls.quick_info + '?id=' + id;

    content = content_states[id];
    if (content === undefined) {
        jQuery.ajax({
            url: $url,
            async: false
        }).done(function(data) {
            content_states[id] = data;
            if (content_states.hasOwnProperty('id') && content_states[id].hasOwnProperty('title')) {
                return content_states[id].title;
            } else {
                return null;
            }
        });
        return null;
    } else {
        return content.title;
    }
}

function remove_element(element) {
    jQuery(element).each(function() {
        jQuery(this).fadeTo('slow', 0.01, function() { //fade
            jQuery(this).slideUp('slow', function() { //slide up
                jQuery(this).remove(); //then remove from the DOM
            });
         });
    });
}

function get_contents_in_frontpage() {
    var els = [];

    jQuery('div.placeholder').each(function() {
        var placeholder = jQuery(this).data('placeholder');
        jQuery(this).find('div.content-provider-element').each(function(index) {
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

function show_save_frontpage_dialog() {
    jQuery('#warnings-validation').html('<div class="alert alert-notice"><button class="close" data-dismiss="alert">×</button>' + frontpage_messages.remember_save_positions + '</div>');
}


function initializePopovers() {
    jQuery('div.placeholder div.content-provider-element .info').each(function() {
        var element = jQuery(this);

        jQuery(this).popover({
            placement: 'left',
            // trigger: 'manual',
            animation: false,
            delay: 0,
            title: get_tooltip_title(element),
            content: get_tooltip_content(element)
        });
    });
}
jQuery(function($) {

    window.setInterval(function(){
        // Frontpage has changed and needs to be reloaded
        if (check_available_new_version()) {
            $('#modal-new-version').modal('show');
        }
    }, 10000);
    /***************************************************************************
    * Sortable handlers
    ***************************************************************************/
    makeContentProviderAndPlaceholdersSortable();

    /***************************************************************************
    * Frontpage version control
    ***************************************************************************/
    $('#modal-new-version').modal({ backdrop: 'static', keyboard: true, show: false });
    $('#modal-new-version').on('click', 'a.btn.no', function(e,ui) {
        e.preventDefault();
        $('#modal-new-version').modal('hide');
    });
    /***************************************************************************
    * Batch Actions
    ***************************************************************************/
    $('#modal-batch-delete').modal({ backdrop: 'static', keyboard: true, show: false });
    $('#modal-batch-delete').on('click', 'a.btn.no', function(e,ui) {
        e.preventDefault();
        $('#modal-batch-delete').modal('hide');
    });
    $('#modal-batch-delete').on('click', 'a.btn.yes', function(e, ui) {
        e.preventDefault();
        var contents = $('#frontpagemanager .content-provider-element input[type="checkbox"]:checked').closest('.content-provider-element');
        show_save_frontpage_dialog();
        $('#modal-batch-delete').modal('hide');
        remove_element(contents);
        e.preventDefault();
    });

    $('#modal-batch-arquive').modal({ backdrop: 'static', keyboard: true, show: false });
    $('#modal-batch-arquive').on('click', 'a.btn.no', function(e,ui) {
        e.preventDefault();
        $('#modal-batch-arquive').modal('hide');
    });
    $('#modal-batch-arquive').on('click', 'a.btn.yes', function(e, ui) {
        e.preventDefault();
        var contents = $('#frontpagemanager .content-provider-element input[type="checkbox"]:checked').closest('.content-provider-element');
        var ids = [];

        contents.each(function() {
            log($(this).closest('.content-provider-element').data('content-id'));
            ids.push($(this).closest('.content-provider-element').data('content-id'));
        });
        $.get(
            frontpage_urls.set_arquived,
            { 'ids': ids }
        ).success(function(data) {
            $('#warnings-validation').html("<div class='success'>" + data + '</div>');
        }).error(function(data) {
            $('#warnings-validation').html("<div class='error'>" + data.responseText + '</div>');
        });
        $('#modal-batch-arquive').modal('hide');
        remove_element(contents);
        e.preventDefault();
    });


    /***************************************************************************
    * Content elements in frontpage code
    ***************************************************************************/
    // $('div.placeholder').on('click', '.content-provider-element input[type="checkbox"]', function() {
    //     log('hola');
    // });
    // Toggle content-provider-element checkbox if all the content-provider-elemnt is clicked
    $('div.placeholder').on('click', '.content-provider-element input[type="checkbox"]', function() {
        checkbox = $(this);
        var checked_elements = $('div.placeholder div.content-provider-element input[type="checkbox"]:checked').length;
        if (checked_elements > 0) {
            $('.old-button .batch-actions').fadeIn('fast');
        } else {
            $('.old-button .batch-actions').fadeOut('fast');
        }
    });
    $('div.content').on('mouseleave', 'div.placeholder div.content-provider-element', function(e) {
        $(this).find('.content-action-buttons').removeClass('open');
    });


    $('div.placeholder').on('mouseenter', 'div.content-provider-element .info', function(e, ui) {
        $('div.placeholder div.content-provider-element .info').popover('show');
    });
    $('div.placeholder').on('mouseleave', 'div.content-provider-element .info', function(e, ui) {
        $('div.placeholder div.content-provider-element .info').popover('hide');
    });

    initializePopovers();

    /***************************************************************************
    * Dropdown menu content actions
    ***************************************************************************/
    // arquive
    $('#modal-element-archive').modal({ backdrop: 'static', keyboard: true, show: false });
    $('#frontpagemanager').on('click', 'div.placeholder div.content-provider-element a.arquive', function(e) {
        var element = $(this).closest('.content-provider-element');
        var elementID = element.data('content-id');
        $('body').data('element-for-archive', element);
        var modal = $('#modal-element-archive');
        modal.data('selected-for-archive', elementID);

        modal.find('.modal-body span.title').html('<strong>' + element.find('.title').html() + '</strong>');
        modal.modal('show');
        e.preventDefault();
    });

    $('#modal-element-archive').on('click', 'a.btn.yes', function(e, ui) {
        var delId = $('#modal-element-archive').data('selected-for-archive');
        if (delId) {
            $.get(
                frontpage_urls.set_arquived,
                { 'ids': [delId] }
            ).success(function(data) {
                $('#warnings-validation').html("<div class='success'>" + data + '</div>');
            }).error(function(data) {
                $('#warnings-validation').html("<div class='error'>" + data.responseText + '</div>');
            });
        }
        $('#modal-element-archive').modal('hide');
        remove_element($('body').data('element-for-archive'));
        e.preventDefault();
    });

    $('#modal-element-archive').on('click', 'a.btn.no', function(e) {
        $('#modal-element-archive').modal('hide');
        e.preventDefault();
    });


    // Drop element button
    $('#frontpagemanager').on('click', 'div.placeholder div.content-provider-element a.drop-element', function(e) {
        e.preventDefault();
        var parent = $(this).closest('.content-provider-element');
        remove_element(parent);
        show_save_frontpage_dialog();
    });

    // suggest-home
    $('#frontpagemanager').on('click', 'div.placeholder div.content-provider-element a.suggest-to-home', function(e) {
        var element = $(this).closest('.content-provider-element');
        var contentId = element.data('content-id');
        if (contentId) {
            $.get(
                frontpage_urls.toggle_suggested,
                { 'ids': [contentId] }
            ).success(function(data) {
            }).error(function(data) {
            });
        }

        element.toggleClass('suggested');
        e.preventDefault();
    });

    // send-to-trash
    $('#modal-element-send-trash').modal({ backdrop: 'static', keyboard: true, show: false });
    $('#frontpagemanager').on('click', 'div.placeholder div.content-provider-element a.send-to-trash', function(e, ui) {
        var element = $(this).closest('.content-provider-element');
        var elementID = element.data('content-id');
        $('body').data('element-for-del', element);
        $('#modal-element-send-trash').data('selected-for-del', elementID);

        $('#modal-element-send-trash .modal-body span.title').html('<strong>' + element.find('.title').html() + '</strong>');
        $('#modal-element-send-trash ').modal('show');
        e.preventDefault();
    });

    $('#modal-element-send-trash').on('click', 'a.btn.yes', function(e, ui) {
        var delId = $('#modal-element-send-trash').data('selected-for-del');
        if (delId) {
            $.get(frontpage_urls.send_to_trash,
                { id: delId }
            );
        }
        show_save_frontpage_dialog();
        $('#modal-element-send-trash').modal('hide');
        $('body').data('element-for-del').animate({ 'backgroundColor': '#fb6c6c' },300).animate({ 'opacity': 0, 'height': 0 }, 300, function() {
            $(this).remove();
        });
        e.preventDefault();
    });

    $('#modal-element-send-trash').on('click', 'a.btn.no', function(e) {
        $('#modal-element-send-trash').modal('hide');
        e.preventDefault();
    });


    // Change-background-color
    $('#modal-element-customize-content').modal({ backdrop: 'static', keyboard: true, show: false });
    $('#frontpagemanager').on('click', 'div.placeholder div.content-provider-element a.change-color', function(e) {
        var element = $(this).closest('.content-provider-element');
        var elementID = element.data('content-id');

        var modal = $('#modal-element-customize-content');
        modal.data('element-for-customize-content', element);

        modal.data('selected-for-customize-content', elementID);


        var title = element.data('title')

        if (title['font-size'] !== undefined) {
            console.log(title['font-size']);
            var size = title['font-size'].substring(0,2);
            modal.find('.modal-body #font-size option[value='+size+']').attr('selected', 'selected');
        }
        if (title['font-family'] !== undefined) {
           modal.find('.modal-body #font-family').val(title['font-family']);
        }
        if (title['font-style'] !== undefined) {
            modal.find('.modal-body #font-style').val(title['font-style']);
        }
        if (title['font-weight'] !== undefined) {
            modal.find('.modal-body #font-style').val(title['font-weight']);
        }
        if (title['color'] !== undefined) {
            modal.find('.modal-body .fontcolor span.simplecolorpicker').css('background-color', title['color']);
            modal.find('.modal-body input#font-color').val(title['color']);
        }

        if (element.data('bg').length>0) {
            var bgcolor = element.data('bg').substring(17,24);
            modal.find('.modal-body .background span.simplecolorpicker').css('background-color', bgcolor);
            modal.find('.modal-body input#bg-color').val(bgcolor);
        }
        modal.modal('show');
        e.preventDefault();
    });

    $('#modal-element-customize-content').on('click', 'a.btn.yes', function(e, ui) {

        var elementID = $('#modal-element-customize-content').data('selected-for-customize-content');
        var element = $('[data-content-id='+elementID+']');
        var url = frontpage_urls.customize_content;

        var titleValues = new Object();

        var keys = new Array();
        var fontFamilyValue = $('#font-family').val();
        var fontSizeValue   = $('#font-size').val();
        var fontStyleValue  = $('#font-style').val();
        var fontColorValue  = $('#font-color').val();
        if(fontFamilyValue.length>0 && fontFamilyValue!='Auto') {
            titleValues["font-family"] = fontFamilyValue;
            keys[0] = "font-family";
        }
        if(fontSizeValue.length>0 && fontSizeValue!='Auto') {
            titleValues["font-size"] = fontSizeValue+'px';
            keys[1] = "font-size";
        }
        if(fontStyleValue.length>0 && fontStyleValue!='Auto') {
            titleValues["font-style"] = fontStyleValue;
            keys[2] = "font-style";
        }
        if(fontColorValue.length>0 && fontColorValue!='Auto' && fontColorValue!='#000000') {
            titleValues["color"] = fontColorValue;
            keys[3] = "color";
        }
        if(fontStyleValue.length>0 && fontStyleValue!='Auto') {
            titleValues["font-weight"] = fontStyleValue;
            keys[2] = "font-weight";
        }

        var jsonTitle = JSON.stringify(titleValues, keys);
        var properties = new Object();

        if(!$.isEmptyObject(titleValues)) {
             var name = 'title_' + $('#frontpagemanager').data('category');
            properties[name] = jsonTitle;
        }
        var bgcolor =$('#bg-color').val();
        if(bgcolor.length>0 && bgcolor !='#ffffff') {

            var name2='bgcolor_' + $('#frontpagemanager').data('category');
            properties[name2] = bgcolor;

        }
console.log(properties);
        if (elementID) {
            $.ajax({
                url:url,
                type:'POST',
                dataType: 'json',
                data: { 'id': elementID, 'properties' : properties}
            }).done(function(data) {
                 $('#modal-element-customize-content').data('element-for-customize-content').animate({ 'backgroundColor': bgcolor },300);
                  element.data('bg', 'background-color:'+bgcolor);
                  element.data('title', jsonTitle);

            }).error(function(data) {
                //data.message
            });
        }
        $('#modal-element-customize-content').modal('hide');

        e.preventDefault();

    });

    $('#modal-element-customize-content').on('click', 'a.btn.no', function(e) {
        $('#modal-element-customize-content').modal('hide');
        e.preventDefault();
    });


    /***************************************************************************
    * Content provider code
    ***************************************************************************/

    $('#content-provider').dialog({ minWidth: 700, autoOpen: false, maxHeight: 500 });

    $('#content-provider .content-provider-block-wrapper').tabs({
        ajaxOptions: {
            error: function(xhr, status, index, anchor ) {
                $(anchor.hash).html(
                    '<div>'+ frontpage_messages.error_tab_content_provider + '</div>');
            },
            complete: function() {
                $('#content-provider .spinner').hide();
            },
            beforeSend: function() {
                $('#content-provider .spinner').show();
            }
        },
        load: function(event,ui) {
            makeContentProviderAndPlaceholdersSortable();
        },
        fx: { opacity: 'toggle', duration: 'fast' }
    });

    $('#content-provider').on('click', '.pagination a', function(e, ui) {
        e.preventDefault();
        var href = $(this).attr('href');
        var parent = $(this).closest('.ui-tabs-panel');
        $.ajax({
            url: $(this).attr('href'),
            beforeSend: function() {
                $('#content-provider .spinner').show();
            }
        }).done(function(data) {
            parent.html(data);
            makeContentProviderAndPlaceholdersSortable();
        }).always(function() {
            $('#content-provider .spinner').hide();
        });
    });


    /***************************************************************************
    * General buttons actions code
    ***************************************************************************/

    $('#button_addnewcontents').on('click', function(e, ui) {
        e.preventDefault();
        $('#content-provider').dialog('open');
    });

    $('#button_savepositions').on('click', function(e, ui) {
        e.preventDefault();
        var els = get_contents_in_frontpage();
        var category = $('#frontpagemanager').data('category');
        var new_version_available = check_available_new_version(false);

        // If there is a new version available for this frontpage avoid to save
        if (new_version_available) {
            $('#modal-new-version').modal('show');
        } else {
            $.ajax({
                url: frontpage_urls.save_positions + '?category=' + category,
                async: false,
                type: 'POST',
                dataType: 'json',
                data: { 'contents_positions': els, 'last_version': frontpage_info.last_saved },
                beforeSend: function(xhr) {
                    $('#warnings-validation').html(
                    "<div class='alert alert-notice'>" +
                        "<button class='close' data-dismiss='alert'>×</button>"+
                        "Saving"+
                    '</div>');
                }
            }).done(function(data) {
                $('#warnings-validation').html(
                    "<div class='alert alert-success'>" +
                        "<button class='close' data-dismiss='alert'>×</button>" +
                        data.message +
                    '</div>');
                frontpage_info.last_saved = data.date;
            }).fail(function(data, ajaxOptions, thrownError) {
                var response = $.parseJSON(data.responseText);
                $('#warnings-validation').html(
                    "<div class='alert alert-error'>" +
                        "<button class='close' data-dismiss='alert'>×</button>" +
                        response.message +
                    '</div>'
                );
            });
        }
    });

    $('#button_clearcache').on('click', function(e, ui) {
        e.preventDefault();
        var category = $(this).data('category');
        $.ajax({
            type: 'POST',
            url: frontpage_urls.clean_frontpage,
            data: {
                'category' : category
            }
        }).done(function(data) {
            $('#warnings-validation').html(data);
        });
    });

    $('#button_previewfrontpage').on('click', function(e, ui) {
        e.preventDefault();
        var contents = get_contents_in_frontpage();
        var category = $(this).data('category-name');
        var encodedContents = JSON.stringify(get_contents_in_frontpage());

        $.ajax({
            type: 'POST',
            url: frontpage_urls.preview_frontpage,
            data: {
                'contents': encodedContents,
                'category_name': category
            },
            beforeSend: function(xhr) {
                $('#warnings-validation').html(
                    "<div class='alert alert-notice'>" +
                        "<button class='close' data-dismiss='alert'>×</button>" +
                        "Generating frontpage. Please wait..." +
                    '</div>'
                );
            }
        }).done(function(data) {
            previewWindow = window.open('', '_blank', '');
            previewWindow.document.write(data);
            previewWindow.focus();
            $('#warnings-validation').html('');
        });
    });

    $('#button_multiple_delete').on('click', function(e,ui) {
        e.preventDefault();
        var contents = $('#frontpagemanager .content-provider-element input[type="checkbox"]:checked').closest('.content-provider-element');
        if (contents.length > 0) {
            $('#modal-batch-delete').modal('show');
        }
    });

    $('#button_multiple_arquive').on('click', function(e,ui) {
        e.preventDefault();
        var contents = $('#frontpagemanager .content-provider-element input[type="checkbox"]:checked').closest('.content-provider-element');
        if (contents.length > 0) {
            $('#modal-batch-arquive').modal('show');
        }
    });

    $('#button_multiple_suggest').on('click', function(e,ui) {
        e.preventDefault();
        var contents = $('#frontpagemanager .content-provider-element input[type="checkbox"]:checked').closest('.content-provider-element');
        var contentIds = [];
        $(contents).each(function() {
            $(this).toggleClass('suggested');
            contentIds.push($(this).data('content-id'));
        });
        if (contentIds) {
            log(frontpage_urls.toggle_suggested, contentIds);
            $.get(
                frontpage_urls.toggle_suggested,
                { 'ids': contentIds }
            ).done(function(data) {
            }).fail(function(data) {
            });
        }
    });

    $('#pick-layout, .settings-panel .close').click('click', function(e, ui) {
        $('.settings-panel').slideToggle();
    });

});
