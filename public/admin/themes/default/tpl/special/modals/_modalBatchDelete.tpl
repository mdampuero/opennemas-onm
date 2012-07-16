<div class="modal hide fade" id="modal-special-batchDelete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete specials{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> specials?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-special-batchDelete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true //Can close on escape

});

jQuery('.delChecked').click(function(e) {
    var number = jQuery(".minput:checked").length;
    if(number >= 1 ) {
        jQuery('#modal-special-batchDelete .modal-body span').html(number);
        jQuery("#modal-special-batchDelete").modal(true);
    }else{
        jQuery("#modal-special-batchDelete").modal(false);
        jQuery("#modal-special-accept").modal('show');
        jQuery('#modal-special-accept .modal-body')
            .html("{t}You must select some elements.{/t}");
    }

    e.preventDefault();
});

jQuery('#modal-special-batchDelete a.btn.yes').on('click', function(){
    jQuery('#formulario').attr('action', "{url name=admin_specials_batchdelete category=$category page=$page}");
    jQuery('#formulario').submit();
});

jQuery('#modal-special-batchDelete a.btn.no').on('click', function(e){
    jQuery("#modal-special-batchDelete").modal('hide');
    e.preventDefault();
});
</script>
