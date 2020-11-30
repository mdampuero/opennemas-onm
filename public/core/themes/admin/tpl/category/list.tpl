{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Categories{/t}
{/block}

{block name="ngInit"}
  ng-controller="CategoryListCtrl" ng-init="forcedLocale = '{$locale}'; init()"
{/block}

{block name="icon"}
  <i class="fa fa-bookmark m-r-10"></i>
{/block}

{block name="title"}
  {t}Categories{/t}
{/block}

{block name="primaryActions"}
  {acl isAllowed="CATEGORY_CREATE"}
    <li class="quicklinks">
      <a class="btn btn-loading btn-success text-uppercase" href="[% routing.generate('backend_category_create') %]">
        <i class="fa fa-plus m-r-5"></i>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="MASTER"}
    <li class="quicklinks" ng-if="selected.items.length < items.length && areSelectedNotEmpty()">
      <button class="btn btn-link" ng-click="moveSelected()" uib-tooltip="{t}Move contents{/t}" tooltip-placement="bottom">
        <i class="fa fa-flip-horizontal fa-reply fa-lg"></i>
      </button>
    </li>
    <li class="quicklinks hidden-xs" ng-if="selected.items.length < items.length && areSelectedNotEmpty()">
      <span class="h-seperate"></span>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="emptySelected()" uib-tooltip="{t}Delete contents{/t}" tooltip-placement="bottom">
        <i class="fa fa-fire fa-lg"></i>
      </button>
    </li>
  {/acl}
  <li class="quicklinks hidden-xs">
    <span class="h-seperate"></span>
  </li>
  {acl isAllowed="CATEGORY_AVAILABLE"}
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="patchSelected('enabled', 0)" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-times fa-lg"></i>
      </button>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="patchSelected('enabled', 1)" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-check fa-lg"></i>
      </button>
    </li>
  {/acl}
  {acl isAllowed="CATEGORY_DELETE"}
    <li class="quicklinks hidden-xs" ng-if="areSelectedEmpty()">
      <span class="h-seperate"></span>
    </li>
    <li class="quicklinks" ng-if="areSelectedEmpty()">
      <button class="btn btn-link" ng-click="deleteSelected('api_v1_backend_categories_delete')" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom">
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
  <li class="hidden-xs m-r-10 ng-cloak quicklinks" ng-init="activated = [ { name: '{t}Any{/t}', value: null}, { name: '{t}Enabled{/t}', value: 1}, { name: '{t}Disabled{/t}', value: 0 } ]">
    <ui-select name="activated" theme="select2" ng-model="criteria.enabled">
      <ui-select-match>
        <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.value as item in activated | filter: $select.search">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
{/block}

{block name="rightFilters"}
  <li class="quicklinks">
    <onm-pagination ng-model="criteria.page" readonly total-items="data.total"></onm-pagination>
  </li>
{/block}

{block name="list"}
  {include file="category/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-empty">
    {include file="category/modal.empty.tpl"}
  </script>
  <script type="text/ng-template" id="modal-move">
    {include file="category/modal.move.tpl"}
  </script>
{/block}
