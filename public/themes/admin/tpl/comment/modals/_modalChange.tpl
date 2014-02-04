<div class="modal hide fade" id="modal-comment-change">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Change comment manager{/t}</h3>
    </div>
    <div class="modal-body">

        <p>{t}Opennemas supports multiple managers for comments. You can change to your desired manager whenever you want.{/t}</p>
        <p>{t}Pick the method to manage comments:{/t}</p>

        <ul class="comment-type-selector">
            <li>
                <a href="{url name=admin_comments_select type=onm}" class="clearfix">
                    <i class="icon icon-comment"></i>
                    {t}Built-in system{/t}
                </a>
            </li>
            <li>
                <a href="{url name=admin_comments_select type=disqus}" class="clearfix">
                    <img src="{$params.IMAGE_DIR}/disqus-icon.png" alt="Disqus" />
                    {t}Disqus{/t}
                </a>
            </li>
            <li>
                <a href="{url name=admin_comments_select type=facebook}" class="clearfix">
                    <i class="icon icon-facebook-sign"></i>
                    {t}Facebook{/t}
                </a>
            </li>
        </ul>
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
