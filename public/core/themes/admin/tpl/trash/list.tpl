{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Trash{/t}
{/block}

{block name="ngInit"}
  ng-controller="TrashListCtrl" ng-init="forcedLocale = '{$locale}'; init()"
{/block}

{block name="icon"}
  <i class="fa fa-trash-o m-r-10"></i>
{/block}

{block name="title"}
  {t}Trash{/t}
{/block}

{block name="primaryActions"}
  <li class="quicklinks">
    <button class="btn btn-danger btn-loading text-uppercase" ng-click="empty()" type="button">
      <i class="fa fa-trash-o m-r-5"></i>
      {t}Empty{/t}
    </button>
  </li>
{/block}

{block name="selectedActions"}
  {acl isAllowed="TRASH_ADMIN"}
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="restoreSelected()" uib-tooltip="{t}Restore{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-retweet fa-lg"></i>
      </button>
    </li>
    <li class="quicklinks">
      <span class="h-seperate"></span>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom">
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
  <li class="hidden-xs m-r-10 quicklinks">
    <select id="content_type_name" ng-model="criteria.content_type_name" data-label="<strong>{t}Content Type{/t}</strong>" class="select2">
      <option value="">{t}Any{/t}</option>
      {is_module_activated name="ARTICLE_MANAGER"}
        {acl isAllowed="ARTICLE_TRASH"}
          <option value="article">{t}Articles{/t}</option>
        {/acl}
      {/is_module_activated}
      {is_module_activated name="OPINION_MANAGER"}
        {acl isAllowed="OPINION_TRASH"}
          <option value="opinion">{t}Opinions{/t}</option>
        {/acl}{/is_module_activated}
      {is_module_activated name="OPINION_MANAGER"}
        {acl isAllowed="LETTER_TRASH"}
          <option value="letter">{t}Letters{/t}</option>
        {/acl}
      {/is_module_activated}
      {is_module_activated name="es.openhost.module.events"}
        {acl isAllowed="es.openhost.module.events"}
          <option value="event">{t}Events{/t}</option>
        {/acl}
      {/is_module_activated}
      {is_module_activated name="ADS_MANAGER"}
        {acl isAllowed="ADVERTISEMENT_TRASH"}
          <option value="advertisement">{t}Advertisements{/t}</option>
        {/acl}
      {/is_module_activated}
      {is_module_activated name="KIOSKO_MANAGER"}
        {acl isAllowed="KIOSKO_TRASH"}
          <option value="kiosko">{t}Covers{/t}</option>
        {/acl}
      {/is_module_activated}
      {is_module_activated name="ALBUM_MANAGER"}
        {acl isAllowed="ALBUM_TRASH"}
          <option value="album">{t}Albums{/t}</option>
        {/acl}
      {/is_module_activated}
      {is_module_activated name="IMAGE_MANAGER"}
        {acl isAllowed="PHOTO_TRASH"}
          <option value="photo">{t}Images{/t}</option>
        {/acl}
      {/is_module_activated}
      {is_module_activated name="VIDEO_MANAGER"}
        {acl isAllowed="VIDEO_TRASH"}
          <option value="video">{t}Videos{/t}</option>
        {/acl}
      {/is_module_activated}
      {is_module_activated name="FILE_MANAGER"}
        {acl isAllowed="FILE_DELETE"}
          <option value="attachment">{t}Files{/t}</option>
        {/acl}
      {/is_module_activated}
      {is_module_activated name="POLL_MANAGER"}
        {acl isAllowed="POLL_DELETE"}
          <option value="poll">{t}Polls{/t}</option>
        {/acl}
      {/is_module_activated}
      {is_module_activated name="SPECIAL_MANAGER"}
        {acl isAllowed="SPECIAL_DELETE"}
          <option value="special">{t}Specials{/t}</option>
        {/acl}
      {/is_module_activated}
      {is_module_activated name="STATIC_PAGES_MANAGER"}
        {acl isAllowed="STATIC_PAGE_DELETE"}
          <option value="static_page">{t}Static Pages{/t}</option>
        {/acl}
      {/is_module_activated}
      {is_module_activated name="WIDGET_MANAGER"}
        {acl isAllowed="WIDGET_DELETE"}
          <option value="widget">{t}Widgets{/t}</option>
        {/acl}
      {/is_module_activated}
    </select>
  </li>
  <li class="hidden-xs m-r-10 ng-cloak quicklinks">
    {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
  </li>
{/block}

{block name="list"}
  {include file="trash/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-empty">
    {include file="trash/modal.empty.tpl"}
  </script>
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-restore">
    {include file="trash/modal.restore.tpl"}
  </script>
{/block}
