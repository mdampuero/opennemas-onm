{extends file="common/extension/list.tpl"}

{block name="icon"}
  <i class="fa fa-cubes m-r-10"></i>
{/block}

{block name="title"}
  {t}Instances{/t}
{/block}

{block name="primaryActions"}
  <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_REPORT')">
    <a class="btn btn-link" ng-href="{url name=manager_ws_instances_csv}?ids=[% selected.items.join(); %]&token=[% security.token %]">
      <i class="fa fa-download fa-lg"></i>
    </a>
  </li>
  <li class="quicklinks" ng-if="security.hasPermission('MASTER') || (security.hasPermission('INSTANCE_CREATE') && security.hasPermission('INSTANCE_REPORT') && security.instances.length < security.user.max_instances)">
    <span class="h-seperate"></span>
  </li>
  <li class="quicklinks" ng-if="security.hasPermission('MASTER') || (security.hasPermission('INSTANCE_CREATE') && security.instances.length < security.user.max_instances)">
    <a class="btn btn-success text-uppercase" ng-href="[% routing.ngGenerate('manager_instance_create') %]">
      <i class="fa fa-plus m-r-5"></i>
      {t}Create{/t}
    </a>
  </li>
{/block}

{block name="selectedActions"}
  <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_REPORT')">
    <a class="btn btn-link" ng-href="{url name=manager_ws_instances_csv}?ids=[% selected.instances.join(); %]&token=[% security.token %]" uib-tooltip="{t}Download CSV of selected{/t}" tooltip-placement="bottom">
      <i class="fa fa-download fa-lg text-white"></i>
    </a>
  </li>
  <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_REPORT') && (security.hasPermission('INSTANCE_UPDATE') || security.hasPermission('INSTANCE_DELETE'))">
    <span class="h-seperate"></span>
  </li>
  <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_UPDATE')">
    <button class="btn btn-link" ng-click="patchSelected('activated', 0)" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
      <i class="fa fa-times fa-lg"></i>
    </button>
  </li>
  <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_UPDATE')">
    <button class="btn btn-link" ng-click="patchSelected('activated', 1)" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
      <i class="fa fa-check fa-lg"></i>
    </button>
  </li>
  <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_UPDATE') && security.hasPermission('INSTANCE_DELETE')">
    <span class="h-seperate"></span>
  </li>
  <li class="quicklinks" ng-if="security.hasPermission('INSTANCE_DELETE')">
    <button class="btn btn-link" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
      <i class="fa fa-trash-o fa-lg"></i>
    </button>
  </li>
{/block}

{block name="leftFilters"}
  <li class="m-r-10 quicklinks">
    <div class="input-group input-group-animated">
      <span class="input-group-addon">
        <i class="fa fa-search fa-lg"></i>
      </span>
      <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.name }" name="name" ng-keyup="searchByKeypress($event)" ng-model="criteria.name" placeholder="{t}Search{/t}" type="text">
      <span class="input-group-addon input-group-addon-inside pointer no-animate" ng-click="clear('name')" ng-show="criteria.name">
        <i class="fa fa-times"></i>
      </span>
    </div>
  </li>
  <li class="m-r-10 quicklinks">
    <ui-select name="country" theme="select2" ng-model="criteria.country">
      <ui-select-match>
        <strong>{t}Country{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="country.id as country in extra.countries | filter: $select.search">
        <div ng-bind-html="country.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="m-r-10 quicklinks">
    <ui-select name="owner" theme="select2" ng-model="criteria.owner_id">
      <ui-select-match>
        <strong>{t}Owner{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="user.id as user in extra.users | filter: $select.search">
        <div ng-bind-html="user.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="m-r-10 quicklinks">
    <button class="btn btn-link" ng-click="resetFilters()" uib-tooltip="{t}Reset filters{/t}" tooltip-placement="bottom">
      <i class="fa fa-fire fa-lg m-l-5 m-r-5"></i>
    </button>
  </li>
  <li class="quicklinks">
    <button class="btn btn-link" ng-click="list()" uib-tooltip="{t}Reload{/t}" tooltip-placement="bottom" type="button">
      <i class="fa fa-lg fa-refresh m-l-5 m-r-5" ng-class="{ 'fa-spin': loading }"></i>
    </button>
  </li>
{/block}

{block name="list"}
  {include file="instance/list.table.tpl"}
{/block}
