<div class="modal hide fade" id="modal-element-send-trash">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete menu{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure that do you want to send to trash "<span>%title%</span>"?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn primary yes" href="#">{t}Send to trash{/t}</a>
        <a class="btn secondary no" href="#">{t}Keep{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-element-send-trash").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true //Can close on escape
});

jQuery('.send-to-trash').click(function(e) {
    jQuery('#modal-element-send-trash .modal-body span').html( jQuery(this).data('title') );
    //Sets up the modal
    jQuery("#modal-element-send-trash ").modal('show');
    jQuery("body").data("selected-for-del", jQuery(this).data("id"));
    e.preventDefault();
});

jQuery('#modal-element-send-trash a.btn.yes').on('click', function(){
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

jQuery('#modal-element-send-trash a.btn.no').on('click', function(e){
    jQuery("#modal-menu-send-trash ").modal('hide');
    e.preventDefault();
});
</script>