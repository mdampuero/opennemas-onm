{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Authors{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="AuthorCtrl" ng-init="getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-edit m-r-10"></i>
  <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/566184-opennemas-gesti%C3%B3n-de-autores" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
    <i class="fa fa-question"></i>
  </a>
{/block}

{block name="title"}
  <a class="no-padding" href="[% routing.generate('backend_authors_list') %]">
    {t}Authors{/t}
  </a>
{/block}

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      <li class="quicklinks">
        <a class="btn btn-link" ng-click="expansibleSettings()" title="{t 1=_('Author')}Config form: '%1'{/t}">
          <span class="fa fa-cog fa-lg"></span>
        </a>
      </li>
      <li class="quicklinks">
        <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
          <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
          {t}Save{/t}
        </button>
      </li>
    </ul>
  </div>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        <div class="form-group no-margin">
          <div class="checkbox">
            <input id="inrss" name="inrss" ng-false-value="'0'" ng-model="item.inrss" ng-true-value="'1'" type="checkbox">
            <label class="form-label" for="inrss">
              {t}Show in RSS{/t}
            </label>
            <div class="help m-t-5" ng-show="isHelpEnabled()">
              <i class="fa fa-info-circle m-r-5 text-info"></i>
              {t}If this option is activated this author will be showed in rss{/t}
            </div>
          </div>
        </div>
        {is_module_activated name="BLOG_MANAGER"}
          <div class="form-group no-margin m-t-15">
            <div class="checkbox">
              <input name="is_blog" id="is_blog" ng-false-value="'0'" ng-model="item.is_blog" ng-true-value="'1'" type="checkbox">
              <label class="form-label" for="is_blog">
                {t}View as Blog{/t}
              </label>
            </div>
            <div class="help m-t-5" ng-show="isHelpEnabled()">
              <i class="fa fa-info-circle m-r-5 text-info"></i>
              {t}If this option is activated page author will be showed as blog{/t}
            </div>
          </div>
        {/is_module_activated}
      </div>
      {acl isAllowed="USER_ADMIN"}
        {include file="ui/component/content-editor/accordion/slug.tpl"}
      {/acl}
    </div>
  </div>
{/block}
{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      <div class="row">
        <div class="col-md-4">
          <div class="thumbnail-wrapper">
            <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.photo1 }"></div>
            <div class="thumbnail-placeholder thumbnail-placeholder-small m-b-15">
              <div class="img-thumbnail img-thumbnail-circle" ng-if="!item.avatar_img_id">
                <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="item.avatar_img_id" ng-click="form.$setDirty(true)">
                  <i class="fa fa-picture-o fa-3x"></i>
                </div>
              </div>
              <div class="dynamic-image-placeholder ng-cloak" ng-show="item.avatar_img_id">
                <dynamic-image class="img-thumbnail img-thumbnail-circle" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.avatar_img_id" only-image="true">
                  <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="item.avatar_img_id" media-picker-type="photo" ng-click="form.$setDirty(true)"></div>
                </dynamic-image>
              </div>
            </div>
            <div class="row text-center">
              <div class="col-lg-8 col-lg-offset-2 col-md-12 col-md-offset-0 col-xs-8 col-xs-offset-2">
                <button class="btn btn-block btn-white m-b-15" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="item.avatar_img_id" media-picker-type="photo" type="button">
                  <i class="fa fa-picture-o"></i>
                  {t}Change{/t}
                </button>
              </div>
              <div class="col-lg-8 col-lg-offset-2 col-md-12 col-md-offset-0 col-xs-8 col-xs-offset-2">
                <button class="btn btn-block btn-danger m-b-15" ng-click="item.avatar_img_id = null" ng-disabled="!item.avatar_img_id">
                  <i class="fa fa-trash-o"></i>
                  {t}Remove{/t}
                </button>
              </div>
            </div>
            <div ng-if="item.facebook_id || item.twitter_id || item.google_id">
              <h4 class="text-center">
                {t}Connections{/t}
              </h4>
              <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-12 col-md-offset-0 col-xs-9 col-xs-offset-2" ng-if="item.facebook_id">
                  <button class="btn btn-block btn-facebook m-b-15">
                    <i class="fa fa-facebook"></i>
                    {t}Disconnect{/t}
                  </button>
                </div>
                <div class="col-lg-8 col-lg-offset-2 col-md-12 col-md-offset-0 col-xs-9 col-xs-offset-2" ng-if="item.twitter_id">
                  <button class="btn btn-block btn-twitter m-b-15">
                    <i class="fa fa-twitter"></i>
                    {t}Disconnect{/t}
                  </button>
                </div>
                <div class="col-lg-8 col-lg-offset-2 col-md-12 col-md-offset-0 col-xs-9 col-xs-offset-2" ng-if="item.google_id">
                  <button class="btn btn-block btn-google m-b-15">
                    <i class="fa fa-google-plus"></i>
                    {t}Disconnect{/t}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group" ng-class="{ 'has-error': form.name.$dirty && form.name.$invalid }">
                <label class="form-label" for="name">{t}Name{/t}</label>
                <div class="controls input-with-icon right">
                  <input class="form-control" id="name" name="name" ng-blur="getUsername()" ng-model="item.name" ng-maxlength="50" type="text"/>
                  <span class="icon right ng-cloak" ng-if="!flags.http.loading">
                    <span class="fa fa-check text-success" ng-if="form.name.$dirty && form.name.$valid"></span>
                    <span class="fa fa-info-circle text-info" ng-if="!form.name.$dirty && form.name.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                    <span class="fa fa-times text-error" ng-if="form.name.$dirty && form.name.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group" ng-class="{ 'has-error': form.name.$dirty && form.name.$invalid }">
                <label class="form-label" for="username">{t}Username{/t}</label>
                <div class="controls input-with-icon right">
                  <input class="form-control" id="username" name="username" ng-disabled="flags.http.slug" ng-model="item.username" ng-maxlength="50" required type="text">
                  <span class="icon right ng-cloak" ng-if="!flags.http.loading">
                    <span class="fa fa-circle-o-notch fa-spin" ng-if="flags.http.slug"></span>
                    <span class="fa fa-check text-success" ng-if="!flags.http.slug && form.username.$dirty && form.username.$valid"></span>
                    <span class="fa fa-info-circle text-info" ng-if="!flags.http.slug && !form.username.$dirty && form.username.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                    <span class="fa fa-times text-error" ng-if="!flags.http.slug && form.username.$dirty && form.username.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group" ng-class="{ 'has-error': form.email.$dirty && form.email.$invalid }">
            <label class="form-label" for="email">{t}Email{/t}</label>
            <div class="controls input-with-icon right">
              <span class="icon right" ng-if="!flags.http.loading">
                <span class="fa fa-check text-success" ng-if="form.email.$dirty && form.email.$valid"></span>
                <span class="fa fa-info-circle text-info" ng-if="!form.email.$dirty && form.email.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                <span class="fa fa-times text-error" ng-if="form.email.$dirty && form.email.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
              </span>
              <input class="form-control" id="email" name="email" placeholder="johndoe@example.org"  ng-model="item.email" required type="email">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label" for="twitter">
                  {t}Twitter user{/t}
                </label>
                <div class="controls">
                  <div class="input-group">
                    <span class="input-group-addon btn-twitter">
                      <i class="fa fa-twitter"></i>
                    </span>
                    <input class="form-control" id="twitter" name="twitter" ng-model="item.twitter" placeholder="{t}Username{/t}" type="text">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label" for="facebook">
                  {t}Facebook user{/t}
                </label>
                <div class="controls">
                  <div class="input-group">
                    <span class="input-group-addon btn-facebook">
                      <i class="fa fa-facebook"></i>
                    </span>
                    <input class="form-control" id="facebook" name="facebook" ng-model="item.facebook" placeholder="{t}Username{/t}" type="text">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="url">
              {t}Blog URL{/t}
            </label>
            <div class="controls">
              <input class="form-control" id="url" name="url" ng-model="item.url" placeholder="http://" type="text">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="bio">
              {t}Short Biography{/t}
            </label>
            <div class="controls">
              <input class="form-control" id="bio" name="bio" ng-model="item.bio" type="text">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="bio_description">
              {t}Biography{/t}
            </label>
            <div class="controls">
              <textarea class="form-control" id="bio_description" name="bio_description" ng-model="item.bio_description" rows="3"></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-expansible-fields">
    {include file="common/modals/_modalExpansibleFields.tpl"}
  </script>
{/block}
