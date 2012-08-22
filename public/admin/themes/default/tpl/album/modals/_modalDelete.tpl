<div class="modal hide fade" id="modal-album-delete">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete album{/t}</h3>
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
jQuery("#modal-album-delete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

jQuery('.del').click(function(e) {

    jQuery('#modal-album-delete .modal-body span').html( jQuery(this).data('title') );
    //Sets up the modal
    jQuery("#modal-album-delete").modal('show');
    jQuery("body").data("selected-for-del", jQuery(this).data("url"));
    e.preventDefault();
});

jQuery('#modal-album-delete a.btn.yes').on('click', function(){
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

jQuery('#modal-album-delete a.btn.no').on('click', function(e){
    jQuery("#modal-album-delete ").modal('hide');
    e.preventDefault();
});
</script>