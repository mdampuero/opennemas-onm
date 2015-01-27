<div class="modal fade" id="modal-comment-change">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{t}Change comment manager{/t}</h4>
            </div>
            <div class="modal-body">
                <p>{t}Opennemas supports multiple managers for comments. You can change to your desired manager whenever you want.{/t}</p>
                <p>{t}Pick the method to manage comments:{/t}</p>

                <div class="row">
                    <div class="col-md-4 comment-system">
                        <a href="{url name=admin_comments_select type=onm}">
                            <i class="fa fa-comment fa-6x"></i>
                            <h4>{t}Built-in system{/t}</h4>
                        </a>
                    </div>
                    <div class="col-md-4 comment-system">
                        <a href="{url name=admin_comments_select type=disqus}" class="clearfix">
                            <img src="{$params.IMAGE_DIR}/disqus-icon.png" alt="Disqus" height="78"/>
                            <h4>{t}Disqus{/t}</h4>
                        </a>
                    </div>
                    <div class="col-md-4 comment-system">
                        <a href="{url name=admin_comments_select type=facebook}" class="clearfix">
                            <i class="fa fa-facebook fa-6x"></i>
                            <h4>{t}Facebook{/t}</h4>
                        </a>
                    </div>
                </div>
            </div>
        </div>
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
</script>

