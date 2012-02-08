<div class="modal hide fade" id="modal-kiosko-batchDelete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete kioskos{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> kioskos?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-kiosko-batchDelete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape

});

jQuery('.delChecked').click(function(e) {
    var number = jQuery(".minput:checked").length;
    if(number >= 1 ) {
        jQuery('#modal-kiosko-batchDelete .modal-body span').html(number);
        jQuery("#modal-kiosko-batchDelete").modal(true);
    }else{
        jQuery("#modal-kiosko-batchDelete").modal(false);
        jQuery("#modal-kiosko-accept").modal('show');
        jQuery('#modal-kiosko-accept .modal-body')
            .html("{t}You must select some elements.{/t}");
    }

    e.preventDefault();
});

jQuery('#modal-kiosko-batchDelete a.btn.yes').on('click', function(){
    jQuery('#action').attr('value', "batchDelete");
    jQuery('#formulario').submit();
    e.preventDefault();
});

jQuery('#modal-kiosko-batchDelete a.btn.no').on('click', function(e){
    jQuery("#modal-kiosko-batchDelete").modal('hide');
    e.preventDefault();
});
</script>
