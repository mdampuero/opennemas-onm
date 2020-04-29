{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Videos{/t}
{/block}

{block name="ngInit"}
  ng-controller="VideoListCtrl" ng-init="forcedLocale = '{$locale}'; init()"
{/block}

{block name="icon"}
  <i class="fa fa-film m-r-10"></i>
{/block}

{block name="title"}
  {t}Videos{/t}
{/block}

{block name="primaryActions"}
  {acl isAllowed="VIDEO_SETTINGS"}
    <li class="quicklinks">
      <a class="btn btn-link" href="{url name=backend_videos_config}" class="admin_add" title="{t}Config video module{/t}">
        <span class="fa fa-cog fa-lg"></span>
      </a>
    </li>
    <li class="quicklinks hidden-xs">
      <span class="h-seperate"></span>
    </li>
  {/acl}
  {acl isAllowed="VIDEO_CREATE"}
    <li class="quicklinks">
      <a class="btn btn-loading btn-success text-uppercase" href="{url name=backend_videos_create}" accesskey="N" tabindex="1" id="create-button">
        <span class="fa fa-plus m-r-5"></span>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="VIDEO_FAVORITE"}
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
  {acl isAllowed="VIDEO_AVAILABLE"}
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
  {acl isAllowed="VIDEO_DELETE"}
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
  <li class="hidden-xs m-r-10 ng-cloak quicklinks">
    <onm-category-selector default-value-text="{t}Any{/t}" label-text="{t}Category{/t}" locale="config.locale.selected" ng-model="criteria.pk_fk_content_category" placeholder="{t}Any{/t}"></onm-category-selector>
  </li>
  <li class="hidden-xs m-r-10 ng-cloak quicklinks">
    {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
  </li>
{/block}

{block name="list"}
  {include file="video/list.table.tpl"}
  {include file="video/list.grid.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.trash.tpl"}
  </script>
{/block}
