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
  <li class="hidden-xs m-r-10 ng-cloak quicklinks" ng-init="types = [ { id: null, name: '{t}Any{/t}' }, { id: 'intelligentwidget', name: '{t}IntelligentWidget{/t}' }, { id: 'html', name: '{t}HTML{/t}' }]">
    <ui-select name="widget_type" theme="select2" ng-model="criteria.widget_type">
      <ui-select-match>
        <strong>{t}Type{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.id as item in types | filter: { name: $select.search }">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="quicklinks hidden-xs ng-cloak">
    <ui-select name="class" theme="select2" ng-model="criteria.class">
      <ui-select-match>
        <strong>{t}Content{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.id as item in addEmptyValue(data.extra.class, 'id', 'name') | filter: { name: $select.search }">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="quicklinks hidden-xs ng-cloak">
    {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
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
