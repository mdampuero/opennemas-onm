{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Global search{/t}
{/block}

{block name="ngInit"}
  ng-controller="GlobalSearchListCtrl" ng-init="init({json_encode($types)|clear_json})"
{/block}

{block name="icon"}
  <i class="fa fa-search m-r-10"></i>
{/block}

{block name="title"}
  {t}Global search{/t}
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
      </span>
    </div>
  </li>
  <li class="hidden-xs ng-cloak quicklinks" ng-init="type = {json_encode($types)|clear_json}">
    <ui-select name="type" theme="select2" ng-model="criteria.content_type_name">
      <ui-select-match>
        <strong>{t}Type{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.value as item in type  | filter: $select.search">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
{/block}

{block name="list"}
  {include file="search_advanced/list.table.tpl"}
{/block}
