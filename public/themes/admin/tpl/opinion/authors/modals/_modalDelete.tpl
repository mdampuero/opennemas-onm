<div class="modal hide fade" id="modal-author-delete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete author{/t}</h3>
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
jQuery("#modal-author-delete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

jQuery('.del').click(function(e, ui) {
    e.preventDefault();
    //Sets up the modal
    jQuery("body").data("selected-for-del", jQuery(this).data("url"));
    jQuery('#modal-author-delete .modal-body span').html( jQuery(this).data('title') );
    jQuery("#modal-author-delete ").modal('show');
});

jQuery('#modal-author-delete a.btn.yes').on('click', function(e, ui){
    e.preventDefault();
    var url = jQuery("body").data("selected-for-del");
    if (url) {
        jQuery.ajax({
            url:  url,
            success: function(){
                location.reload();
            }
        });
    }
});

jQuery('#modal-author-delete a.btn.no').on('click', function(e, ui){
    jQuery("#modal-author-delete").modal('hide');
    e.preventDefault();
});
</script>