<div class="modal hide fade" id="modal-letter-batchDelete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete letters{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> letters?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-letter-batchDelete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true //Can close on escape

});

jQuery('.batch-delete').click(function(e,ui) {
    var number = jQuery(".minput:checked").length;
    log(number)
    if(number >= 1 ) {
        jQuery('#modal-letter-batchDelete .modal-body span').html(number);
        jQuery("#modal-letter-batchDelete").modal(true);
    }else{
        jQuery("#modal-letter-batchDelete").modal(false);
        jQuery("#modal-letter-accept").modal('show');
        jQuery('#modal-letter-accept .modal-body')
            .html("{t}You must select some elements.{/t}");
    }

    e.preventDefault();
});

jQuery('#modal-letter-batchDelete a.btn.yes').on('click', function(e,ui){
    jQuery('#formulario').attr('action', "{url name=admin_letters_batchdelete}");
    jQuery('#formulario').submit();
});

jQuery('#modal-letter-batchDelete a.btn.no').on('click', function(e,ui){
    jQuery("#modal-letter-batchDelete").modal('hide');
    e.preventDefault();
});
</script>
