<div class="modal hide fade" id="modal-confirm-send">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Are you sure to send the newsletter.{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}This newsletter is going to send. Are you sure?{/t}</p>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Send{/t}</a>
        <a class="btn secondary no" href="#">{t}Cancel{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-confirm-send").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

 jQuery('.confirm-send-button').on('click', function(e, ui) {
        if ($('#items-recipients li').length == 0 ) {
            $(".messages").html('<div class="alert alert-warning"><button class="close" data-dismiss="alert">×</button>Please, check some recipients for this newsletter</div>');
        } else {
            jQuery("#modal-confirm-send").modal('show');
        }
         e.preventDefault();
    });

jQuery('#modal-confirm-send a.btn.yes').on('click', function(){
    jQuery('#pick-recipients-form').attr('action', "{url name=admin_newsletter_send id=$id}");
    jQuery('#pick-recipients-form').submit();

    e.preventDefault();
});

jQuery('#modal-confirm-send a.btn.no').on('click', function(e){
    jQuery("#modal-confirm-send").modal('hide');
    e.preventDefault();
});
</script>
