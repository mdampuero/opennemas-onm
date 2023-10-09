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

{block name="list"}
  {include file="webpush_notifications/list.table.tpl"}
{/block}
