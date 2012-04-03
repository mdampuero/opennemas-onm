<div class="modal hide fade" id="modal-author-delete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete author{/t}</h3>
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
jQuery("#modal-author-delete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true //Can close on escape
});

jQuery('.del').click(function(e) {
     jQuery('#modal-author-delete .modal-body span').html( jQuery(this).data('title') );
     jQuery.ajax({
        url:  "{$smarty.server.SCRIPT_NAME}",
        type: "GET",
        data: { action:"getOpinions", id: jQuery(this).data("id") },
        success: function(response){
            if(response != '') {
                jQuery('#modal-author-delete p').append(response);
            }
        }
    });
    //Sets up the modal
    jQuery("#modal-author-delete ").modal('show');
    jQuery("body").data("selected-for-del", jQuery(this).data("id"));
    e.preventDefault();
});

jQuery('#modal-author-delete a.btn.yes').on('click', function(){
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

jQuery('#modal-author-delete a.btn.no').on('click', function(e){
    jQuery("#modal-author-delete").modal('hide');
    e.preventDefault();
});
</script>