<div class="modal hide fade" id="modal-file-batchDelete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete files{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> files?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-file-batchDelete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape

});

jQuery('.delChecked').click(function(e) {
    var number = jQuery(".minput:checked").length;
    if(number >= 1 ) {
        jQuery('#modal-file-batchDelete .modal-body span').html(number);
        jQuery("#modal-file-batchDelete").modal('show');
    }else{
        jQuery("#modal-file-batchDelete").modal('hide');
        jQuery("#modal-file-accept").modal('show');
        jQuery('#modal-file-accept .modal-body')
            .html("{t}You must select some elements.{/t}");
    }

    e.preventDefault();
});

jQuery('#modal-file-batchDelete a.btn.yes').on('click', function(e){
    jQuery('#formulario').attr('action', file_manager_urls.batchDelete);
    jQuery('#formulario').submit();
    e.preventDefault();
});

jQuery('#modal-file-batchDelete a.btn.no').on('click', function(e){
    jQuery("#modal-file-batchDelete").modal('hide');
    e.preventDefault();
});
</script>
