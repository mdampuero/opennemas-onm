{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Polls{/t}
{/block}

{block name="ngInit"}
  ng-controller="PollListCtrl" ng-init="forcedLocale = '{$locale}'; init()"
{/block}

{block name="icon"}
  <i class="fa fa-pie-chart m-r-10"></i>
{/block}

{block name="title"}
  {t}Polls{/t}
{/block}

{block name="primaryActions"}
  <li class="hidden-xs quicklinks">
    <a class="btn btn-white" href="[% getExportUrl() %]">
      <span class="fa fa-download"></span>
      {t}Download{/t}
    </a>
  </li>
  {acl isAllowed="POLL_CREATE"}
    <li class="hidden-xs quicklinks">
      <span class="h-seperate"></span>
    </li>
    <li class="quicklinks">
      <a class="btn btn-loading btn-success text-uppercase" href="{url name=backend_poll_create}" title="{t}New poll{/t}">
        <i class="fa fa-plus m-r-5"></i>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="POLL_AVAILABLE"}
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="patchSelected('content_status', 0)" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-times fa-lg"></i>
      </button>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="patchSelected('content_status', 1)" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-check fa-lg"></i>
      </button>
    </li>
  {/acl}
  {acl isAllowed="POLL_DELETE"}
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
  <li class="quicklinks hidden-xs ng-cloak"  ng-init="categories = {json_encode($categories)|clear_json}">
    <onm-category-selector default-value-text="{t}Any{/t}" label-text="{t}Category{/t}" locale="config.locale.selected" ng-model="criteria.pk_fk_content_category" placeholder="{t}Any{/t}"></onm-category-selector>
  </li>
  <li class="quicklinks hidden-xs ng-cloak">
    {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
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
  {include file="poll/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.trash.tpl"}
  </script>
{/block}
