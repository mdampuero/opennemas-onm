<div class="modal hide fade" id="modal-comment-change">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Change comments system{/t}</h3>
    </div>
    <div class="modal-body">
        <p>{t escape=off}Are you sure you want to change the comments system?{/t}</p>

    </div>
    <div class="modal-footer">
        <a class="btn btn-primary yes" href="#">{t}Yes, change{/t}</a>
        <a class="btn secondary no" href="#">{t}No{/t}</a>
    </div>
</div>

<script>
jQuery("#modal-comment-change").modal({
    backdrop: 'static', //Show a grey back drop
    keyboard: true, //Can close on escape
    show: false
});

jQuery('.change').click(function(e) {
    jQuery('#modal-comment-change .modal-body span').html( jQuery(this).data('title') );
    //Sets up the modal
    jQuery("#modal-comment-change").modal('show');
    jQuery("#modal-comment-change").data('url', jQuery(this).data("url"));
    e.preventDefault();
});

jQuery('#modal-comment-change a.btn.yes').on('click', function(){
    window.location.href = '{url name=admin_comments_select type=reset}';
    e.preventDefault();
});

jQuery('#modal-comment-change a.btn.no').on('click', function(e){
    jQuery("#modal-comment-change").modal('hide');
    e.preventDefault();
});
</script>
