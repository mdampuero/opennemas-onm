{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Users{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="UserCtrl" ng-init="getItem({$id});backup.master = {if $app.container->get('core.security')->hasPermission('MASTER')}true{else}false{/if}"
{/block}

{block name="icon"}
  <i class="fa fa-user m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="[% routing.generate('backend_users_list') %]">
    {t}Users{/t}
  </a>
{/block}

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      <li class="quicklinks">
        <a class="btn btn-link" ng-click="expansibleSettings()" title="{t 1=_('User')}Config form: '%1'{/t}">
          <span class="fa fa-cog fa-lg"></span>
        </a>
      </li>
      <li class="quicklinks">
        <div class="btn-group">
          <button class="btn btn-loading btn-success text-uppercase" ng-click="confirm()" ng-disabled="flags.http.saving || form.$invalid || (item.password && item.password !== rpassword)" type="button">
            <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
            {t}Save{/t}
          </button>
          <button class="btn btn-success dropdown-toggle" data-toggle="dropdown" ng-disabled="flags.http.saving || form.$invalid || (item.password && item.password !== rpassword)" ng-if="item.id" type="button">
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu no-padding pull-right" ng-if="item.id">
            <li>
              <a href="#" ng-click="convertTo('type', 2)" ng-if="item.type !== 2">
                <i class="fa fa-user-plus"></i>
                {t}Convert to{/t} {t}subscriber{/t} + {t}user{/t}
              </a>
            </li>
            <li class="divider" ng-if="item.type !== 2"></li>
            <li>
              <a href="#" ng-click="convertTo('type', 1)">
                <i class="fa fa-address-card"></i>
                {t}Convert to{/t} {t}subscriber{/t}
              </a>
            </li>
          </ul>
        </div>
      </li>
    </ul>
  </div>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      {acl isAllowed="USER_ADMIN"}
      <div class="grid-collapse-title">
        <div class="form-group no-margin">
          <div class="checkbox">
            <input id="activated" name="activated" ng-false-value="0" ng-model="item.activated" ng-true-value="1" type="checkbox">
            <label class="form-label" for="activated">
              {t}Enabled{/t}
            </label>
          </div>
        </div>
      </div>
      {acl isAllowed="USER_ADMIN"}
        {include file="ui/component/content-editor/accordion/slug.tpl"}
      {/acl}
      <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.user_groups }" ng-click="expanded.user_groups = !expanded.user_groups">
        <i class="fa fa-users m-r-10"></i>{t}User Groups{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.user_groups }"></i>
        <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.user_groups && countUserGroups(item) && (toArray(item.user_groups) | filter: { status: 1 }).length > 0">
          <span ng-show="countUserGroups(item) === toArray(data.extra.user_groups).length">{t}All{/t}</span>
          <span ng-show="countUserGroups(item) !== toArray(data.extra.user_groups).length">
            <strong>[% countUserGroups(item) %]</strong> {t}selected{/t}
          </span>
        </span>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.user_groups }">
        {acl isAllowed="USER_ADMIN"}
          <div class="checkbox p-b-5" ng-repeat="user_group in item.user_groups">
            <input id="checkbox-[% $index %]" ng-false-value="0" ng-model="user_group.status" ng-true-value="1" type="checkbox">
            <label for="checkbox-[% $index %]">[% data.extra.user_groups[user_group.user_group_id].name %]</label>
          </div>
        {/acl}
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.settings }" ng-click="expanded.language = !expanded.language">
        <i class="fa fa-globe m-r-10"></i>{t}Language & time{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.language }"></i>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.language }">
        <label class="form-label" for="language">
          {t}Language{/t}
        </label>
        <div class="controls">
          <select id="language" name="language" ng-model="item.user_language">
            <option value="[% key %]" ng-repeat="(key, value) in data.extra.languages">[% value %]</option>
          </select>
          <div class="m-t-10" ng-show="isHelpEnabled()">
            <small class="help m-l-3">
              <i class="fa fa-info-circle m-r-5 text-info"></i>
              {t}Used for interface, messages and units in the control panel{/t}
            </small>
          </div>
        </div>
      </div>
      {/acl}
    </div>
  </div>
  {if $id == $app.user->id}
    <div class="grid simple">
      <div class="grid-title">
        <h4>{t}Social Networks{/t}</h4>
      </div>
      <div class="grid-body">
        <div class="form-group">
          <label class="form-label" for="facebook_login">{t}Facebook{/t}</label>
          <div class="controls">
            <iframe src="{url name=backend_user_social id=$id resource='facebook'}" frameborder="0" style="width:100%;overflow-y:hidden;"></iframe>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="twitter_login">{t}Twitter{/t}</label>
          <div class="controls">
            <iframe src="{url name=backend_user_social id=$id resource='twitter'}" frameborder="0" style="width:100%;overflow-y:hidden;"></iframe>
          </div>
        </div>
      </div>
    </div>
  {/if}
{/block}

{block name="customFields"}
  <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('user_groups')">
    <input id="checkbox-user_groups" checklist-model="app.fields[contentKey].selected" checklist-value="'user_groups'" type="checkbox">
    <label for="checkbox-user_groups">
      {t}User Groups{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('language')">
    <input id="checkbox-language" checklist-model="app.fields[contentKey].selected" checklist-value="'language'" type="checkbox">
    <label for="checkbox-language">
      {t}Language{/t}
    </label>
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
                  <input class="form-control" id="username" name="username" ng-disabled="flags.http.slug" ng-model="item.username" ng-maxlength="50" required type="text"/>
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
          <div class="form-group">
            <label class="form-label" for="url">{t}Blog URL{/t}</label>
            <div class="controls">
              <input class="form-control" id="url" name="url" ng-model="item.url" placeholder="http://" type="text">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="short-bio">{t}Short Biography{/t}</label>
            <div class="controls">
              <input class="form-control" id="short-bio" name="short-bio" ng-model="item.bio" type="text">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="bio">{t}Biography{/t}</label>
            <div class="controls">
              <textarea class="form-control" id="bio" name="bio_description" ng-model="item.bio_description" rows="3"></textarea>
            </div>
          </div>
          <div class="form-group" ng-class="{ 'has-error': form.password.$dirty && form.password.$invalid }">
            <label class="form-label" for="password">{t}Password{/t}</label>
            <div class="controls">
              <div class="input-group">
                <span class="input-group-addon pointer" ng-click="passwordUnlocked = !passwordUnlocked">
                  <i class="fa fa-lock" ng-class="{ 'fa-unlock-alt': passwordUnlocked }"></i>
                </span>
                <input class="form-control no-animate" id="password" name="password" ng-model="item.password" maxlength="20" type="[% !passwordUnlocked ? 'password' : 'text' %]">
              </div>
            </div>
          </div>
          <div class="form-group no-margin" ng-class="{ 'has-error': form.password.$valid && item.password && item.password !== rpassword }">
            <label class="form-label" for="rpassword">{t}Confirm password{/t}</label>
            <div class="controls">
              <div class="input-group">
                <span class="input-group-addon pointer" ng-click="rpasswordUnlocked = !rpasswordUnlocked">
                  <i class="fa fa-lock" ng-class="{ 'fa-unlock-alt': rpasswordUnlocked }"></i>
                </span>
                <input class="form-control" id="rpassword" id="rpassword" maxlength="20" ng-model="rpassword" maxlength="20" type="[% !rpasswordUnlocked ? 'password' : 'text' %]">
              </div>
              <span class="input-group-status">
                <span class="fa fa-check text-success" ng-if="form.password.$dirty && item.password === rpassword"></span>
                <span class="fa fa-times text-error" ng-if="form.password.$valid && item.password && item.password !== rpassword" uib-tooltip="{t}The passwords don't match{/t}"></span>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-convert">
    {include file="subscriber/modal.convert.tpl"}
  </script>
  <script type="text/ng-template" id="modal-confirm">
    {include file="user/modal.confirm.tpl"}
  </script>
  <script type="text/ng-template" id="modal-expansible-fields">
    {include file="common/modals/_modalExpansibleFields.tpl"}
  </script>
{/block}
