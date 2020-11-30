<div class="grid simple">
  <div class="grid-title">
    <h4>
      <i class="fa fa-toggle-on"></i>
      {t}Default settings{/t}
    </h4>
  </div>

  <div class="grid-body">

    <div class="form-group">
      <div class="checkbox">
        <input id="disable_comments" name="configs[disable_comments]" type="checkbox"  ng-model="configs.disable_comments" value=1>
        <label class="form-label" for="disable_comments">
          <span class="checkbox-title">{t}Disallow comments on site{/t}</span>
          <div class="help">
            {t}If set, users will not be able to comment on the site and comments already approved will not be displayed{/t}
          </div>
        </label>
      </div>
    </div>

    <div class="form-group ng-cloak m-b-5 " ng-show="!configs.disable_comments">
      <div class="checkbox">
        <input id="with_comments" name="configs[with_comments]" type="checkbox" value=1 ng-model="configs.with_comments">
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

<div class="grid simple">
  <div class="grid-title">
    <h4>
      <i class="fa fa-bars m-r-5"></i>
      {t}Comment handler{/t}
    </h4>
  </div>

  <div class="grid-body">
    <div class="form-group">

      <div class="p-l-20 row">
        <div class="alert alert-primary ng-cloak m-r-15 m-t-15" role="alert" ng-show="configs.disable_comments">
          {t}Note: your site will not show any kind of comment and users will not be allowed to send more comments. Registered comments are not removed.{/t}
        </div>

        <p class="help">{t}Opennemas supports multiple managers for comments. You can change to your desired manager whenever you want.{/t}</p>

        <div class="comment-system col-sm-4">
          <div>
            <a ng-click="changeHandler('{t}Built-in system{/t}', 'fa-comments', '{url name=backend_comments_select type=onm}')" class="m-b-5 comment-system-block btn btn-block {if $extra['handler'] == 'onm'}btn-primary{else}btn-white{/if}" uib-tooltip="{t}Use the built-in comment system{/t}">
              <i class="fa fa-comments fa-3x m-t-5 m-b-10"></i><br/>
              {t}Built-in system{/t}
            </a>
            <div class="help">
            {t}Use our simple but effective comment system that requires zero configuration to start.{/t}
            </div>
          </div>
        </div>
        <div class="comment-system col-sm-4 " >
          <div>
            <a ng-click="changeHandler('Facebook', 'fa-facebook-official', '{url name=backend_comments_select type=facebook}')" class="m-b-5 comment-system-block btn btn-block {if $extra['handler'] == 'facebook'}btn-primary{else}btn-white{/if}" uib-tooltip="{t}Use the Facebook comment system{/t}">
              <i class="fa fa-facebook-official fa-3x m-t-5 m-b-10"></i><br/>
              Facebook
            </a>
            <div class="help">
            {t escape=off}Use the external <a href="https://developers.facebook.com/docs/plugins/comments/" target="_blank">Facebook comment system</a> to show comments on your page. You can only manage comments using their online tools.{/t}
            </div>
          </div>
        </div>
        <div class="comment-system col-sm-4">
          <div>
            <a  ng-click="changeHandler('Disqus', 'fa-comment', '{url name=backend_comments_select type=disqus}')"  class="m-b-5 comment-system-block btn btn-block {if $extra['handler'] == 'disqus'}btn-primary{else}btn-white{/if}" uib-tooltip="{t}Use the Disqus comment system{/t}">
              <i class="fa fa-comment fa-3x m-t-5 m-b-10"></i><br/>
              Disqus
            </a>
            <div class="help">
              {t escape=off}Use the external <a href="http://www.disqus.com/" target="_blank">Disqus comment system</a> and use their powerful system to manage your website comments.{/t}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
