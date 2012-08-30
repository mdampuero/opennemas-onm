<div class="modal hide fade" id="modal-video-delete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal-video-delete" aria-hidden="true">×</button>
      <h3>{t}Delete video{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure that do you want delete "<span>%title%</span>"?{/t}</p>
    </div>
    <div class="relations"></div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete{/t}</a>
        <a class="btn secondary no" data-dismiss="modal-video-delete" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
(function($) {
    jQuery("#modal-video-delete").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false
    });

    jQuery('.del').click(function(e, ui) {
        e.preventDefault();
        jQuery('#modal-video-delete').data('url', jQuery(this).data('url'));
        jQuery('#modal-video-delete .modal-body span').html( jQuery(this).data('title') );

        //Sets up the modal
        jQuery("#modal-video-delete").modal('show');
        jQuery("body").data("selected-for-del", jQuery(this).data("id"));
    });

    jQuery('#modal-video-delete a.btn.yes').on('click', function(e, ui){
        e.preventDefault();

        var url = jQuery('#modal-video-delete').data('url');
        if (url) {
            jQuery.ajax({
                url:  url,
                type: "POST",
                success: function(){
                    location.reload();
                }
            });
        }
        jQuery("#modal-video-delete").modal('hide');
    });

    jQuery('#modal-video-delete a.btn.no').on('click', function(e){
        e.preventDefault();

        jQuery("#modal-video-delete").modal('hide');
    });
})(jQuery);
</script>