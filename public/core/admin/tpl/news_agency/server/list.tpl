{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}News Agency{/t} > {t}Servers{/t}
{/block}

{block name="ngInit"}
  ng-controller="NewsAgencyServerListCtrl" ng-init="init()"
{/block}

{block name="icon"}
  <i class="fa fa-microphone m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_news_agency_resource_list}">
    {t}News Agency{/t}
  </a>
{/block}

{block name="extraTitle"}
  <li class="quicklinks m-l-5 m-r-5">
    <h4>
      <i class="fa fa-angle-right"></i>
    </h4>
  </li>
  <li class="quicklinks">
    <h4>
      {t}Servers{/t}
    </h4>
  </li>
{/block}

{block name="primaryActions"}
  {acl isAllowed="IMPORT_NEWS_AGENCY_CONFIG"}
    <li class="quicklinks">
      <a class="btn btn-loading btn-success text-uppercase" href="{url name=backend_news_agency_server_create}">
        <i class="fa fa-plus m-r-5"></i>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="IMPORT_NEWS_AGENCY_CONFIG"}
    <li class="quicklinks hidden-xs">
      <button class="btn btn-link" href="#" ng-click="patchSelected('activated', 0)" uib-tooltip="{t}Disable{/t}" tooltip-placement="left">
        <i class="fa fa-times fa-lg"></i>
      </button>
    </li>
    <li class="quicklinks hidden-xs">
      <button class="btn btn-link" href="#" ng-click="patchSelected('activated', 1)" uib-tooltip="{t}Enable{/t}" tooltip-placement="left">
        <i class="fa fa-check fa-lg"></i>
      </button>
    </li>
    <li class="quicklinks">
      <span class="h-seperate"></span>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="left" type="button">
        <i class="fa fa-trash-o fa-lg"></i>
      </button>
    </li>
  {/acl}
{/block}

{block name="rightFilters"}
  <li class="quicklinks">
    <onm-pagination ng-model="criteria.page" readonly total-items="data.total"></onm-pagination>
  </li>
{/block}

{block name="list"}
  {include file="news_agency/server/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
{/block}
