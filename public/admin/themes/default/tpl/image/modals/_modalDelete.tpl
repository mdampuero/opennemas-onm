<div class="modal hide fade" id="modal-image-delete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete image{/t}</h3>
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
jQuery("#modal-image-delete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false,
});

jQuery('.del').click(function(e) {
    jQuery('#modal-image-delete .modal-body span').html( jQuery(this).data('title') );
    //Sets up the modal
    jQuery("#modal-image-delete").modal('show');
    jQuery("#modal-image-delete").data('url', jQuery(this).data("url"));
    e.preventDefault();
});

jQuery('#modal-image-delete a.btn.yes').on('click', function(){
    var url = jQuery("#modal-image-delete").data("url");
    if(url) {
        jQuery.ajax({
            url:  url,
            type: "POST",
            success: function(){
                location.reload();
            }
        });
    }
    e.preventDefault();
});

jQuery('#modal-image-delete a.btn.no').on('click', function(e){
    jQuery("#modal-image-delete").modal('hide');
    e.preventDefault();
});
</script>