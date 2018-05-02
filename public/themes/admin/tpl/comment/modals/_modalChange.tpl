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

        <div class="row clearfix comment-system-element">
          <a href="{url name=admin_comments_select type=onm}" class="col-md-2">
            <i class="fa fa-comment fa-4x"></i>
          </a>
          <div class="col-md-10">
            <a href="{url name=admin_comments_select type=onm}">
              <h4>{t}Built-in system{/t}</h4>
            </a>
            {t}Opennemas has simple but effective comment system that requires zero configuration.{/t}
          </div>
        </div>
        <div class="row clearfix comment-system-element">
          <a href="{url name=admin_comments_select type=disqus}" class="col-md-2">
            <img src="{$_template->getImageDir()}/disqus-icon.png" alt="Disqus" style="max-with:50px"/>
          </a>
          <div class="col-md-10">
            <a href="{url name=admin_comments_select type=disqus}">
              <h4>Disqus</h4>
            </a>
            {t escape=off}Integrate Opennemas with the <a href="http://www.disqus.com/">Disqus comment system</a> and use their powerful system to manage your website comments.{/t}
          </div>
        </div>
        <p></p>
        <div class="row clearfix comment-system-element">
          <a href="{url name=admin_comments_select type=facebook}" class="col-md-2">
            <i class="fa fa-facebook fa-4x"></i>
          </a>
          <div class="col-md-10">
            <a href="{url name=admin_comments_select type=facebook}">
              <h4>Facebook</h4>
            </a>
            {t escape=off}Integrate Opennemas with the <a href="https://developers.facebook.com/docs/plugins/comments/">Facebook comment system</a> and use their online tools to manage your website comments.{/t}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{javascripts}
  <script>
  jQuery("#modal-comment-change").modal({
      backdrop: 'static', //Show a grey back drop
      keyboard: true, //Can close on escape
      show: false
  });

  jQuery('.change').click(function(e) {
      //Sets up the modal
      jQuery("#modal-comment-change").modal('show');
      e.preventDefault();
  });
  </script>
{/javascripts}
