makeContentProviderAndPlaceholdersSortable = function () {
    // Make content providers sortable and allow to D&D over the placeholders
    jQuery('div#content-provider .ui-tabs-panel > div').sortable({
        connectWith: "div.placeholder div.content",
        placeholder: 'placeholder-element',
        handle : '.description',
        update: function(event,ui) {
            jQuery('#warnings-validation').html('<div class="notice">'+frontpage_messages.remember_save_positions+'</div>');
        }
        //containment: '#content-with-ticker'
    }).disableSelection();

    // Make content providers sortable and allow to D&D over placeholders and content provider
    jQuery('div.placeholder div.content').sortable({
        connectWith: "div#content-provider .ui-tabs-panel > div, div.placeholder div.content",
        placeholder: 'placeholder-element',
        handle : '.description',
        update: function(event,ui) {
            jQuery('#warnings-validation').html('<div class="notice">'+frontpage_messages.remember_save_positions+'</div>');
        }
        //containment: '#content-with-ticker'
    }).disableSelection();
};
jQuery(function($){

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

    // Drop element button
    $('div.placeholder').on('click', 'div.content-provider-element a.drop-element', function(e) {
        e.preventDefault();
        var parent = $(this).closest('.content-provider-element');
        parent.animate({'backgroundColor':'#fb6c6c'},300).animate({'opacity': 0, 'height': 0 }, 300, function() {
            parent.remove();
        });
        show_save_frontpage_dialog();
    });

    // suggest-home
    //
    $("#modal-element-suggest-to-home").modal({ backdrop: 'static', keyboard: true });
    $('div.placeholder').on('click', 'div.content-provider-element a.suggest-to-home', function(e) {
        var element = $(this).closest('.content-provider-element');
        var elementID = element.data('content-id');
        var modal = $('#modal-element-suggest-to-home');
        if (element.is('.suggested')) {
            modal.find('.enable').hide();
            modal.find('.disable').show();
        } else {
            modal.find('.enable').show();
            modal.find('.disable').hide();
        }

        modal.data('selected-for-suggest-to-home', elementID);
        $("body").data('element-for-suggest-to-home', element);

        $('#modal-element-suggest-to-home .modal-body span.title').html( '<strong>' + element.find('.title').html() + '</strong>');
        modal.modal('show');
        e.preventDefault();
        return false;
    });

    $('#modal-element-suggest-to-home').on('click', 'a.btn.yes', function(e, ui){
        var contentId = $("#modal-element-suggest-to-home").data("selected-for-suggest-to-home");
        if(contentId) {
            $.ajax({
                url:  "/admin/controllers/common/content.php",
                type: "GET",
                data: { action:"toggle-suggested", id:contentId }
            });
        }
        $("#modal-element-suggest-to-home").modal('hide');
        $("body").data('element-for-suggest-to-home').toggleClass('suggested');
        e.preventDefault();
    });

    $('#modal-element-suggest-to-home').on('click', 'a.btn.no', function(e){
        $("#modal-element-suggest-to-home").modal('hide');
        e.preventDefault();
    });

    // arquive
    $("#modal-element-archive").modal({ backdrop: 'static', keyboard: true });
    $('div.placeholder').on('click', 'div.content-provider-element a.arquive', function(e) {
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
        var delId = $("#modal-element-archive").data("selected-for-del");
        log(delId);
        if(delId) {
            $.ajax({
                url:  "/admin/controllers/common/content.php",
                type: "GET",
                data: { action:"archive", id:delId }
            });
        }
        show_save_frontpage_dialog();
        $("#modal-element-archive").modal('hide');
        $("body").data('element-for-archive')
            .animate({ 'backgroundColor':'#fb6c6c' },300)
            .animate({ 'opacity': 0, 'height': 0 }, 300, function() {
                $(this).remove();
        });
        e.preventDefault();
    });

    $('#modal-element-archive').on('click', 'a.btn.no', function(e){
        $("#modal-element-archive").modal('hide');
        e.preventDefault();
    });



    // send-to-trash
    $("#modal-element-send-trash").modal({ backdrop: 'static', keyboard: true });
    $('div.placeholder').on('click', 'div.content-provider-element a.send-to-trash', function(e, ui) {
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
        log(delId);
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
        $( "#content-provider").dialog('open');
        e.preventDefault();
    });

    $('#button_savepositions').on('click',function() {
        var els = get_contents_in_frontpage();
        var category = $("#frontpagemanager").data("category");

        $.post("frontpagemanager.php?action=save_positions&category=" + category,
                { 'contents_positions': els }
        ).success(function(data) {
            $('#warnings-validation').html("<div class='success'>"+data+"</div>");
        }).error(function(data) {
            $('#warnings-validation').html("<div class='error'>"+data.responseText+"</div>");
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
        $.colorbox({
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

function show_save_frontpage_dialog() {
    jQuery('#warnings-validation').html('<div class="notice">'+frontpage_messages.remember_save_positions+'</div>');
}