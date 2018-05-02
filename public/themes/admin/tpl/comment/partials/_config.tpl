<style>
  .checkbox-title {
    font-family: 'Open Sans';
    font-weight: 300;
    color: #505458;
    font-size: 1.12em;
  }

  .comment-system .comment-system-block {
    border:1px solid #ccc;
    border-radius:3px;
    padding:10px ;
    cursor:pointer;
    width:100%;
    display:block;
    text-align:center;
  }
  .comment-system .comment-system-block:hover {
    background:#eee;
  }
  .comment-system.active .comment-system-block {
    background-color: #0aa699 !important;
    border: 1px solid #0aa699 !important;
    color: White;
  }
  .comment-system.active .comment-system-block .help {
    color:White;
  }
</style>

<div class="grid simple">
  <div class="grid-body">
    <div class="form-group">
      <h5>
        <i class="fa fa-bars m-r-5"></i>
        {t}Comment handler{/t}
      </h5>
      <div class="p-l-20 row">
        <p class="help">{t}Opennemas supports multiple managers for comments. You can change to your desired manager whenever you want.{/t}</p>
        <div class="comment-system col-sm-4" uib-tooltip="{t}Use the built-in comment system{/t}">
          <div>
            <a href="{url name=admin_comments_select type=onm}" class="comment-system-block btn btn-block {if $comment_system == 'onm'}btn-success{/if}">
              <i class="fa fa-comment"></i>
              <i>{t}Built-in system{/t}</i>
            </a>
            <div class="help">
            {t}Use our simple but effective comment system that requires zero configuration to start.{/t}
            </div>
          </div>
        </div>
        <div class="comment-system col-sm-4 " uib-tooltip="{t}Use the Facebook comment system{/t}">
          <div>
            <a href="{url name=admin_comments_select type=facebook}" class="comment-system-block btn btn-block {if $comment_system == 'facebook'}btn-success{/if}">
              <i class="fa fa-facebook"></i>
              Facebook
            </a>
            <div class="help">
            {t escape=off}Use the external <a href="https://developers.facebook.com/docs/plugins/comments/" target="_blank">Facebook comment system</a> to show comments on your page. You can only manage comments using their online tools.{/t}
            </div>
          </div>
        </div>
        <div class="comment-system col-sm-4" uib-tooltip="{t}Use the Disqus comment system{/t}">
          <div>
            <a href="{url name=admin_comments_select type=disqus}" class="comment-system-block btn btn-block {if $comment_system == 'disqus'}btn-success{/if}">
              <i class="fa fa-comment"></i>
              Disqus
            </a>
            <div class="help">
              {t escape=off}Use the external <a href="http://www.disqus.com/" target="_blank">Disqus comment system</a> and use their powerful system to manage your website comments.{/t}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="checkbox">
        <input id="disable_comments" name="configs[disable_comments]" type="checkbox" value="1" {if $configs['disable_comments'] == true}checked="checked"{/if} >
        <label class="form-label" for="disable_comments">
          <span class="checkbox-title">{t}Allow comments on site{/t}</span>
          <div class="help">
            {t}If set, users will not be able to comment on the site and comments already approved will not be displayed{/t}
          </div>
        </label>
      </div>
    </div>

    <div class="form-group">
      <div class="form-group">
        <div class="checkbox">
          <input id="with_comments" name="configs[with_comments]" type="checkbox" value="1" {if !isset($configs['with_comments']) || $configs['with_comments'] == true}checked="checked"{/if} >
          <label class="form-label" for="with_comments">
            <span class="checkbox-title">{t}Allow comments in contents by default{/t}</span>
            <div class="help">
              {t}Whether to allow users to comment in comments by default for all contents (you can change this setting for specific contents){/t}
            </div>
          </label>
        </div>
      </div>
    </div>
  </div>
</div>
