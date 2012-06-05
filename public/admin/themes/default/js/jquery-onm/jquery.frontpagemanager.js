function makeContentProviderAndPlaceholdersSortable () {
    // Make content providers sortable and allow to D&D over the placeholders
    jQuery('div#content-provider .ui-tabs-panel > div:not(.pagination)').sortable({
        connectWith: "div.placeholder div.content",
        placeholder: 'placeholder-element',
        handle : '.description',
        update: function(event,ui) {
            jQuery('#warnings-validation').html('<div class="notice">'+frontpage_messages.remember_save_positions+'</div>');
        },
        tolerance: 'pointer'
        //containment: '#content-with-ticker'
    }).disableSelection();

    // Make content providers sortable and allow to D&D over placeholders and content provider
    jQuery('div.placeholder div.content').sortable({
        connectWith: "div#content-provider .ui-tabs-panel > div:not(.pagination), div.placeholder div.content",
        placeholder: 'placeholder-element',
        handle : '.description',
        update: function(event,ui) {
            jQuery('#warnings-validation').html('<div class="notice">'+frontpage_messages.remember_save_positions+'</div>');
        },
        tolerance: 'pointer'
        //containment: '#content-with-ticker'
    }).disableSelection();
};

function get_tooltip_content (elem) {
    var parent_content_div = elem.closest('div.content-provider-element');
    var content_html = '';

    if (parent_content_div.data('popover-content') == undefined) {
        var id = parent_content_div.data('content-id');
        var url = '/admin/controllers/common/content.php?action=get-info&id='+id;
        var content = '';
        // jQuery.ajax({
        //     url: url,
        //     // async: false,
        //     dataType: 'json'
        // }).done(function(data) {
        //     content = data;
        // });
        content = content_states[id];

        var content_html = "State: "+content.state
            + "<br>Views: "+content.views
            + "<br>Category: "+content.category
            + "<br>Scheduled: <span class='scheduled-state "+content.scheduled_state+"'>"+content.scheduled_state+"</span>"
            + "<br>Start time: "+content.starttime
            + "<br>Last author: "+content.last_author;
        parent_content_div.data('popover-content', content_html);
    } else {
        content_html = parent_content_div.data('popover-content');
    }

    return content_html;
}

function get_tooltip_title (elem) {
    var ajaxdata;
    var id = elem.closest('div.content-provider-element').data('content-id');
    var url = '/admin/controllers/common/content.php?action=get-info&id='+id;
    // jQuery.ajax({
    //     url: url,
    //     // async: false,
    //     dataType: 'json'
    // }).done(function(data) {
    //     ajaxdata = data;
    // });

    content = content_states[id];
    return content.title;
}

function remove_element (element) {
    jQuery(element).each(function(){
        jQuery(this).animate({ 'backgroundColor':'#fb6c6c' },300).animate({ 'opacity': 0, 'height': 0 }, 300, function() {
            jQuery(this).remove();
        });
    });
}

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

function show_save_frontpage_dialog() {
    jQuery('#warnings-validation').html('<div class="notice">'+frontpage_messages.remember_save_positions+'</div>');
}

jQuery(function($){

    /***************************************************************************
    * Sortable handlers
    ***************************************************************************/
    makeContentProviderAndPlaceholdersSortable();

    /***************************************************************************
    * Batch Actions
    ***************************************************************************/
    $("#modal-batch-delete").modal({ backdrop: 'static', keyboard: true });
    $('#modal-batch-delete').on('click', 'a.btn.no', function(e,ui){
        e.preventDefault();
        $("#modal-batch-delete").modal('hide');
    });
    $('#modal-batch-delete').on('click', 'a.btn.yes', function(e, ui){
        e.preventDefault();
        var contents = $('#frontpagemanager .content-provider-element input[type="checkbox"]:checked').closest('.content-provider-element');
        show_save_frontpage_dialog();
        $("#modal-batch-delete").modal('hide');
        remove_element(contents);
        e.preventDefault();
    });

    $("#modal-batch-arquive").modal({ backdrop: 'static', keyboard: true });
    $('#modal-batch-arquive').on('click', 'a.btn.no', function(e,ui){
        e.preventDefault();
        $("#modal-batch-arquive").modal('hide');
    });
    $('#modal-batch-arquive').on('click', 'a.btn.yes', function(e, ui){
        e.preventDefault();
        var contents = $('#frontpagemanager .content-provider-element input[type="checkbox"]:checked').closest('.content-provider-element');
        var ids = [];

        contents.each(function (){
            log($(this).closest('.content-provider-element').data('content-id'));
            ids.push($(this).closest('.content-provider-element').data('content-id'));
        });
        $.post("/admin/controllers/common/content.php?action=archive",
                { 'ids': ids }
        ).success(function(data) {
            $('#warnings-validation').html("<div class='success'>"+data+"</div>");
        }).error(function(data) {
            $('#warnings-validation').html("<div class='error'>"+data.responseText+"</div>");
        });
        $("#modal-batch-arquive").modal('hide');
        remove_element(contents);
        e.preventDefault();
    });


    /***************************************************************************
    * Content elements in frontpage code
    ***************************************************************************/
    // Toggle content-provider-element checkbox if all the content-provider-elemnt is clicked
    $('div.content').on('click', 'div.placeholder div.content-provider-element .description', function() {
        checkbox = $(this).find('input[type="checkbox"]');
        checkbox.attr(
           'checked',
           !checkbox.is(':checked')
        );
        var checked_elements = $('div.placeholder div.content-provider-element input[type="checkbox"]:checked').length
        if (checked_elements > 0) {
            $('.old-button .batch-actions').fadeIn('fast');
        } else {
            $('.old-button .batch-actions').fadeOut('fast');
        };
    });
    $('div.content').on('mouseleave', 'div.placeholder div.content-provider-element', function(e) {
        $(this).find('.content-action-buttons').removeClass('open');
    });


    $('div.placeholder div.content-provider-element .info').hover(function(e, ui) {
        $('div.placeholder div.content-provider-element .info').popover('show');
    }, function(e, ui) {
        $('div.placeholder div.content-provider-element .info').popover('hide');
    });

    $('div.placeholder div.content-provider-element .info').each(function() {
        var element = $(this);

        $(this).popover({
            placement: 'left',
            // trigger: 'manual',
            animation: false,
            delay:0,
            title: get_tooltip_title(element),
            content: get_tooltip_content(element),
        })
    });

    /***************************************************************************
    * Dropdown menu content actions
    ***************************************************************************/
    // arquive
    $("#modal-element-archive").modal({ backdrop: 'static', keyboard: true });
    $('div.content').on('click', 'div.placeholder div.content-provider-element a.arquive', function(e) {
        var element = $(this).closest('.content-provider-element');
        var elementID = element.data('content-id');
        $("body").data('element-for-archive', element);
        var modal = $('#modal-element-archive');
        modal.data('selected-for-archive', elementID);

        modal.find('.modal-body span.title').html( '<strong>' + element.find('.title').html() + '</strong>');
        modal.modal('show');
        e.preventDefault();
    });

    $('#modal-element-archive').on('click', 'a.btn.yes', function(e, ui){
        var delId = $("#modal-element-archive").data("selected-for-archive");
        if(delId) {
            $.post("/admin/controllers/common/content.php?action=archive",
                    { 'ids': [delId] }
            ).success(function(data) {
                $('#warnings-validation').html("<div class='success'>"+data+"</div>");
            }).error(function(data) {
                $('#warnings-validation').html("<div class='error'>"+data.responseText+"</div>");
            });
        }
        $("#modal-element-archive").modal('hide');
        remove_element($("body").data('element-for-archive'));
        e.preventDefault();
    });

    $('#modal-element-archive').on('click', 'a.btn.no', function(e){
        $("#modal-element-archive").modal('hide');
        e.preventDefault();
    });


    // Drop element button
    $('div.content').on('click', 'div.placeholder div.content-provider-element a.drop-element', function(e) {
        e.preventDefault();
        var parent = $(this).closest('.content-provider-element');
        remove_element(parent);
        show_save_frontpage_dialog();
    });

    // suggest-home
    $('div.content').on('click', 'div.placeholder div.content-provider-element a.suggest-to-home', function(e) {
        var element = $(this).closest('.content-provider-element');
        var contentId = element.data('content-id');
        if(contentId) {
            $.post("/admin/controllers/common/content.php?action=toggle-suggested",
                { 'ids': [contentId] }
            ).success(function(data) {
            }).error(function(data) {
            });
        }

        element.toggleClass('suggested');
        e.preventDefault();
    });

    // send-to-trash
    $("#modal-element-send-trash").modal({ backdrop: 'static', keyboard: true });
    $('div.content').on('click', 'div.placeholder div.content-provider-element a.send-to-trash', function(e, ui) {
        var element = $(this).closest('.content-provider-element');
        var elementID = element.data('content-id');
        $("body").data('element-for-del', element);
        $('#modal-element-send-trash').data('selected-for-del', elementID);

        $('#modal-element-send-trash .modal-body span.title').html( '<strong>' + element.find('.title').html() + '</strong>');
        $("#modal-element-send-trash ").modal('show');
        e.preventDefault();
    });

    $('#modal-element-send-trash').on('click', 'a.btn.yes', function(e, ui){
        var delId = $("#modal-element-send-trash").data("selected-for-del");
        if(delId) {
            $.ajax({
                url:  "/admin/controllers/common/content.php",
                type: "GET",
                data: { action:"send-to-trash", id:delId }
            });
        }
        show_save_frontpage_dialog();
        $("#modal-element-send-trash").modal('hide');
        $("body").data('element-for-del').animate({ 'backgroundColor':'#fb6c6c' },300).animate({ 'opacity': 0, 'height': 0 }, 300, function() {
            $(this).remove();
        });
        e.preventDefault();
    });

    $('#modal-element-send-trash').on('click', 'a.btn.no', function(e){
        $("#modal-element-send-trash").modal('hide');
        e.preventDefault();
    });

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
        e.preventDefault();
        $( "#content-provider").dialog('open');
    });

    $('#button_savepositions').on('click',function(e, ui) {
        e.preventDefault();
        var els = get_contents_in_frontpage();
        var category = $("#frontpagemanager").data("category");

        $.post("frontpagemanager.php?action=save_positions&category=" + category,
                { 'contents_positions': els }
        ).success(function(data) {
            $('#warnings-validation').html("<div class='success'>"+data+"</div>");
        }).error(function(data) {
            $('#warnings-validation').html("<div class='error'>"+data.responseText+"</div>");
        });
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

        $.ajax({
            type: 'POST',
            url: "/admin/controllers/frontpagemanager/frontpagemanager.php?action=preview_frontpage",
            data: {
                'contents': encodedContents
            },
            success: function(data) {
                previewWindow = window.open('','_blank','');
                previewWindow.document.write(data);
                previewWindow.focus();
            }
        });

        // $.colorbox({
        //     href: "/admin/controllers/frontpagemanager/frontpagemanager.php?action=preview_frontpage&contents="+encodedContents,
        //     data: { 'contents': contents },
        //     title: 'Previsualización Portada',
        //     iframe: true,
        //     width: '90%',
        //     height: '90%'
        // });
    });

    $('#button_multiple_delete').on('click', function(e,ui){
        e.preventDefault();
        var contents = $('#frontpagemanager .content-provider-element input[type="checkbox"]:checked').closest('.content-provider-element');
        if(contents.length > 0) {
            $("#modal-batch-delete").modal('show');
        }
    });

    $('#button_multiple_arquive').on('click', function(e,ui){
        e.preventDefault();
        var contents = $('#frontpagemanager .content-provider-element input[type="checkbox"]:checked').closest('.content-provider-element');
        if (contents.length > 0) {
            $("#modal-batch-arquive").modal('show');
        }
    });

    $('#button_multiple_suggest').on('click', function(e,ui){
        e.preventDefault();
        var contents = $('#frontpagemanager .content-provider-element input[type="checkbox"]:checked').closest('.content-provider-element');
        var contentIds = [];
        $(contents).each(function(){
            $(this).toggleClass('suggested');
            contentIds.push($(this).data('content-id'));
        });
        if (contentIds) {
            $.post("/admin/controllers/common/content.php?action=toggle-suggested",
                { 'ids': contentIds }
            ).success(function(data) {
            }).error(function(data) {
            });
        }
    });

});