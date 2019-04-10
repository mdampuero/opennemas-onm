{extends file="base/admin.tpl"}

{block name="content"}
<div ng-controller="VideoListCtrl" ng-init="forcedLocale = '{$locale}'; init()">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-film m-r-10"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>
              {t}Videos{/t}
            </h4>
          </li>
          <li class="quicklinks m-l-5 m-r-5 ng-cloak" ng-if="config.locale.multilanguage && config.locale.available">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
            <translator keys="data.extra.keys" ng-model="config.locale.selected" options="config.locale"></translator>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
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
              <a class="btn btn-success text-uppercase" href="{url name=backend_videos_create}" accesskey="N" tabindex="1" id="create-button">
                <span class="fa fa-plus m-r-5"></span>
                {t}Create{/t}
              </a>
            </li>
            {/acl}
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.items.length == 0 }">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section pull-left">
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="Clear selection" tooltip-placement="right"type="button">
              <i class="fa fa-arrow-left fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <h4>
              [% selected.items.length %] <span class="hidden-xs">{t}items selected{/t}</span>
            </h4>
          </li>
        </ul>
        <ul class="nav quick-section pull-right">
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
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
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
          {acl isAllowed="VIDEO_DELETE"}
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          {/acl}
          {/acl}
          {acl isAllowed="VIDEO_DELETE"}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="sendToTrash()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-trash-o fa-lg"></i>
            </button>
          </li>
          {/acl}
        </ul>
      </div>
    </div>
  </div>
  <div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <button class="btn btn-link ng-cloak" ng-click="setMode('list')" ng-if="mode === 'grid'" uib-tooltip="{t}List{/t}" tooltip-placement="right">
              <i class="fa fa-lg fa-list"></i>
            </button>
            <button class="btn btn-link" ng-click="setMode('grid')" ng-if="mode === 'list'" uib-tooltip="{t}Mosaic{/t}" tooltip-placement="right">
              <i class="fa fa-lg fa-th"></i>
            </button>
          </li>
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="no-boarder" name="title" ng-model="criteria.title" placeholder="{t}Search by title{/t}" type="text"/>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs ng-cloak">
          {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
          </li>
          <li class="quicklinks hidden-xs ng-cloak"  ng-init="categories = {json_encode($categories)|clear_json}">
            <onm-category-selector ng-model="criteria.pk_fk_content_category" label-text="{t}Category{/t}" default-value-text="{t}Any{/t}" placeholder="{t}Any{/t}" />
          </li>
          <li class="quicklinks hidden-xs ng-cloak" ng-show="mode === 'list'">
            {include file="ui/component/select/epp.tpl" label="true" ngModel="criteria.epp"}
          </li>
        </ul>
        <ul class="nav quick-section pull-right ng-cloak">
          <li class="quicklinks hidden-xs" ng-if="mode !== 'grid'">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="listing-no-contents ng-cloak" ng-show="flags.http.loading && !flags.loadingMore">
      <div class="text-center p-b-15 p-t-15">
        <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
        <h3 class="spinner-text">{t}Loading{/t}...</h3>
      </div>
    </div>
    <div class="listing-no-contents ng-cloak" ng-show="!flags.http.loading && items.length == 0">
      <div class="text-center p-b-15 p-t-15">
        <i class="fa fa-4x fa-warning text-warning"></i>
        <h3>{t}Unable to find any item that matches your search.{/t}</h3>
        <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
      </div>
    </div>
    {include file="video/partials/list_list.tpl"}

    {include file="video/partials/list_grid.tpl"}
  </div>

  <script type="text/ng-template" id="modal-delete">
    {include file="common/modals/modal.trash.tpl"}
  </script>

  <script type="text/ng-template" id="modal-delete-selected">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>

  <script type="text/ng-template" id="modal-update-selected">
    {include file="common/modals/_modalBatchUpdate.tpl"}
  </script>
</div>
{/block}
