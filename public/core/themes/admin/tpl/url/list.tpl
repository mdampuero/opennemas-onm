{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > URLs
{/block}

{block name="ngInit"}
  ng-controller="UrlListCtrl" ng-init="init()"
{/block}

{block name="icon"}
  <i class="fa fa-globe m-r-10"></i>
{/block}

{block name="title"}
  URLs
{/block}

{block name="primaryActions"}
  {acl isAllowed=URL_CREATE}
    <li class="quicklinks">
      <a class="btn btn-success text-uppercase" href="[% routing.generate('backend_url_create') %]">
        <i class="fa fa-plus"></i>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="URL_AVAILABLE"}
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
  {acl isAllowed="URL_DELETE"}
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
        <span class="fa fa-search fa-lg"></span>
      </span>
      <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.source }" ng-model="criteria.source" placeholder="{t}Search{/t}" type="text">
      <span class="input-group-addon input-group-addon-inside pointer no-animate ng-cloak" ng-click="criteria.source = null" ng-show="criteria.source">
        <i class="fa fa-times"></i>
      </span>
    </div>
  </li>
  <li class="hidden-xs m-r-10 ng-cloak quicklinks" ng-init="type = [ { name: '{t}Any{/t}', value: null}, { name: '{t}Content{/t} {t}to{/t} {t}Content{/t}', value: 0}, { name: 'URI {t}to{/t} {t}Content{/t}', value: 1 }, { name: 'URI {t}to{/t} URI', value: 2 }, { name: 'Regex {t}to{/t} Content', value: 3 }, { name: 'Regex {t}to{/t} URI', value: 4 }, { name: 'Regex {t}to{/t} 410 GONE', value: 5 }, { name: 'URI {t}to{/t} 410 GONE', value: 6 } ]">
    <ui-select name="type" theme="select2" ng-model="criteria.type">
      <ui-select-match>
        <strong>{t}Type{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.value as item in type | filter: $select.search">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="hidden-xs m-r-10 ng-cloak quicklinks">
    <ui-select name="content_type" theme="select2" ng-model="criteria.content_type">
      <ui-select-match>
        <strong>{t}Content type{/t}:</strong> [% $select.selected.title %]
      </ui-select-match>
      <ui-select-choices repeat="item.name as item in addEmptyValue(data.extra.content_types, 'name', 'title') | filter: $select.search">
        <div ng-bind-html="item.title | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="hidden-xs m-r-10 ng-cloak quicklinks" ng-init="redirection = [ { name: '{t}Any{/t}', value: null}, { name: '{t}Yes{/t}', value: 1}, { name: '{t}No{/t}', value: 0 } ]">
    <ui-select name="redirection" theme="select2" ng-model="criteria.redirection">
      <ui-select-match>
        <strong>{t}Redirection{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.value as item in redirection | filter: $select.search">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
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
{/block}

{block name="list"}
  {include file="url/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
{/block}
