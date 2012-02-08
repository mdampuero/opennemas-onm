<div class="modal hide fade" id="modal-letter-delete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete letter{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t}Are you sure that do you want delete "%title%"?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn primary yes" href="#">{t}Yes, delete{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-letter-delete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
});

jQuery('.del').click(function(e) {
    jQuery('#modal-letter-delete .modal-body').html(
    jQuery('#modal-letter-delete .modal-body').html()
        .replace( /%title%/g, jQuery(this).data('title'))
    );
    //Sets up the modal
    jQuery("#modal-letter-delete").modal('show');
    jQuery("body").data("selected-for-del", jQuery(this).data("id"));
    e.preventDefault();
});

jQuery('#modal-letter-delete a.btn.yes').on('click', function(){
    var delId = jQuery("body").data("selected-for-del");
    if(delId) {
        jQuery.ajax({
            url:  "{$smarty.server.SCRIPT_NAME}",
            type: "POST",
            data: { action:"delete", id:delId },
            success: function(){
                location.reload();
            }
        });
    }
    e.preventDefault();
});

jQuery('#modal-letter-delete a.btn.no').on('click', function(e){
    jQuery("#modal-letter-delete ").modal('hide');
    e.preventDefault();
});
</script>