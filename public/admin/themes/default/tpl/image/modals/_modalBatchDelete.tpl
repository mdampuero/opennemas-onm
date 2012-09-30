<div class="modal hide fade" id="modal-image-batch-delete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete multiple images{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> images?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery(function(){
    jQuery("#modal-image-batch-delete").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false,
    });

    jQuery('.batch-delete-button').on('click', function(e, ui) {
        var number = jQuery(".minput:checked").length;
        if (number >= 1 ) {
            jQuery('#modal-image-batch-delete .modal-body span').html(number);
            jQuery("#modal-image-batch-delete").modal('show');
        } else {
            jQuery("#modal-image-accept").modal('show');
        }

        e.preventDefault();
    });

    jQuery('#modal-image-batch-delete .btn.yes').on('click', function(){
        jQuery('#formulario').attr('action', image_manager_urls.batchDelete);
        jQuery('#formulario').submit();
    });

    jQuery('#modal-image-batch-delete a.btn.no').on('click', function(e){
        jQuery("#modal-image-batch-delete").modal('hide');
        e.preventDefault();
    });
});
</script>
