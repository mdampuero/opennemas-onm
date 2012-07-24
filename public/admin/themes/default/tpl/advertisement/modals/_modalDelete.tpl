<div class="modal hide fade" id="modal-advertisement-delete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete advertisement{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure that do you want delete "<span>%title%</span>"?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-advertisement-delete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
});

jQuery('.del').click(function(e, ui) {
    jQuery('#modal-advertisement-delete .modal-body span').html( jQuery(this).data('title') );
    //Sets up the modal
    jQuery("#modal-advertisement-delete").modal('show');
    jQuery("body").data("selected-for-del", jQuery(this).data("url"));
    e.preventDefault();
});

jQuery('#modal-advertisement-delete a.btn.yes').on('click', function(e, ui){
    var url = jQuery("body").data("selected-for-del");
    if (url) {
        jQuery.ajax({
            url:  url,
            success: function(){
                location.reload();
            }
        });
    }
    e.preventDefault();
});

jQuery('#modal-advertisement-delete a.btn.no').on('click', function(e, ui){
    jQuery("#modal-advertisement-delete ").modal('hide');
    e.preventDefault();
});
</script>