<div class="modal hide fade" id="modal-advertisement-batchDelete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete advertisements{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> advertisements?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-advertisement-batchDelete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false,
});

jQuery('.delChecked').click(function(e, ui) {
    var number = jQuery(".minput:checked").length;
    if(number >= 1 ) {
        jQuery('#modal-advertisement-batchDelete .modal-body span').html(number);
        jQuery("#modal-advertisement-batchDelete").modal('show');
    }else{
        jQuery("#modal-advertisement-batchDelete").modal('hide');
        jQuery("#modal-advertisement-accept").modal('show');
        jQuery('#modal-advertisement-accept .modal-body')
            .html("{t}You must select some elements.{/t}");
    }

    e.preventDefault();
});

jQuery('#modal-advertisement-batchDelete a.btn.yes').on('click', function(e, ui){
    jQuery('#formulario').attr('action', "{url name=admin_ads_batchdelete}");
    jQuery('#formulario').submit();
    e.preventDefault();
});

jQuery('#modal-advertisement-batchDelete a.btn.no').on('click', function(e, ui){
    jQuery("#modal-advertisement-batchDelete").modal('hide');
    e.preventDefault();
});
</script>
