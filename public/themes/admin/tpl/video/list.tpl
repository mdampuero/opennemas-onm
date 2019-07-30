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
  <li class="quicklinks ng-cloak" ng-if="app.mode === 'grid'" uib-tooltip="{t}Mosaic{/t}" tooltip-placement="bottom">
    <button class="btn btn-link" ng-click="setMode('list')">
      <i class="fa fa-lg fa-th"></i>
    </button>
  </li>
  <li class="quicklinks ng-cloak" ng-if="app.mode === 'list'" uib-tooltip="{t}List{/t}" tooltip-placement="bottom">
    <button class="btn btn-link" ng-click="setMode('grid')">
      <i class="fa fa-lg fa-list"></i>
    </button>
  </li>
  <li class="quicklinks">
    <span class="h-seperate"></span>
  </li>
  <li class="input-prepend inside ng-cloak search-input no-boarder">
    <span class="add-on">
      <span class="fa fa-search fa-lg"></span>
    </span>
    <input class="no-boarder" name="title" ng-model="criteria.title" placeholder="{t}Search by title{/t}" type="text"/>
  </li>
  <li class="quicklinks hidden-xs">
    <span class="h-seperate"></span>
  </li>
  <li class="quicklinks hidden-xs ng-cloak">
    <onm-category-selector default-value-text="{t}Any{/t}" label-text="{t}Category{/t}" locale="config.locale.selected" ng-model="criteria.pk_fk_content_category" placeholder="{t}Any{/t}"></onm-category-selector>
  </li>
  <li class="quicklinks hidden-xs ng-cloak">
    {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
  </li>
  <li class="quicklinks hidden-xs ng-cloak" ng-show="app.mode === 'list'">
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
  {include file="video/list.table.tpl"}
  {include file="video/list.grid.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.trash.tpl"}
  </script>
{/block}
