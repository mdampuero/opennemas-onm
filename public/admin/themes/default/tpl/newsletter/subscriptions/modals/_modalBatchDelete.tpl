<div class="modal hide fade" id="modal-subscriptors-batchDelete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal-subscriptors-batchDelete" aria-hidden="true">×</button>
      <h3>{t}Delete videos{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete those <span>%num%</span> subscriptors?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-subscriptors-batchDelete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false,
});

jQuery('.batchDeleteButton').click(function(e) {
    e.preventDefault();
    var number = jQuery(".minput:checked").length;
    if (number >= 1) {
        jQuery('#modal-subscriptors-batchDelete .modal-body span').html(number);
        jQuery("#modal-subscriptors-batchDelete").modal('show');
    } else {
        jQuery("#modal-subscriptors-batchDelete").modal('hide');
        jQuery("#modal-subscriptions-accept").modal('show');
        jQuery('#modal-subscriptions-accept .modal-body')
            .html("{t}You must select some elements.{/t}");
    }
});

jQuery('#modal-subscriptors-batchDelete .btn.yes').on('click', function(){
    jQuery('#formulario').attr('action', '{url name=admin_newsletter_subscriptor_batch_delete}');
    jQuery('#formulario').submit();
});

jQuery('#modal-subscriptors-batchDelete a.btn.no').on('click', function(e){
    jQuery("#modal-subscriptors-batchDelete").modal('hide');
    e.preventDefault();
});
</script>
