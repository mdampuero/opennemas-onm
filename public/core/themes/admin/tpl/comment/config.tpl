{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Comments{/t} >
    {t}Configuration{/t}
{/block}

{block name="ngInit"}
  ng-controller="CommentsConfigCtrl" ng-init="init();"
{/block}

{block name="icon"}
  <i class="fa fa-comment m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_comments_list}">
    {t}Comments{/t}
  </a>
{/block}

{block name="primaryActions"}
  <li class="quicklinks">
    <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
      <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
      {t}Save{/t}
    </button>
  </li>
{/block}


{block name="extraTitle"}
  <li class="hidden-xs m-l-5 m-r-5 quicklinks">
    <h4>
      <i class="fa fa-angle-right"></i>
    </h4>
  </li>
  <li class="quicklinks">
    <h4>
      {t}Configuration{/t}
    </h4>
  </li>
{/block}

{block name="grid" prepend}
  <div class="content">
    {acl isAllowed="MASTER"}
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
              <input id="disable_comments" name="disable-comments" type="checkbox" ng-model="config.disable_comments">
              <label class="form-label" for="disable_comments">
                <span class="checkbox-title">{t}Disallow comments on site{/t}</span>
                <div class="help">
                  {t}If set, users will not be able to comment on the site and comments already approved will not be displayed{/t}
                </div>
              </label>
            </div>
          </div>
          <div class="form-group ng-cloak m-b-5 " ng-if="!config.disable_comments">
            <div class="checkbox">
              <input id="with_comments" name="with-comments" type="checkbox" ng-model="config.with_comments">
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
              <div class="alert alert-primary ng-cloak m-r-15 m-t-15" role="alert" ng-if="config.disable_comments">
                {t}Note: your site will not show any kind of comment and users will not be allowed to send more comments. Registered comments are not removed.{/t}
              </div>
              <p class="help">{t}Opennemas supports multiple managers for comments. You can change to your desired manager whenever you want.{/t}</p>
              <div class="comment-system col-sm-4">
                <div>
                  <a ng-model="extra.handler" ng-click="extra.handler = 'onm'" class="m-b-5 comment-system-block btn btn-block" ng-class="{ 'btn-primary': extra.handler == 'onm', 'btn-white': extra.handler != 'onm' }" uib-tooltip="{t}Use the built-in comment system{/t}">
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
                  <a ng-model="extra.handler" ng-click="extra.handler = 'facebook'" class="m-b-5 comment-system-block btn btn-block" ng-class="{ 'btn-primary': extra.handler == 'facebook', 'btn-white': extra.handler != 'facebook' }" uib-tooltip="{t}Use the Facebook comment system{/t}">
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
                  <a ng-model="extra.handler" ng-click="extra.handler = 'disqus'" class="m-b-5 comment-system-block btn btn-block" ng-class="{ 'btn-primary': extra.handler == 'disqus', 'btn-white': extra.handler != 'disqus' }" uib-tooltip="{t}Use the Disqus comment system{/t}">
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
      <div class="ng-cloak" ng-if="!config.disable_comments">
        <div ng-if="extra.handler == 'onm'">
          <div class="grid simple">
            <div class="grid-title">
              <h4>
                <i class="fa fa-eye m-r-5"></i>
                {t}Visualization{/t}
              </h4>
            </div>
            <div class="grid-body">
              <div class ="form-inline">
                <div class="form-group p-l-20" ng-class="{ 'has-error' : (config.number_elements < 3 || config.number_elements > 100 || !config.number_elements) }">
                  <label class="form-label" for="number_elements">
                    <h5>
                      {t}Number of comments to show{/t}:
                    </h5>
                  </label>
                  <input id="number_elements" class="form-control" name="number-elements" min=3 max=100 type="number" ng-model="config.number_elements">
                  <div class="help help-block">{t}Number of comments to show by page{/t}</div>
                </div>
              </div>
            </div>
          </div>
          <div class="grid simple">
            <div class="grid-title">
              <h4>
                <i class="fa fa-black-tie m-r-5"></i>
                {t}Comment moderation{/t}
              </h4>
            </div>
            <div class="grid-body">
              <div class="form-group">
                <div class="checkbox">
                  <input id="required_email" name="required-email" type="checkbox" ng-model="config.required_email">
                  <label class="form-label" for="required_email">
                    <span class="checkbox-title">{t}Email required{/t}</span>
                  </label>
                  <div class="help p-l-25">
                    {t}Comment must have an email assigned to be validated{/t}.
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="checkbox">
                  <input id="moderation_manual" name="moderation-manual" type="checkbox" ng-model="config.moderation_manual">
                  <label class="form-label" for="moderation_manual">
                    <span class="checkbox-title">{t}Manual moderation of comments{/t}</span>
                  </label>
                  <div class="help p-l-25">
                    {t}Comments must be manually approved by an administrator before is publicly available.{/t}
                  </div>
                </div>
              </div>
              <div class="row" ng-if="!config.moderation_manual">
                <div class="col-xs-12">
                  <h4><i class="fa fa-commenting m-r-5"></i> {t}Blacklist{/t}</h4>
                  <hr />
                </div>
                <div class="col-xs-12 col-sm-6">
                  <div class="form-group">
                    <div class="checkbox">
                      <input id="moderation_autoaccept" name="moderation-autoaccept" type="checkbox" ng-model="config.moderation_autoaccept">
                      <label class="form-label" for="moderation_autoaccept">
                        <span class="checkbox-title">{t escape=off}<strong>Auto-accept comments</strong> if they pass the blacklist rules{/t}</span>
                      </label>
                      <div class="help p-l-25">
                        {t}When a comment is submitted and it passes the blacklist checks, then accept it without intervenction.{/t}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                  <div class="form-group">
                    <div class="checkbox">
                      <input id="moderation_autoreject" name="moderation-autoreject" type="checkbox" ng-model="config.moderation_autoreject">
                      <label class="form-label" for="moderation_autoreject">
                        <span class="checkbox-title">{t escape=off}<strong>Auto-reject comments</strong> if they don't pass the blacklist rules{/t}</span>
                      </label>
                      <div class="help p-l-25">
                        {t}When a comment is submitted and it doesn't pass the blacklist checks, then reject it without intervenction.{/t}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xs-12">
                  <h5><i class="fa fa-fire m-r-5"></i> {t}Words and rules prohibited{/t}</h5>
                  <div class="form-group">
                    <textarea name="blacklist-comment" id="blacklist_comment" class="form-control" rows=10 ng-model="extra.blacklist_comment"></textarea>
                    <div class="help">{t}List of words or regular expressions that are prohibited on comments, one per line.{/t}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {is_module_activated name="es.openhost.module.acton"}
            <div class="grid simple">
              <div class="grid-title">
                <h4>
                  <i class="fa fa-address-book m-r-5"></i>
                  {t}Act-on{/t}
                </h4>
              </div>
              <div class="grid-body">
                <div class ="form-inline">
                  <div class="form-group p-l-20">
                    <label class="form-label" for="acton_list">
                      <h5>
                        {t}Act-on list where to import users who comment on the web{/t}:
                      </h5>
                    </label>
                    <input id="acton_list" class="form-control" name="acton-list" type="text" ng-model="config.acton_list">
                    <div class="help help-block">
                      {t}Only approved comments and non repeated e-mails will create a new contact on Act-on list.{/t}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          {/is_module_activated}
        </div>
        <div ng-if="extra.handler == 'facebook'">
          <div class="grid simple">
            <div class="grid-title">
              <h4>
                <i class="fa fa-cog"></i>
                {t}Configuration{/t}
              </h4>
            </div>
            <div class="grid-body">
              <div class="alert alert-danger m-l-20" role="alert" ng-if="!extra.facebook.api_key.trim()"> {t}Please enter your Facebook settings{/t} </div>
              <div class="form-group m-l-20" ng-class="{ 'has-error' : !extra.facebook.api_key.trim() }">
                <label class="form-label" for="facebook_api_key">
                  {t}Facebook App Id{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="facebook_api_key" name="facebook-api-key" type="text" ng-model="extra.facebook.api_key"/>
                  <div class="help">
                    {t escape=off}To be able to moderate comments of your site in Facebook you must create and set here your <strong>Facebook App Id</strong>.{/t}
                    <br>
                    {t escape=off}You can get your Facebook App Keys from <a href="https://developers.facebook.com/apps" target="_blank">Facebook Developers website</a>.{/t}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div ng-if="extra.handler == 'disqus'">
          <div class="grid simple">
            <div class="grid-title">
              <h4>
                <i class="fa fa-cog"></i>
                {t}Configuration{/t}
              </h4>
            </div>
            <div class="grid-body">
              <div class="alert alert-danger m-l-20" role="alert" ng-if="!extra.disqus_shortname || !extra.disqus_secret_key"> {t}Please enter your Disqus settings{/t} </div>
              <div class="row">
                <div class="col-sm-6 col-xs-12">
                  <div class="form-group m-l-20" ng-class="{ 'has-error' : !extra.disqus_shortname }">
                    <label class="form-label" for="shortname">
                      Disqus Id (extra.disqus_shortname)
                    </label>
                    <div class="controls">
                      <input class="form-control" id="shortname" name="shortname" required type="text" ng-model="extra.disqus_shortname"/>
                      <div class="help">
                        {t}A shortname is the unique identifier assigned to a Disqus site. All the comments posted to a site are referenced with the shortname{/t}.
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                  <div class="form-group m-l-20" ng-class="{ 'has-error' : !extra.disqus_secret_key }">
                    <label class="form-label" for="secret_key">
                      Disqus API Secret Key
                    </label>
                    <div class="controls">
                      <input class="form-control" id="secret_key" name="secret-key" required type="text" ng-model="extra.disqus_secret_key"/>
                      <div class="help">
                        {t escape=off}You can get your Disqus secret key in <a href="http://disqus.com/api/applications/" target="_blank">here</a>{/t}.
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    {/acl}
  </div>
{/block}
