<div class="modal hide fade" id="modal-statics-delete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete statics{/t}</h3>
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
jQuery("#modal-statics-delete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true //Can close on escape
});

jQuery('.del').click(function(e) {
    jQuery('#modal-statics-delete .modal-body span').html( jQuery(this).data('title') );
    //Sets up the modal
    jQuery("#modal-statics-delete").modal('show');
    jQuery("body").data("selected-for-del", jQuery(this).data("id"));
    e.preventDefault();
});

jQuery('#modal-statics-delete a.btn.yes').on('click', function(e){
    var delId = jQuery("body").data("selected-for-del");
    if(delId) {
        jQuery.ajax({
            url:  "{url name=admin_staticpages_delete id=DELETE}",
            type: "POST",
            data: { action:"delete", id:delId },
            success: function(){
                location.reload();
            }
        });
    }
    e.preventDefault();
});

jQuery('#modal-statics-delete a.btn.no').on('click', function(e){
    jQuery("#modal-statics-delete").modal('hide');
    e.preventDefault();
});
</script>