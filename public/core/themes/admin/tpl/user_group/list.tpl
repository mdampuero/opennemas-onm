{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}User Groups{/t}
{/block}

{block name="ngInit"}
  ng-controller="UserGroupListCtrl" ng-init="init()"
{/block}

{block name="icon"}
  <i class="fa fa-users m-r-10"></i>
{/block}

{block name="title"}
  {t}User Groups{/t}
{/block}

{block name="primaryActions"}
  {acl isAllowed=GROUP_CREATE}
    <li class="quicklinks">
      <a class="btn btn-success text-uppercase" href="[% routing.generate('backend_user_group_create') %]">
        <i class="fa fa-plus"></i>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="GROUP_AVAILABLE"}
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
  {acl isAllowed="GROUP_DELETE"}
    <li class="quicklinks hidden-xs">
      <span class="h-seperate"></span>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" href="#" ng-click="deleteSelected('backend_ws_user_groups_delete')">
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
  <li class="hidden-xs m-r-10 ng-cloak quicklinks" ng-init="enabled = [ { name: '{t}Any{/t}', value: null}, { name: '{t}Enabled{/t}', value: 1}, { name: '{t}Disabled{/t}', value: 0 } ]">
    <ui-select name="enabled" theme="select2" ng-model="criteria.enabled">
      <ui-select-match>
        <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.value as item in enabled | filter: $select.search">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="hidden-xs m-r-10 ng-cloak quicklinks" ng-init="private = [ { name: '{t}Any{/t}', value: null}, { name: '{t}Private{/t}', value: 1}, { name: '{t}Public{/t}', value: 0 } ]">
    <ui-select name="private" theme="select2" ng-model="criteria.private">
      <ui-select-match>
        <strong>{t}Visibility{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.value as item in private | filter: $select.search">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
{/block}


{block name="list"}
  {include file="user_group/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
{/block}
