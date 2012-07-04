<div class="modal hide fade" id="modal-file-delete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete file{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure that do you want delete "<span>%title%</span>"?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn primary yes" href="#">{t}Yes, delete{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-file-delete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true //Can close on escape
});

jQuery('.del').click(function(e, ui) {
    e.preventDefault();
    jQuery('#modal-file-delete .modal-body span').html( jQuery(this).data('title') );
    //Sets up the modal
    jQuery("#modal-file-delete ").modal('show');
    jQuery("body").data("selected-for-del", jQuery(this).data("url"));
    log(jQuery('#modal-file-delete .modal-body span').html());
});

jQuery('#modal-file-delete a.btn.yes').on('click', function(e, ui){
    e.preventDefault();
    var delId = jQuery("body").data("selected-for-del");
    if ( delId ) {
        location.href = delId;
    }
});

jQuery('#modal-file-delete a.btn.no').on('click', function(e){
    jQuery("#modal-file-delete").modal('hide');
    e.preventDefault();
});
</script>