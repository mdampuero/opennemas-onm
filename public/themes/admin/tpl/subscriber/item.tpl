{extends file="base/admin.tpl"}

{block name="content"}
  <form name="subscriberForm" ng-controller="SubscriberCtrl" ng-init="init({$id})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="{url name="backend_subscribers_list"}">
                  <i class="fa fa-user"></i>
                  {t}Subscribers{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="!flags.loading && item">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="!flags.loading && item">
              <h5 class="ng-cloak">
                <span ng-if="item.id">{t}Edit{/t}</span>
                <span ng-if="!item.id">{t}Create{/t}</span>
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
                        <a href="#" ng-click="convertTo('type', 2)">
                          <i class="fa fa-level-up"></i>
                          {t}Convert to user + subscriber{/t}
                        </a>
                      </li>
                      <li class="divider"></li>
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
                <div class="form-group">
                  <label class="form-label">
                    {t}Subscriptions{/t}
                  </label>
                  <div class="controls">
                    <div ng-repeat="subscription in data.extra.subscriptions">
                      <div class="checkbox">
                        <input id="checkbox-[% $index %]" checklist-model="item.fk_user_group" checklist-value="subscription.pk_user_group.toString()" type="checkbox">
                        <label class="form-label" for="checkbox-[% $index %]">
                          [% subscription.name %]
                        </label>
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
