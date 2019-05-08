{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  - {t}Albums{/t}
{/block}

{block name="ngInit"}
  ng-controller="AlbumListCtrl" ng-init="forcedLocale = '{$locale}'; init()"
{/block}

{block name="icon"}
  <i class="fa fa-stack-overflow m-r-10"></i>
  <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/745938-opennemas-c%C3%B3mo-crear-%C3%A1lbumes-galer%C3%ADas-de-imagene" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
    <i class="fa fa-question"></i>
  </a>
{/block}

{block name="title"}
  {t}Albums{/t}
{/block}

{block name="primaryActions"}
  {acl isAllowed="ALBUM_CREATE"}
    <li class="quicklinks">
      <a class="btn btn-loading btn-success text-uppercase" href="{url name=backend_album_create}">
        <span class="fa fa-plus m-r-5"></span>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="ALBUM_AVAILABLE"}
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
    <li class="quicklinks hidden-xs">
      <span class="h-seperate"></span>
    </li>
    <li class="quicklinks hidden-xs">
      <button class="btn btn-link" href="#" ng-click="patchSelected('favorite', 1)" uib-tooltip="{t escape="off"}Favorite{/t}" tooltip-placement="bottom">
        <i class="fa fa-star"></i>
      </button>
    </li>
    <li class="quicklinks hidden-xs">
      <button class="btn btn-link" href="#" ng-click="patchSelected('favorite', 0)" uib-tooltip="{t escape="off"}Unfavorite{/t}" tooltip-placement="bottom">
        <i class="fa fa-star"></i>
        <i class="fa fa-times fa-sub text-danger"></i>
      </button>
    </li>
  {/acl}
  {acl isAllowed="ALBUM_DELETE"}
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
  <li class="quicklinks ng-cloak" ng-if="!mode || mode === 'grid'" uib-tooltip="{t}List{/t}" tooltip-placement="bottom">
    <button class="btn btn-link" ng-click="setMode('list')">
      <i class="fa fa-lg fa-list"></i>
    </button>
  </li>
  <li class="quicklinks ng-cloak" ng-if="mode === 'list'" uib-tooltip="{t}Mosaic{/t}" tooltip-placement="bottom">
    <button class="btn btn-link" ng-click="setMode('grid')">
      <i class="fa fa-lg fa-th"></i>
    </button>
  </li>
  <li class="quicklinks">
    <span class="h-seperate"></span>
  </li>
  <li class="m-r-10 input-prepend inside search-input no-boarder">
    <span class="add-on">
      <span class="fa fa-search fa-lg"></span>
    </span>
    <input class="no-boarder" name="title" ng-model="criteria.title" placeholder="{t}Search by title{/t}" type="text"/>
  </li>
  <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
  <li class="quicklinks hidden-xs ng-cloak">
    <onm-category-selector ng-model="criteria.pk_fk_content_category" locale="config.locale.selected" label-text="{t}Category{/t}" default-value-text="{t}Any{/t}" placeholder="{t}Any{/t}" />
  </li>
  <li class="quicklinks hidden-xs ng-cloak">
    {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
  </li>
  <li class="quicklinks hidden-xs ng-cloak" ng-if="mode === 'list'">
    {include file="ui/component/select/epp.tpl" label="true" ngModel="criteria.epp"}
  </li>
{/block}

{block name="list"}
  {include file="album/partials/list_list.tpl"}
  {include file="album/partials/list_grid.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/modals/modal.trash.tpl"}
  </script>
  <script type="text/ng-template" id="modal-delete-selected">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-update-selected">
    {include file="common/modals/_modalBatchUpdate.tpl"}
  </script>
{/block}
