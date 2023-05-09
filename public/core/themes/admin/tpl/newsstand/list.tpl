{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Newsstand{/t}
{/block}

{block name="ngInit"}
  ng-controller="NewsstandListCtrl" ng-init="init()"
{/block}

{block name="icon"}
  <i class="fa fa-newspaper-o m-r-10"></i>
{/block}

{block name="title"}
  {t}Newsstand{/t}
{/block}

{block name="primaryActions"}
  {acl isAllowed="KIOSKO_CREATE"}
    <li class="quicklinks">
      <a class="btn btn-loading btn-success text-uppercase" href="{url name=backend_newsstand_create}">
        <i class="fa fa-plus m-r-5"></i>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="KIOSKO_AVAILABLE"}
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
  {acl isAllowed="KIOSKO_DELETE"}
    <li class="quicklinks">
      <span class="h-seperate"></span>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" href="#" ng-click="patchSelected('in_litter', 1)">
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
  <li class="hidden-xs m-r-10 ng-cloak quicklinks">
    <onm-category-selector ng-model="criteria.category_id" categories="categories" label-text="{t}Category{/t}" default-value-text="{t}Any{/t}" placeholder="{t}Any{/t}" required />
  </li>
  <li class="hidden-xs m-r-10 ng-cloak quicklinks">
    {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
  </li>
  <li class="hidden-xs hidden-sm ng-cloak quicklinks">
    {include file="ui/component/select/month_alt.tpl" ngModel="criteria.created" data="data.extra.years"}
  </li>
{/block}

{block name="list"}
  {include file="newsstand/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
{/block}
