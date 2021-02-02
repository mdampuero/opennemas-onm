<div class="modal hide fade" id="modal-book-delete">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Delete book{/t}</h3>
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
jQuery("#modal-book-delete").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false,
});

jQuery('.del').click(function(e) {
    e.preventDefault();
    log(jQuery(this).data('url'));
    jQuery('#modal-book-delete .modal-body span').html( jQuery(this).data('title') );
    //Sets up the modal
    jQuery('#modal-book-delete').data('url', jQuery(this).data('url'));

    jQuery("#modal-book-delete").modal('show');


});

jQuery('#modal-book-delete a.btn.yes').on('click', function(e){
    e.preventDefault();
    var url = jQuery('#modal-book-delete').data('url');

        if (url) {
            log(url);
            jQuery.ajax({
                url:  url,
                type: "POST",
                success: function(){
                    location.reload();
                }
            });
        }
        jQuery("#modal-book-delete").modal('hide');
});

jQuery('#modal-book-delete a.btn.no').on('click', function(e){
    jQuery("#modal-book-delete ").modal('hide');
    e.preventDefault();
});
</script>