//STEP 1 -Add Contents

//STEPS
$('#newsletter-pick-elements-form').on('submit', function(e, ui) {
    log('submit');
    saveContents();
});

$("#newsletter-pick-elements-form").on('click', '#next-button', function(e, ui) {
    total = $('#items-recipients li').length;
    if (total<0) {
        $(".messages").html('<div class="warning alert-message"><button class="close" data-dismiss="alert">×</button>Choose email recipients</div>');
        e.preventDefault();
    } else {
        $("#modal-newsletter-accept").modal('hide');
        $('#newsletter-pick-elements-form').submit();
    }
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
                }).html(item).append('<i class="fa fa-trash"></i>');
                $('#items-recipients').append(item);
            }
        });
        if(total >= 10) {
            $(".messages").html('<div class="alert alert-warning"><button class="close" data-dismiss="alert">×</button>Remember, only 10 recipients are allowed manually</div>');

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

