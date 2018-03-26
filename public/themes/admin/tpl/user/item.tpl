{extends file="base/admin.tpl"}

{block name="content"}
  <form name="form" ng-controller="UserCtrl" ng-init="getItem({$id});master = {if $app.user->isMaster()}true{else}false{/if}">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_users_list') %]">
                  <i class="fa fa-user"></i>
                  {t}Users{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="!flags.http.loading && item">
              <div class="p-l-10 p-r-10 p-t-10">
                <i class="fa fa-angle-right"></i>
              </div>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="!flags.http.loading && item">
              <h5 class="ng-cloak">
                <strong ng-if="item.id">{t}Edit{/t}</strong>
                <strong ng-if="!item.id">{t}Create{/t}</strong>
              </h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success text-uppercase" ng-click="confirm()" type="button">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                  <span class="text">{t}Save{/t}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="listing-no-contents" ng-hide="!flags.http.loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && item === null">
        <div class="text-center p-b-15 p-t-15">
          <a href="[% routing.generate('backend_users_list') %]">
            <i class="fa fa-4x fa-warning text-warning"></i>
            <h3>{t 1=$id}Unable to find any user with id "%1".{/t}</h3>
            <h4>{t}Click here to return to the list of users.{/t}</h4>
          </a>
        </div>
      </div>
      <div class="ng-cloak" ng-show="!flags.http.loading && item">
        <div class="row">
          <div class="col-sm-8">
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
                  <div class="col-sm-8">
                    <div class="form-group" ng-class="{ 'has-error': form.name.$dirty && form.name.$invalid }">
                      <label class="form-label" for="name">{t}Name{/t}</label>
                      <div class="controls input-with-icon right">
                          <input class="form-control" id="name" name="name" ng-model="item.name" ng-maxlength="50" type="text"/>
                        <span class="icon right ng-cloak" ng-if="!flags.http.loading">
                          <span class="fa fa-check text-success" ng-if="form.name.$dirty && form.name.$valid"></span>
                          <span class="fa fa-info-circle text-info" ng-if="!form.name.$dirty && form.name.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                          <span class="fa fa-times text-error" ng-if="form.name.$dirty && form.name.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                        </span>
                      </div>
                    </div>
                    <div class="form-group" ng-class="{ 'has-error': form.username.$dirty && form.username.$invalid }">
                      <label class="form-label" for="username">{t}Username{/t}</label>
                      <div class="controls input-with-icon right">
                        <input class="form-control" id="username" name="username" ng-model="item.username" ng-maxlength="50" type="text"/>
                        <span class="icon right ng-cloak" ng-if="!flags.http.loading">
                          <span class="fa fa-check text-success" ng-if="form.username.$dirty && form.username.$valid"></span>
                          <span class="fa fa-info-circle text-info" ng-if="!form.username.$dirty && form.username.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                          <span class="fa fa-times text-error" ng-if="form.username.$dirty && form.username.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                        </span>
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
                    <div class="form-group" ng-class="{ 'has-error': form.password.$valid && item.password && item.password !== rpassword }">
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
          </div>
          <div class="col-sm-4">
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
                  <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.user_groups }" ng-click="expanded.user_groups = !expanded.user_groups">
                    <i class="fa fa-users m-r-5"></i>
                    {t}User Groups{/t}
                    <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.user_groups }"></i>
                    <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.user_groups && (toArray(item.user_groups) | filter: { status: 1 }).length > 0">
                      <span ng-show="(toArray(item.user_groups) | filter: { status: 1 }).length === toArray(data.extra.user_groups).length">{t}All{/t}</span>
                      <span ng-show="(toArray(item.user_groups) | filter: { status: 1 }).length !== toArray(data.extra.user_groups).length">
                        <strong>[% (toArray(item.user_groups) | filter: { status: 1 }).length %]</strong> {t}selected{/t}
                      </span>
                    </span>
                  </div>
                  <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.user_groups }">
                    <div class="ng-cloak">
                      {acl isAllowed="USER_ADMIN"}
                        <div class="checkbox p-b-5" ng-repeat="user_group in data.extra.user_groups">
                          <input id="checkbox-[% $index %]" ng-false-value="0" ng-model="item.user_groups[user_group.pk_user_group].status" ng-true-value="1" type="checkbox">
                          <label for="checkbox-[% $index %]">[% user_group.name %]</label>
                        </div>
                      {/acl}
                    </div>
                  </div>
                  <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.category = !expanded.category">
                    <input name="categories" ng-value="categories" type="hidden">
                    <i class="fa fa-bookmark m-r-5"></i>
                    {t}Categories{/t}
                    <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.category }"></i>
                    <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.category">
                      <span ng-show="item.categories.length === 0 || flags.categories.none">{t}All{/t}</span>
                      <span ng-show="item.categories.length != 0 && !flags.categories.none">
                        <strong>[% item.categories.length %]</strong> {t}selected{/t}
                      </span>
                    </span>
                  </div>
                  <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.category, 'no-animate': flags.categories.none }">
                    <div class="checkbox">
                      <input id="category-all" name="category-all" ng-model="flags.categories.none" ng-true-value="true" ng-false-value="false" type="checkbox">
                      <label class="form-label" for="category-all">
                        {t}Access to all categories{/t}
                      </label>
                    </div>
                    <div class="m-t-10" ng-show="!flags.categories.none">
                      <div class="m-b-10">
                        <small class="help">
                          <i class="fa fa-info-circle m-r-5 text-info"></i>
                          {t}The user can assign contents to the selected categories only{/t}
                        </small>
                      </div>
                      <div class="checkbox p-b-5">
                        <input id="toggle-categories" name="toggle-categories" ng-change="areAllCategoriesSelected()" ng-model="flags.categories.all" type="checkbox">
                        <label class="form-label" for="toggle-categories">
                          {t}Select/deselect all{/t}
                        </label>
                      </div>
                      <div class="checkbox-list checkbox-list-user-groups">
                        <div class="checkbox p-b-5" ng-repeat="category in (filteredCategories = (data.extra.categories | filter : { parent: 0 }))" ng-if="category.id != 0">
                          <div class="m-t-15" ng-if="$index > 0 && category.type != filteredCategories[$index - 1].type">
                            <h5 ng-if="category.type == 1"><i class="fa fa-sticky-note m-r-5"></i>{t}Contents{/t}</h5>
                            <h5 ng-if="category.type == 7"><i class="fa fa-camera m-r-5"></i>{t}Albums{/t}</h5>
                            <h5 ng-if="category.type == 9"><i class="fa fa-play-circle-o m-r-5"></i>{t}Videos{/t}</h5>
                            <h5 ng-if="category.type == 11"><i class="fa fa-pie-chart m-r-5"></i>{t}Polls{/t}</h5>
                          </div>
                          <div ng-if="category.parent == 0">
                            <input id="category-[% category.id %]" name="category-[% category.id %]" checklist-model="item.categories" checklist-value="category.id" type="checkbox">
                            <label class="form-label" for="category-[% category.id %]">
                              [% category.name %]
                            </label>
                          </div>
                          <div ng-if="category.id != 0">
                            <div ng-repeat="subcategory in extra.categories | filter : { parent: category.id }">
                              <input id="category-[% subcategory.id %]" name="category-[% subcategory.id %]" checklist-model="item.categories" checklist-value="subcategory.id" type="checkbox">
                              <label class="form-label" for="category-[% subcategory.id %]">
                                &rarr; [% subcategory.name %]
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    <div class="m-t-5" ng-if="flags.categories.all">
                      <small class="help">
                        <i class="fa fa-exclamation-triangle m-r-5 text-warning"></i>
                        {t}We recomend you to use the "Access to all categories" mark to avoid unchecked future created categories{/t}
                      </small>
                    </div>
                    </div>
                  </div>
                  <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.settings }" ng-click="expanded.language = !expanded.language">
                    <i class="fa fa-globe m-r-5"></i>
                    {t}Language & time{/t}
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
                      <div class="m-t-10">
                        <small class="help">
                          <i class="fa fa-info-circle m-r-5 text-info"></i>
                          {t}Used for interface, messages and units in the control panel{/t}</div>
                        </small>
                      </div>
                    </div>
                  </div>
                {/acl}
              </div>
            </div>
            {if isset($user['id']) && ($user['id'] == $app.user->id)}
            <div class="row">
              <div class="col-md-12">
                <div class="grid simple">
                  <div class="grid-title">
                    <h4>{t}Social Networks{/t}</h4>
                  </div>
                  <div class="grid-body">
                    <div class="form-group">
                      <label class="form-label" for="facebook_login">{t}Facebook{/t}</label>
                      <div class="controls">
                        <iframe src="{url name=backend_user_social id=$user['id'] resource='facebook'}" frameborder="0" style="width:100%;overflow-y:hidden;"></iframe>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="twitter_login">{t}Twitter{/t}</label>
                      <div class="controls">
                        <iframe src="{url name=backend_user_social id=$user['id'] resource='twitter'}" frameborder="0" style="width:100%;overflow-y:hidden;"></iframe>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            {/if}
          </div>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="modal-confirm">
      {include file="user/modal.confirm.tpl"}
    </script>
  </form>
{/block}
