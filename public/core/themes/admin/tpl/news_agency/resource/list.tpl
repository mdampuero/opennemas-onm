{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}News Agency{/t}
{/block}

{block name="ngInit"}
  ng-controller="NewsAgencyResourceListCtrl" ng-init="init();"
{/block}

{block name="icon"}
  <i class="fa fa-microphone m-r-10"></i>
  <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/788682-opennemas-agencias-de-noticias" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
    <i class="fa fa-question"></i>
  </a>
{/block}

{block name="title"}
  {t}News Agency{/t}
{/block}

{block name="primaryActions"}
  {acl isAllowed="IMPORT_NEWS_AGENCY_CONFIG"}
    <li class="quicklinks">
      <a class="btn btn-link" href="{url name=backend_news_agency_server_list}">
        <i class="fa fa-cog fa-lg"></i>
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  <li class="quicklinks">
    <a href="#" class="btn btn-link" ng-click="importList()" uib-tooltip="{t}Import{/t}" tooltip-placement="left">
      <i class="fa fa-cloud-download"></i>
    </a>
  </li>
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
  <li class="hidden-xs m-r-10 ng-cloak quicklinks">
    <ui-select name="source" theme="select2" ng-model="criteria.source">
      <ui-select-match>
        <strong>{t}Server{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.id as item in addEmptyValue(data.extra.servers, 'id', 'name') | filter: $select.search">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="hidden-xs m-r-10 ng-cloak quicklinks">
    <ui-select name="type" theme="select2" ng-model="criteria.type">
      <ui-select-match>
        <strong>{t}Type{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.value as item in data.extra.types | filter: $select.search">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
{/block}

{block name="list"}
  {include file="news_agency/resource/list.table.tpl"}
  {include file="news_agency/resource/list.grid.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-image">
    {include file="news_agency/resource/modal.image.tpl"}
  </script>
  <script type="text/ng-template" id="modal-import">
    {include file="news_agency/resource/modal.import.tpl"}
  </script>
  <script type="text/ng-template" id="modal-preview">
    {include file="news_agency/resource/modal.preview.tpl"}
  </script>
{/block}
