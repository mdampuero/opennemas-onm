{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Widgets{/t}
{/block}

{block name="ngInit"}
  ng-controller="WidgetListCtrl" ng-init="init()"
{/block}

{block name="icon"}
  <i class="fa fa-puzzle-piece m-r-10"></i>
{/block}

{block name="title"}
  {t}Widgets{/t}
{/block}

{block name="primaryActions"}
  {acl isAllowed="WIDGET_CREATE"}
    <li class="quicklinks">
      <a class="btn btn-loading btn-success text-uppercase" href="{url name=backend_widget_create}">
        <span class="fa fa-plus m-r-5"></span>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="WIDGET_AVAILABLE"}
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="patchSelected('content_status', 0)" uib-tooltip="{t}Unpublish{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-times fa-lg"></i>
      </button>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="patchSelected('content_status', 1)" uib-tooltip="{t}Publish{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-check fa-lg"></i>
      </button>
    </li>
  {/acl}
  {acl isAllowed="WIDGET_DELETE"}
    <li class="quicklinks">
      <span class="h-seperate"></span>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="sendToTrash()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-trash-o fa-lg"></i>
      </button>
    </li>
  {/acl}
{/block}

{block name="leftFilters"}
  <li class="m-r-10 input-prepend inside search-input no-boarder">
    <span class="add-on">
      <span class="fa fa-search fa-lg"></span>
    </span>
    <input class="no-boarder" name="title" ng-model="criteria.title" placeholder="{t}Search by title{/t}" type="text"/>
  </li>
  <li class="quicklinks hidden-xs">
    <span class="h-seperate"></span>
  </li>
  <li class="quicklinks hidden-xs ng-cloak" ng-init="type = [ { id: null, name: '{t}Any{/t}' }, { id: 'intelligentwidget', name: '{t}IntelligentWidget{/t}' }, { id: 'html', name: '{t}HTML{/t}' }]">
    <ui-select name="renderlet" theme="select2" ng-model="criteria.renderlet">
      <ui-select-match>
        <strong>{t}Type{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.id as item in type | filter: { name: $select.search }">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="quicklinks hidden-xs ng-cloak">
    <ui-select name="content" theme="select2" ng-model="criteria.content">
      <ui-select-match>
        <strong>{t}Content{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.id as item in addEmptyValue(data.extra.types, 'id', 'name') | filter: { name: $select.search }">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="quicklinks hidden-xs ng-cloak">
    {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
  </li>
  <li class="quicklinks hidden-xs ng-cloak" ng-show="!isModeSupported() || app.mode === 'list'">
    {include file="ui/component/select/epp.tpl" label="true" ngModel="criteria.epp"}
  </li>
  <li class="quicklinks hidden-xs ng-cloak" ng-show="!isModeSupported() || app.mode === 'list'">
    <span class="h-seperate"></span>
  </li>
  <li class="quicklinks hidden-xs ng-cloak" ng-show="!isModeSupported() || app.mode === 'list'">
    <button class="btn btn-link" ng-click="list()" uib-tooltip="{t}Reload{/t}" tooltip-placement="bottom" type="button">
      <i class="fa fa-lg fa-refresh" ng-class="{ 'fa-spin': flags.http.loading }"></i>
    </button>
  </li>
{/block}

{block name="list"}
  {include file="widget/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
{/block}
