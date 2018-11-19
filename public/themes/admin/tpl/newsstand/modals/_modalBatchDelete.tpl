<div class="modal hide fade" id="modal-kiosko-batch-delete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete kioskos{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> kioskos?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
(function($){
    $("#modal-kiosko-batch-delete").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false
    });

    jQuery('.batch-delete-button').on('click', function(e, ui) {
        e.preventDefault();
        var number = jQuery(".minput:checked").length;
        if (number >= 1 ) {
            jQuery('#modal-kiosko-batch-delete .modal-body span').html(number);
            jQuery("#modal-kiosko-batch-delete").modal('show');
        } else {
            jQuery("#modal-kiosko-accept").modal('show');
        }
    });

    jQuery('#modal-kiosko-batch-delete .btn.yes').on('click', function(){
        jQuery('#formulario').attr('action', cover_manager_urls.batchDelete);
        log(jQuery('#formulario'));
        jQuery('#formulario').submit();
    });

    jQuery('#modal-kiosko-batch-delete a.btn.no').on('click', function(e){
        jQuery("#modal-kiosko-batch-delete").modal('hide');
        e.preventDefault();
    });
})(jQuery);

</script>
