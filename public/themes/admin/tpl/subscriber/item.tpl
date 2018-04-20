{extends file="base/admin.tpl"}

{block name="content"}
  <form name="form" ng-controller="SubscriberCtrl" ng-init="getItem({$id})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-address-card m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_subscribers_list') %]">
                  {t}Subscribers{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs m-l-5 m-r-5">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <h4>
                {if empty($id)}{t}Create{/t}{else}{t}Edit{/t}{/if}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right ng-cloak" ng-if="!flags.http.loading && item">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <div class="btn-group">
                  <button class="btn btn-loading btn-success text-uppercase" ng-click="save();" ng-disabled="flags.http.saving || form.$invalid || (item.password && item.password !== rpassword)" type="button">
                    <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                    {t}Save{/t}
                  </button>
                  <button class="btn btn-success dropdown-toggle" data-toggle="dropdown" ng-disabled="flags.http.saving || form.$invalid || (item.password && item.password !== rpassword)" ng-if="item.id" type="button">
                    <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu no-padding pull-right" ng-if="item.id">
                    <li>
                      <a href="#" ng-click="convertTo('type', 2)" ng-if="item.type !== 2">
                        <i class="fa fa-level-up"></i>
                        {t}Convert to{/t} {t}subscriber{/t} + {t}user{/t}
                      </a>
                    </li>
                    <li class="divider" ng-if="item.type !== 2"></li>
                    <li>
                      <a href="#" ng-click="convertTo('type', 0)">
                        <i class="fa fa-retweet"></i>
                        {t}Convert to{/t} {t}user{/t}
                      </a>
                    </li>
                  </ul>
                </div>
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
          <a href="[% routing.generate('backend_subscribers_list') %]">
            <i class="fa fa-4x fa-warning text-warning"></i>
            <h3>{t}Unable to find the item{/t}</h3>
            <h4>{t}Click here to return to the list{/t}</h4>
          </a>
        </div>
      </div>
      <div class="ng-cloak" ng-show="!flags.http.loading && item">
        <div class="row">
          <div class="col-md-4 col-md-push-8">
            <div class="grid simple">
              <div class="grid-body no-padding">
                <div class="grid-collapse-title">
                  <div class="form-group no-margin">
                    <div class="checkbox">
                      <input id="activated" name="activated" ng-model="item.activated" ng-true-value="1" type="checkbox">
                      <label class="form-label" for="activated">
                        {t}Enabled{/t}
                      </label>
                    </div>
                  </div>
                </div>
                <div class="grid-collapse-title pointer" ng-class="{ 'open': expanded.subscriptions }" ng-click="expanded.subscriptions = !expanded.subscriptions">
                  <i class="fa fa-check-square-o m-r-10"></i>{t}Subscriptions{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.subscriptions }"></i>
                </div>
                <div class="grid-collapse-body no-padding ng-cloak" ng-class="{ 'expanded': expanded.subscriptions }">
                  <div class="p-l-15 p-t-15 p-b-15 p-r-15 b-t" ng-show="!data.extra.subscriptions || data.extra.subscriptions.length === 0">
                    <i class="fa fa-warning m-r-5 text-warning"></i>
                    {t escape=off}There are no <a href="[% routing.generate('backend_subscriptions_list') %]">subscriptions</a>{/t}
                  </div>
                  <div class="p-l-15 p-t-15 p-b-15 p-r-15 b-t" ng-repeat="subscription in data.extra.subscriptions">
                    <label class="form-label">
                      <span ng-class="{ 'text-danger': item.user_groups && item.user_groups[subscription.pk_user_group] && item.user_groups[subscription.pk_user_group].status === 2 }">
                        [% subscription.name %]
                        <span ng-if="item.user_groups && item.user_groups[subscription.pk_user_group] && item.user_groups[subscription.pk_user_group].status === 2">({t}Pending{/t})</span>
                      </span>
                    </label>
                    <div class="checkbox" ng-if="!item.user_groups || !item.user_groups[subscription.pk_user_group] || item.user_groups[subscription.pk_user_group].status !== 2">
                      <input id="checkbox-[% $index %]" ng-false-value="0" ng-model="item.user_groups[subscription.pk_user_group].status" ng-true-value="1" type="checkbox">
                      <label class="form-label" for="checkbox-[% $index %]">
                        {t}Subscribed{/t}
                      </label>
                    </div>
                    <div class="controls" ng-if="item.user_groups && item.user_groups[subscription.pk_user_group] && item.user_groups[subscription.pk_user_group].status === 2">
                      <p>{t}The subscriber has requested this subscription{/t}</p>
                      <div class="row">
                        <div class="col-lg-4 col-xs-6">
                          <button class="btn btn-block btn-success m-r-15" ng-click="accept(subscription.pk_user_group)">
                            <i class="fa fa-thumbs-up"></i>
                            {t}Accept{/t}
                          </button>
                        </div>
                        <div class="col-lg-4 col-xs-6">
                          <button class="btn btn-block btn-danger" ng-click="reject(subscription.pk_user_group)">
                            <i class="fa fa-thumbs-down"></i>
                            {t}Reject{/t}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-8 col-md-pull-4">
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
                          <button class="btn btn-block btn-white m-b-15" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="item.avatar_img_id" media-picker-type="photo">
                            <i class="fa fa-picture-o"></i>
                            {t}Change{/t}
                          </button>
                        </div>
                        <div class="col-lg-8 col-lg-offset-2 col-md-12 col-md-offset-0 col-xs-8 col-xs-offset-2">
                          <button class="btn btn-block btn-danger m-b-15" ng-click="item.avatar_img_id = null" ng-disabled="!item.avatar_img_id" type="button">
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
                    <div class="form-group" ng-class="{ 'has-error': form.name.$dirty && form.name.$invalid }">
                      <label class="control-label" for="name">{t}Name{/t}</label>
                        <div class="controls input-with-icon right">
                          <input class="form-control" id="name" name="name" ng-model="item.name" ng-maxlength="50" type="text"/>
                          <span class="icon right ng-cloak" ng-if="!flags.http.loading">
                            <span class="fa fa-check text-success" ng-if="form.name.$dirty && form.name.$valid"></span>
                            <span class="fa fa-info-circle text-info" ng-if="!form.name.$dirty && form.name.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                            <span class="fa fa-times text-error" ng-if="form.name.$dirty && form.name.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                          </span>
                        </div>
                      </div>
                      <div class="form-group" ng-class="{ 'has-error': form.email.$dirty && form.email.$invalid }">
                        <label class="control-label" for="email">{t}Email{/t}</label>
                        <div class="controls input-with-icon right">
                          <span class="icon right" ng-if="!flags.http.loading">
                            <span class="fa fa-check text-success" ng-if="form.email.$dirty && form.email.$valid"></span>
                            <span class="fa fa-info-circle text-info" ng-if="!form.email.$dirty && form.email.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                            <span class="fa fa-times text-error" ng-if="form.email.$dirty && form.email.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                          </span>
                          <input class="form-control" id="email" name="email" placeholder="johndoe@example.org"  ng-model="item.email" required type="email">
                        </div>
                      </div>
                      <div class="form-group" ng-class="{ 'has-error': form.password.$dirty && form.password.$invalid }">
                        <label class="control-label" for="password">{t}Password{/t}</label>
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
                        <label class="control-label" for="rpassword">{t}Confirm password{/t}</label>
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
            {acl isAllowed="USER_ADMIN"}
              <div class="grid simple ng-cloak" ng-if="data.extra.settings && data.extra.settings.fields && data.extra.settings.fields.length > 0">
                <div class="grid-title">
                  <h4>{t}Additional data{/t}</h4>
                </div>
                <div class="grid-body">
                  <div class="form-group" ng-repeat="field in data.extra.settings.fields">
                    <label class="form-label" for="[% field.name %]">[% field.title %]</label>
                    <div class="controls">
                      <input class="form-control" id="[% field.name %]" name="[% field.name %]" ng-if="field.type === 'text'" ng-model="item[field.name]" type="text">
                      <input class="form-control" datetime-picker id="[% field.name %]" name="[% field.name %]" ng-if="field.type === 'date'" ng-model="item[field.name]" type="text">
                      <select class="form-control" id="[% field.name %]" name="[% field.name %]" ng-if="field.type === 'country'" ng-model="item[field.name]">
                        <option value="">{t}Select a country{/t}...</option>
                        <option value="[% key %]" ng-repeat="(key,value) in extra.countries" ng-selected="[% item[field.name] === value %]">[% value %]</option>
                      </select>
                      <div class="radio" ng-if="field.type === 'options'" ng-repeat="option in field.values">
                        <input id="option-[% option.key %]" name="[% field.name %]" ng-model="item[field.name]" value="[% option.key %]" type="radio">
                        <label for="option-[% option.key %]">[% option.value %]</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            {/acl}
          </div>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="modal-convert">
      {include file="subscriber/modal.convert.tpl"}
    </script>
  </form>
{/block}
