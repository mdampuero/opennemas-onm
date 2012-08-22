<div class="modal hide fade" id="modal-album-batchDelete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete albums{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to delete <span>%num%</span> albums?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete all{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-album-batchDelete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true //Can close on escape

});

jQuery('.delChecked').click(function(e, ui) {
    e.preventDefault();
    var number = jQuery(".minput:checked").length;
    if (number >= 1 ) {
        jQuery('#modal-album-batchDelete .modal-body span').html(number);
        jQuery("#modal-album-batchDelete").modal('show');
    } else {
        jQuery('#modal-album-accept .modal-body')
            .html("{t}You must select some elements.{/t}");
        jQuery("#modal-album-accept").modal('show');
    }
});

jQuery('#modal-album-batchDelete a.btn.yes').on('click', function(){
    jQuery('#formulario').attr('action', album_manager_urls.batch_delete);
    jQuery('#formulario').submit();
    e.preventDefault();
});

jQuery('#modal-album-batchDelete a.btn.no').on('click', function(e){
    jQuery("#modal-album-batchDelete").modal('hide');
    e.preventDefault();
});
</script>
