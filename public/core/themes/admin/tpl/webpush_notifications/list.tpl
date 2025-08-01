{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Web Push notifications{/t}
{/block}

{block name="ngInit"}
  ng-controller="WebPushNotificationsListCtrl" ng-init="forcedLocale = '{$locale}'; init()"
{/block}

{block name="icon"}
  <i class="fa fa-history m-r-10"></i>
{/block}

{block name="title"}
  {t}Web Push notifications History{/t}
{/block}

{block name="demo"}
  {if !in_array("es.openhost.module.webpush_notifications", $app.instance->activated_modules)}
      <div class="grid simple m-b-2">
        <div class="grid-body bg-transparent">
          <div class="bg-white onm-shadow p-15">
            <h2>{t}Improve your manager{/t}</h2>
              <p class="lead">{t}Contact with us to enjoy this feature.{/t}</p>
              <a class="btn btn-success btn-lg btn-lg-onm btn-block" href="mailto:sales@openhost.es" role="button" target="_blank">{t}I want this module{/t}</a>
          </div>
        </div>
      </div>
    {/if}
{/block}

{block name="filters"}
  <div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="m-r-10 quicklinks ng-cloak" ng-if="isModeSupported() && app.mode === 'grid'" uib-tooltip="{t}Mosaic{/t}" tooltip-placement="bottom">
            <button class="btn btn-link" ng-click="setMode('list')">
              <i class="fa fa-lg fa-th"></i>
            </button>
          </li>
          <li class="m-r-10 quicklinks ng-cloak" ng-if="isModeSupported() && app.mode === 'list'" uib-tooltip="{t}List{/t}" tooltip-placement="bottom">
            <button class="btn btn-link" ng-click="setMode('grid')">
              <i class="fa fa-lg fa-list"></i>
            </button>
          </li>
          {block name="leftFilters"}
            <li class="m-r-10 quicklinks">
              <div class="input-group input-group-animated">
                <span class="input-group-addon">
                  <i class="fa fa-search fa-lg"></i>
                </span>
                <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.title }" name="name" ng-keyup="searchByKeypress($event)" ng-model="criteria.title" placeholder="{t}Search{/t}" type="text">
                <span class="input-group-addon input-group-addon-inside pointer ng-cloak no-animate" ng-click="clear('title')" ng-show="criteria.title">
                  <i class="fa fa-times"></i>
                </span>
              </div>
            </li>
            <li class="hidden-xs ng-cloak m-r-10 quicklinks">
              {include file="ui/component/select/notification_status.tpl" label="true" ngModel="criteria.status"}
            </li>
            <li class="hidden-xs hidden-sm ng-cloak quicklinks">
              {include file="ui/component/select/month.tpl" ngModel="criteria.send_date" data="data.extra.years"}
            </li>
          {/block}
          <li class="quicklinks hidden-xs ng-cloak" ng-show="!isModeSupported() || app.mode === 'list'">
            <button class="btn btn-link" ng-click="list()" uib-tooltip="{t}Reload{/t} ({t}Notifications data is updated every 15 minutes{/t})" tooltip-placement="bottom" type="button">
              <i class="fa fa-lg fa-refresh m-l-5 m-r-5" ng-class="{ 'fa-spin': flags.http.loading }"></i>
            </button>
          </li>
        </ul>
        <ul class="nav quick-section quick-section-fixed ng-cloak" ng-if="data.items.length > 0">
          {block name="rightFilters"}
            <li class="quicklinks">
              <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total" hide-views="isModeSupported() && app.mode === 'grid'"></onm-pagination>
            </li>
          {/block}
        </ul>
      </div>
    </div>
  </div>
{/block}

{block name="list"}
  {include file="webpush_notifications/list.table.tpl"}
{/block}
