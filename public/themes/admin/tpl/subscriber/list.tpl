{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Subscribers{/t}
{/block}

{block name="ngInit"}
  ng-controller="SubscriberListCtrl" ng-init="init();backup.master = {if $app.security->hasPermission('MASTER')}true{else}false{/if};backup.id = {$app.user->id}"
{/block}

{block name="icon"}
  <i class="fa fa-address-card m-r-10"></i>
{/block}

{block name="title"}
  {t}Subscribers{/t}
{/block}

{block name="primaryActions"}
  {acl isAllowed="SUBSCRIBER_SETTINGS"}
    <li class="quicklinks">
      <a class="btn btn-link" href="[% routing.generate('backend_subscribers_settings') %]" title="{t}Config users module{/t}">
        <i class="fa fa-cog fa-lg"></i>
      </a>
    </li>
  {/acl}
  <li class="quicklinks">
    <a class="btn btn-white" href="[% routing.generate('api_v1_backend_subscriber_get_report') %]">
      <span class="fa fa-download"></span>
      {t}Download{/t}
    </a>
  </li>
  <li class="quicklinks">
    <span class="h-seperate"></span>
  </li>
  {acl isAllowed=SUBSCRIBER_CREATE}
    <li class="quicklinks">
      <a class="btn btn-success text-uppercase" href="[% routing.generate('backend_subscriber_create') %]">
        <i class="fa fa-plus"></i>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="SUBSCRIBER_AVAILABLE"}
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="confirm('activated', 0)" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-times fa-lg"></i>
      </button>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="confirm('activated', 1)" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-check fa-lg"></i>
      </button>
    </li>
  {/acl}
  {acl isAllowed="SUBSCRIBER_DELETE"}
    <li class="quicklinks hidden-xs">
      <span class="h-seperate"></span>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom">
        <i class="fa fa-trash-o fa-lg"></i>
      </button>
    </li>
  {/acl}
{/block}

{block name="leftFilters"}
  <li class="m-r-10 quicklinks">
    <div class="input-group input-group-animated">
      <span class="input-group-addon">
        <i class="fa fa-search fa-lg"></i>
      </span>
      <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.name }" name="name" ng-keyup="searchByKeypress($event)" ng-model="criteria.name" placeholder="{t}Search{/t}" type="text">
      <span class="input-group-addon input-group-addon-inside pointer ng-cloak no-animate" ng-click="clear('name')" ng-show="criteria.name">
        <i class="fa fa-times"></i>
      </span>
    </div>
  </li>
  <li class="hidden-xs ng-cloak quicklinks text-left">
    <div class="checkbox p-t-7">
      <input id="status" ng-false-value="null" ng-model="criteria.status" ng-true-value="2" type="checkbox">
      <label for="status"><strong>{t}Requests only{/t}</strong></label>
    </div>
  </li>
  <li class="hidden-xs m-r-10 ng-cloak quicklinks">
    <ui-select name="group" theme="select2" ng-model="criteria.user_group_id">
      <ui-select-match>
        <strong>{t}Lists{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.pk_user_group as item in toArray(addEmptyValue(data.extra.subscriptions, 'pk_user_group')) | filter: $select.search">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="hidden-xs m-r-10 ng-cloak quicklinks" ng-init="activated = [ { name: '{t}Any{/t}', value: null}, { name: '{t}Enabled{/t}', value: 1}, { name: '{t}Disabled{/t}', value: 0 } ]">
    <ui-select name="activated" theme="select2" ng-model="criteria.activated">
      <ui-select-match>
        <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.value as item in activated  | filter: $select.search">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
{/block}

{block name="list"}
  {include file="subscriber/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-confirm">
    {include file="user/modal.confirm.tpl"}
  </script>
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
{/block}
