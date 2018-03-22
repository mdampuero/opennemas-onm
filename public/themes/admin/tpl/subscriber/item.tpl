{extends file="base/admin.tpl"}

{block name="content"}
  <form name="subscriberForm" ng-controller="SubscriberCtrl" ng-init="init({$id})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_subscribers_list') %]">
                  <i class="fa fa-address-card"></i>
                  {t}Subscribers{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="!flags.loading && item">
              <div class="p-l-10 p-r-10 p-t-10">
                <i class="fa fa-angle-right"></i>
              </div>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="!flags.loading && item">
              <h5 class="ng-cloak">
                <strong ng-if="item.id">{t}Edit{/t}</strong>
                <strong ng-if="!item.id">{t}Create{/t}</strong>
              </h5>
            </li>
          </ul>
          <div class="all-actions pull-right ng-cloak" ng-if="!flags.loading && item">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <div class="btn-group">
                  <button class="btn btn-loading btn-success text-uppercase" ng-click="save();" ng-disabled="flags.saving || subscriberForm.$invalid || (item.password && item.password !== rpassword)" type="button">
                    <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.saving }"></i>
                    {t}Save{/t}
                  </button>
                  {acl isAllowed=MASTER}
                    <button class="btn btn-success dropdown-toggle" data-toggle="dropdown" ng-disabled="flags.saving || subscriberForm.$invalid || (item.password && item.password !== rpassword)" ng-if="item.id" type="button">
                      <span class="caret"></span>
                    </button>
                  {/acl}
                  {acl isAllowed=MASTER}
                    <ul class="dropdown-menu no-padding pull-right" ng-if="item.id">
                      <li>
                        <a href="#" ng-click="convertTo('type', 2)" ng-if="item.type !== 2">
                          <i class="fa fa-level-up"></i>
                          {t}Convert to user + subscriber{/t}
                        </a>
                      </li>
                      <li class="divider" ng-if="item.type !== 2"></li>
                      <li>
                        <a href="#" ng-click="convertTo('type', 0)">
                          <i class="fa fa-retweet"></i>
                          {t}Convert to user{/t}
                        </a>
                      </li>
                    </ul>
                  {/acl}
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="listing-no-contents" ng-hide="!flags.loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!flags.loading && item === null">
        <div class="text-center p-b-15 p-t-15">
          <a href="[% routing.generate('backend_subscribers_list') %]">
            <i class="fa fa-4x fa-warning text-warning"></i>
            <h3>{t 1=$id}Unable to find any subscriber with id "%1".{/t}</h3>
            <h4>{t}Click here to return to the list of subscribers.{/t}</h4>
          </a>
        </div>
      </div>
      <div class="ng-cloak" ng-show="!flags.loading && item">
        <div class="row">
          <div class="col-sm-7">
            <div class="grid simple">
              <div class="grid-body">
                <div class="form-group">
                  <label class="control-label" for="name">{t}Name{/t}</label>
                  <div class="controls input-with-icon right">
                    <input class="form-control" id="name" name="name" ng-model="item.name" ng-maxlength="50" type="text"/>
                    <span class="icon right ng-cloak" ng-if="!flags.loading">
                      <span class="fa fa-check text-success" ng-if="subscriberForm.name.$dirty && subscriberForm.name.$valid"></span>
                      <span class="fa fa-info-circle text-info" ng-if="!subscriberForm.name.$dirty && subscriberForm.name.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                      <span class="fa fa-times text-error" ng-if="subscriberForm.name.$dirty && subscriberForm.name.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                    </span>
                  </div>
                </div>
                <div class="form-group" ng-class="{ 'has-error': subscriberForm.email.$dirty && subscriberForm.email.$invalid }">
                  <label class="control-label" for="email">{t}Email{/t}</label>
                  <div class="controls input-with-icon right">
                    <span class="icon right" ng-if="!flags.loading">
                      <span class="fa fa-check text-success" ng-if="subscriberForm.email.$dirty && subscriberForm.email.$valid"></span>
                      <span class="fa fa-info-circle text-info" ng-if="!subscriberForm.email.$dirty && subscriberForm.email.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                      <span class="fa fa-times text-error" ng-if="subscriberForm.email.$dirty && subscriberForm.email.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                    </span>
                    <input class="form-control" id="email" name="email" placeholder="johndoe@example.org"  ng-model="item.email" required type="email">
                  </div>
                </div>
                <div class="form-group" ng-class="{ 'has-error': subscriberForm.password.$dirty && subscriberForm.password.$invalid }">
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
                <div class="form-group" ng-class="{ 'has-error': subscriberForm.password.$valid && item.password && item.password !== rpassword }">
                  <label class="control-label" for="rpassword">{t}Confirm password{/t}</label>
                  <div class="controls">
                    <div class="input-group">
                      <span class="input-group-addon pointer" ng-click="rpasswordUnlocked = !rpasswordUnlocked">
                        <i class="fa fa-lock" ng-class="{ 'fa-unlock-alt': rpasswordUnlocked }"></i>
                      </span>
                      <input class="form-control" id="rpassword" id="rpassword" maxlength="20" ng-model="rpassword" maxlength="20" type="[% !rpasswordUnlocked ? 'password' : 'text' %]">
                    </div>
                    <span class="input-group-status">
                      <span class="fa fa-check text-success" ng-if="subscriberForm.password.$dirty && item.password === rpassword"></span>
                      <span class="fa fa-times text-error" ng-if="subscriberForm.password.$valid && item.password && item.password !== rpassword" uib-tooltip="{t}The passwords don't match{/t}"></span>
                    </span>
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
                    <label class="form-label" for="[% field.key %]">[% field.name %]</label>
                    <div class="controls">
                      <input class="form-control" id="[% field.key %]" name="[% field.key %]" ng-if="field.type === 'text'" ng-model="item[field.key]" type="text">
                      <input class="form-control" datetime-picker id="[% field.key %]" name="[% field.key %]" ng-if="field.type === 'date'" ng-model="item[field.key]" type="text">
                      <select class="form-control" id="[% field.key %]" name="[% field.key %]" ng-if="field.type === 'country'" ng-model="item[field.key]">
                        <option value="">{t}Select a country{/t}...</option>
                        <option value="[% key %]" ng-repeat="(key,value) in extra.countries" ng-selected="[% item[field.key] === value %]">[% value %]</option>
                      </select>
                      <div class="radio" ng-if="field.type === 'options'" ng-repeat="option in field.values">
                        <input id="option-[% option.key %]" name="[% field.key %]" ng-model="item[field.key]" value="[% option.key %]" type="radio">
                        <label for="option-[% option.key %]">[% option.value %]</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            {/acl}
          </div>
          <div class="col-sm-5">
            <div class="grid simple">
              <div class="grid-title">
                <h4>{t}Settings{/t}</h4>
              </div>
              <div class="grid-body">
                <div class="form-group">
                  <div class="checkbox">
                    <input id="activated" name="activated" ng-model="item.activated" ng-true-value="1" type="checkbox">
                    <label class="form-label" for="activated">
                      {t}Enabled{/t}
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="grid simple">
              <div class="grid-title">
                {t}Subscriptions{/t}
              </div>
              <div class="grid-body no-padding">
                <div class="p-l-15 p-t-15 p-b-15 p-r-15" ng-class="{ 'b-t': $index > 0 }" ng-repeat="subscription in data.extra.subscriptions">
                  <label class="form-label">
                    <span ng-class="{ 'text-danger': item.user_groups && item.user_groups[subscription.pk_user_group] && item.user_groups[subscription.pk_user_group].status === 2 }">
                      [% subscription.name %]
                      <span ng-if="item.user_groups && item.user_groups[subscription.pk_user_group] && item.user_groups[subscription.pk_user_group].status === 2">({t}Pending{/t})</span>
                    </span>
                  </label>
                  <div class="checkbox" ng-if="!item.user_groups || !item.user_groups[subscription.pk_user_group] || item.user_groups[subscription.pk_user_group].status !== 2">
                    <input id="checkbox-[% $index %]" ng-model="item.user_groups[subscription.pk_user_group].status" ng-true-value="1" type="checkbox">
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
      </div>
    </div>
    <script type="text/ng-template" id="modal-convert">
      {include file="subscriber/modal.convert.tpl"}
    </script>
  </form>
{/block}
