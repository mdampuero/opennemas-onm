{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Albums{/t}
{/block}

{block name="ngInit"}
  ng-controller="AlbumListCtrl" ng-init="forcedLocale = '{$locale}'; init()"
{/block}

{block name="icon"}
  <i class="fa fa-camera m-r-10"></i>
  <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/745938-opennemas-c%C3%B3mo-crear-%C3%A1lbumes-galer%C3%ADas-de-imagene" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
    <i class="fa fa-question"></i>
  </a>
{/block}

{block name="title"}
  {t}Albums{/t}
{/block}

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      {acl isAllowed="MASTER"}
        <li class="quicklinks">
          <a class="btn btn-link" href="{url name=backend_albums_config}" class="admin_add" title="{t}Config album module{/t}">
            <span class="fa fa-cog fa-lg"></span>
          </a>
        </li>
        <li class="quicklinks"><span class="h-seperate"></span></li>
      {/acl}
      {acl isAllowed="ALBUM_CREATE"}
        <li class="quicklinks">
          <a class="btn btn-loading btn-success text-uppercase" href="{url name=backend_album_create}">
            <span class="fa fa-plus m-r-5"></span>
            {t}Create{/t}
          </a>
        </li>
      {/acl}
    </ul>
  </div>
{/block}

{block name="selectedActions"}
  {acl isAllowed="ALBUM_FAVORITE"}
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
  <li class="hidden-xs quicklinks">
    <div class="input-group input-group-animated">
      <span class="input-group-addon tag-input-icon">
        <i class="fa fa-tags fa-lg"></i>
      </span>
    </div>
  </li>
  <li>
    <onm-tags-input class="hidden-xs ng-cloak m-r-10 quicklinks" ng-model="criteria.tag" hide-generate="true" selection-only="true" generate-from="false" ignoreLocale="true" max-results="5" max-tags="1" filter="true" placeholder="{t}Search by tag{/t}"/>
  </li>
  <li class="quicklinks hidden-xs ng-cloak">
    <onm-category-selector default-value-text="{t}Any{/t}" label-text="{t}Category{/t}" locale="config.locale.selected" ng-model="criteria.category_id" placeholder="{t}Any{/t}"></onm-category-selector>
  </li>
  <li class="hidden-xs m-r-10 ng-cloak quicklinks">
    {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
  </li>
  <li class="hidden-xs ng-cloak quicklinks">
    {include file="ui/component/select/month_alt.tpl" ngModel="criteria.created" data="data.extra.years"}
  </li>
  <li class="hidden-xs hidden-sm ng-cloak m-r-10 quicklinks">
    {include file="ui/component/button/postponed.tpl"}
  </li>
{/block}

{block name="list"}
  {include file="album/list.table.tpl"}
  {include file="album/list.grid.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.trash.tpl"}
  </script>
{/block}
