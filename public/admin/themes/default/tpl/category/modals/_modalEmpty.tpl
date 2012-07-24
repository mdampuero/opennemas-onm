<div class="modal hide fade" id="modal-category-empty">
    <div class="modal-header">
      <a class="close" href="#">×</a>
      <h3>{t}Delete all the contents in category{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure that do you want delete all the contents in the category "<span>%title%</span>"?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, delete{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-category-empty").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true //Can close on escape
});

jQuery('.empty-category').click(function(e, ui) {
    jQuery('#modal-category-empty .modal-body span').html( jQuery(this).data('title') );
    //Sets up the modal
    jQuery("#modal-category-empty").modal('show');
    jQuery("body").data("selected-for-del", jQuery(this).data("url"));
    e.preventDefault();
});

jQuery('#modal-category-empty a.btn.yes').on('click', function(e, ui){
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

jQuery('#modal-category-empty a.btn.no').on('click', function(e){
    jQuery("#modal-category-empty ").modal('hide');
    e.preventDefault();
});
</script>