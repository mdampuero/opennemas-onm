//STEP 1 -Add Contents

//STEPS
$('#clean-button').on('click', function() {
    $('div#newsletter-container').find('ul li').remove();
});

$('#newsletter-pick-elements-form').on('submit', function(e, ui) {
    log('submit');
    saveContents();
});

$("#newsletter-pick-elements-form").on('click', '#next-button', function(e, ui) {
    total = $('#items-recipients li').length;
    if (total<0) {
        $(".messages").html('<div class="alert alert-notice"><button class="close" data-dismiss="alert">×</button>Choose email recipients</div>');
        e.preventDefault();
    } else {
        $("#modal-newsletter-accept").modal('hide');
        $('#newsletter-pick-elements-form').submit();
    }
});

$("#modal-newsletter-accept").on('click', '.accept', function(e, ui) {
    $("#modal-newsletter-accept").modal('hide');
    log('yes clicked');
    $('#newsletter-pick-elements-form').submit();
});

$('#modal-newsletter-accept a.btn.no').on('click', function(e) {
    e.preventDefault();
    $('#modal-newsletter-accept').modal('hide');
});

$("#modal-newsletter-accept").on('click', '.accept', function(e, ui) {
    $("#modal-newsletter-accept").modal('hide');
    log('yes clicked');

    $('#newsletter-pick-elements-form').submit();
});

saveContents = function() {
    var els = [];

    $('div#newsletter-container').find('div.container-receiver').each(function(index, container) {
        var lis = [];
        $(container).find('li').each(function(i, content) {
            lis.push({
                'id' : $(content).data('id'),
                'title': $(content).data('title'),
                'content_type': $(content).data('type'),
                'position': (i + 1)
            });
        });

        els.push({
            'id' : $(container).data('id'),
            'title': $(container).data('title'),
            'content_type': 'container',
            'position': (index + 1),
            'items': lis
        });

    });

    var encodedContents = JSON.stringify(els);
    $('#content_ids').val(encodedContents);
};

toggleProviderCheckbox = (function(item) {
    var toggleElement = $('input#toggleallcheckbox');

    var toggle = $(document).data('toggle');
    if (toggle === undefined) {
        toggle = true;
    }

    if (toggleElement) {
        $('ul#contentList li input[type=checkbox]').each(function() {
            $(this).prop('checked', toggle);
        });
    }
    $(document).data('toggle', !toggle);
});

//OPERATIONS
$('div.newsletter-contents').on('click', ' div.container-receiver .container-label', function(event) {
    $(this).closest('div.container-receiver')
        .addClass('active')
        .siblings().removeClass('active');
});

$('div#newsletter-container').on('click', '.container-label .icon-chevron-down, .container-label .icon-chevron-up', function(i, item) {
    var ul = $(this).closest('div.container-receiver').find('ul.content-receiver');
    if ($(ul).css('display') == 'none') {
      $(ul).show().sortable('enable');
    } else {
      $(ul).hide().sortable('disable');
    }
    $(this).toggleClass('icon-chevron-down icon-chevron-up');
});

$('div#newsletter-container').on('click', '.container-label .icon-trash', function(e) {
    $(this).closest('div.container-receiver').remove();

    e.preventDefault();
});

$('div#newsletter-container').on('click', '.icon-remove', function() {
    $(this).closest('.container-receiver').find('li').each(function (){
        $(this).remove();
    });
});

$('#button-add-container').on('click', function(event) {
    $('#modal-add-label').modal('show');
    return false;
});


$('modal-add-label').modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

$('#modal-add-label a.btn.save').on('click', function(e) {
//open modal
    var label = $('#modal-add-label input#container_label').val();
    var id = 1;
    $('div.column-receiver div.container-receiver').each(function(index, item) {
        if ($(item).data('id') > id) {
            id = $(item).data('id');
        }

    });
    id = id + 1;
    $('div.column-receiver').find('.container-receiver').removeClass('active');
    $('div.column-receiver').append('<div data-title="' + label + '" data-id="' + id +
            '" class="container-receiver active"><div class="container-label"><span>' +
            label + '</span> <div class="container-buttons btn-group">' +
            ' <i class="icon-chevron-down"></i><i class="icon-pencil"></i>' +
            ' <i class="icon-trash"></i> <i class="icon-clean"></i> </div> </div>' +
            ' <ul class="content-receiver"> </ul> </div>');


    $('div.column-receiver ul.content-receiver').sortable({
        connectWith: 'div#content-provider ul#contentList, div.column-receiver ul.content-receiver',
        dropOnEmpty: true,
        placeholder: 'placeholder-element',
        tolerance: 'pointer',
        items: 'li:not(.container-label)'
    }).disableSelection();

    $('#modal-add-label input#container_label').val('');
    $('#modal-add-label').modal('hide');

    e.preventDefault();
});

jQuery('div.newsletter-contents').on('click', '#button-check-all', function(event) {
    jQuery('#content-provider div.ui-tabs-panel:not(.ui-tabs-hide) ul#contentList li input[type=checkbox]').each(function() {
        jQuery(this).prop('checked', !jQuery(this).prop('checked'));
    });
});

jQuery('#add-selected').on('click', function(event) {
    if ((jQuery('div#newsletter-container div.active').length === 0)) {
        jQuery('#modal-container-active').modal('show');
    } else {
        jQuery('div.ui-tabs-panel:not(.ui-tabs-hide) ul#contentList li').find('input:checked').each(function() {
            if (this.checked === true) {
                jQuery(this).prop('checked', false);
                item = jQuery(this).parent();
                item.draggable('disable');
                item.removeClass('ui-state-disabled');
                jQuery('div#newsletter-container div.active ul.content-receiver').append(item);
            }
        });

        jQuery('ul#contentList li').find('input:checked').prop('checked', false);
        jQuery('input#toggleallcheckbox').prop('checked', false);
    }
});

$('div#newsletter-container').sortable({
    axis: 'y',
    handle: 'span',
    tolerance: 'pointer'
}).disableSelection();

$('modal-update-label').modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

$('#modal-update-label a.btn.save').on('click', function(e) {
    var label = $('#modal-update-label input#updated_label').val();

    var id = $('#modal-update-label input#updated_id').val();

    var container = $('div.container-receiver[data-id='+ id + ']');
    container.attr('data-title', label);
    $(container).find('div.container-label span').html(label);

    $('#modal-update-label').modal('hide');

    e.preventDefault();
});

/* Containers operations  */
$('div#newsletter-container').on('click', '.container-label .icon-pencil', function(e) {
    var container = $(this).closest('div.container-receiver');

    $('#modal-update-label input#updated_label').val(container.attr('data-title'));
    $('#modal-update-label input#updated_id').val(container.data('id'));

    $('#modal-update-label').modal('show');
    e.preventDefault();

});

/*****************************************************************************/

//SETP 2 - Preview
$('#newsletter-preview-form').on('click', '#edit-button', function(e, ui) {
    e.preventDefault();

    $(document).data('saved', false);

    $('#li-save-button').toggle();
    $('#edit-button').toggle();

    $.onmEditor({
        editor_class: '#html_content'
    });

    return false;
});

$('#newsletter-preview-form').on('click', '#save-button', function() {
    // Save updates
    CKEDITOR.instances.html_content.destroy();

    var htmlContent = $('#html_content').html();
    var title = $('#title').val();


    $('#li-save-button').toggle();
    $('#edit-button').toggle();

    $.ajax({
        url: newsletter_urls.save_contents,
        type: 'POST',
        data: { html: htmlContent, title: title },
        error: function(xhr, ajaxOptions, thrownError) {
            log(xhr.status + 'problem saving html code ');
        },
        success: function() {
            log('ok');
        }
    });
    $(document).data('saved', true);
});

$('#newsletter-preview-form').on('click', '#next-button, #prev-button', function() {
    var saved = $(document).data('saved');
    if (!saved) {
        log('not saved');
        $("#modal-save-changes").modal('show');
        return false;
    }
});

/*****************************************************************************/

// SETP 3 - ADD recipients
$('#pick-recipients-form').on('submit', function () {
    var recipients = [];

    $('#items-recipients li').each(function () {
        recipients.push(
            {
                name : $(this).data('name'),
                email : $(this).data('email')
            }
        );
    });
    recipients = JSON.stringify(recipients);
    $('#recipients_hidden').val(recipients);
});

$('#pick-recipients-form #accounts-provider').tabs();

$('#pick-recipients-form #database-accounts').on('click', '#add-selected', function(e, ui) {
    e.preventDefault();
    $('#database-accounts ul li input[type=checkbox]:checked').each(function() {
        item = $(this).closest('li');
        $('#items-recipients').append(item);

    });
    $('#button-check-all').prop('checked', false);
});


$('#database-accounts').on('click', '#button-check-all', function(e, ui) {
    e.preventDefault();
    $(this).data('toggled', !$(this).data('toggled'));
    var toggle = $(this).data('toggled');
    jQuery('#database-accounts ul li input[type=checkbox]').each(function() {
        if (toggle) {
            $(this).attr('checked', 'checked');
        } else {
            $(this).attr('checked', '');
        }

    });
});

$('#parse-and-add').on('click', function (e, ui) {
    e.preventDefault();

    var email_reg_ex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
    var raw_list = $('#othersMails').val();
    if (raw_list.length > 0) {
        var final_list = [];
        $.each(raw_list.split('\n'), function(index, item) {

            if (item.search(email_reg_ex) != -1) {
                final_list.push(item);
            }
        });
        total = $('#items-recipients li').length;
        $.each(final_list, function(index, item) {
            total = total +1;
            if (total <= 10) {
                item = $('<li></li>', {
                    'data-email' : item,
                    'data-name' : item,
                    'class' : 'account'
                }).html(item).append('<i class="icon icon-trash"></i>');
                $('#items-recipients').append(item);
            }
        });
        if(total >= 10) {
            $(".messages").html('<div class="alert alert-notice"><button class="close" data-dismiss="alert">×</button>You can send 10 custom email</div>');

        }
    }
});

$('#items-recipients').on('click', '.icon', function () {
    $(this).closest('.account').remove();
});

$('#database-accounts-list').sortable({
    connectWith: '#items-recipients',
    dropOnEmpty: true,
    placeholder: 'placeholder-element',
    tolerance: 'pointer'
}).disableSelection();

$('#maillist-account-list').sortable({
    connectWith: '#items-recipients',
    dropOnEmpty: true,
    placeholder: 'placeholder-element',
    tolerance: 'pointer'
}).disableSelection();

$('div#recipients ul#items-recipients').sortable({
    connectWith: '#database-accounts-list, #maillist-account-list',
    dropOnEmpty: true,
    placeholder: 'placeholder-element',
    tolerance: 'pointer'
}).disableSelection();

