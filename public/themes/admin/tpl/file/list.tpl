{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Files{/t}
{/block}

{block name="ngInit"}
  ng-controller="FileListCtrl" ng-init="init()"
{/block}

{block name="icon"}
  <i class="fa fa-file-o m-r-10"></i>
{/block}

{block name="title"}
  {t}Files{/t}
{/block}

{block name="primaryActions"}
  {acl isAllowed="ATTACHMENT_CREATE"}
    <li class="quicklinks">
      <a class="btn btn-loading btn-success text-uppercase" href="{url name=backend_file_create}">
        <span class="fa fa-plus m-r-5"></span>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="FILE_FAVORITE"}
    <li class="quicklinks hidden-xs">
      <button class="btn btn-link" href="#" ng-click="patchSelected('favorite', 0)" uib-tooltip="{t escape="off"}Unfavorite{/t}" tooltip-placement="bottom">
        <i class="fa fa-star"></i>
        <i class="fa fa-times fa-sub text-danger"></i>
      </button>
    </li>
    <li class="quicklinks hidden-xs">
      <button class="btn btn-link" href="#" ng-click="patchSelected('favorite', 1)" uib-tooltip="{t escape="off"}Favorite{/t}" tooltip-placement="bottom">
        <i class="fa fa-star"></i>
      </button>
    </li>
    <li class="quicklinks hidden-xs">
      <span class="h-seperate"></span>
    </li>
  {/acl}
  {acl isAllowed="ATTACHMENT_AVAILABLE"}
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
  {acl isAllowed="ATTACHMENT_DELETE"}
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
  <li class="quicklinks hidden-xs ng-cloak"  ng-init="categories = {json_encode($categories)|clear_json}">
    <onm-category-selector ng-model="criteria.pk_fk_content_category" label-text="{t}Category{/t}" default-value-text="{t}Any{/t}" placeholder="{t}Any{/t}" />
  </li>
  <li class="quicklinks hidden-xs ng-cloak" ng-init="status = [ { name: '{t}Any{/t}', value: null }, { name: '{t}Published{/t}', value: 1 }, { name: '{t}No published{/t}', value: 0 } ]">
    <ui-select name="status" theme="select2" ng-model="criteria.content_status">
      <ui-select-match>
        <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.value as item in status | filter: { name: $select.search }">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="quicklinks hidden-xs ng-cloak">
    <ui-select name="view" theme="select2" ng-model="criteria.epp">
      <ui-select-match>
        <strong>{t}View{/t}:</strong> [% $select.selected %]
      </ui-select-match>
      <ui-select-choices repeat="item in views  | filter: $select.search">
        <div ng-bind-html="item | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
{/block}

{block name="list"}
  {include file="file/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/modals/modal.trash.tpl"}
  </script>
{/block}
