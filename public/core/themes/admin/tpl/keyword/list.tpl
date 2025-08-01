{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Keywords{/t}
{/block}

{block name="ngInit"}
  ng-controller="KeywordListCtrl" ng-init="init()"
{/block}

{block name="icon"}
  <i class="fa fa-tags m-r-10"></i>
{/block}

{block name="title"}
  {t}Keywords{/t}
{/block}

{block name="primaryActions"}
  {acl isAllowed="KEYWORD_CREATE"}
    <li class="quicklinks">
      <a href="{url name=backend_keyword_create}" class="btn btn-success text-uppercase" title="{t}New keyword{/t}" id="create-button">
        <i class="fa fa-plus"></i>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="KEYWORD_DELETE"}
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
      <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.keyword }" name="name" ng-keyup="searchByKeypress($event)" ng-model="criteria.keyword" placeholder="{t}Search{/t}" type="text">
      <span class="input-group-addon input-group-addon-inside pointer ng-cloak no-animate" ng-click="clear('keyword')" ng-show="criteria.keyword">
        <i class="fa fa-times"></i>
      </span>
    </div>
  </li>
{/block}

{block name="list"}
  {include file="keyword/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="keyword/modals/_modalDelete.tpl"}
  </script>
{/block}
