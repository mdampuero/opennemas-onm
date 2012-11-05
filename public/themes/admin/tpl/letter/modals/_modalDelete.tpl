<div class="modal hide fade" id="modal-letter-delete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete letter{/t}</h3>
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
jQuery("#modal-letter-delete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false,
});

jQuery('.del').click(function(e) {
    jQuery('#modal-letter-delete .modal-body span').html( jQuery(this).data('title') );
    //Sets up the modal
    jQuery("#modal-letter-delete").modal('show');
    jQuery("body").data("selected-for-del", jQuery(this).data("url"));
    e.preventDefault();
});

jQuery('#modal-letter-delete a.btn.yes').on('click', function(e){
    var url = jQuery("body").data("selected-for-del");
    if (url) {
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

jQuery('#modal-letter-delete a.btn.no').on('click', function(e){
    jQuery("#modal-letter-delete ").modal('hide');
    e.preventDefault();
});
</script>