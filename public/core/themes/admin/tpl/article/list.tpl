{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Articles{/t}
{/block}

{block name="ngInit"}
  ng-controller="ArticleListCtrl" ng-init="forcedLocale = '{$locale}'; init()"
{/block}

{block name="icon"}
  <i class="fa fa-file-text m-r-10"></i>
{/block}

{block name="title"}
  {t}Articles{/t}
{/block}

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      {acl isAllowed="MASTER"}
        <li class="quicklinks">
          <a class="btn btn-link" href="{url name=backend_articles_config}" class="admin_add" title="{t}Config article module{/t}">
            <span class="fa fa-cog fa-lg"></span>
          </a>
        </li>
        <li class="quicklinks"><span class="h-seperate"></span></li>
      {/acl}
      {acl isAllowed="ARTICLE_CREATE"}
        <li class="quicklinks">
            <a class="btn btn-success text-uppercase" href="{url name=backend_article_create}" title="{t}New article{/t}" id="create-button">
              <i class="fa fa-plus"></i>
              {t}Create{/t}
            </a>
        </li>
      {/acl}
    </ul>
  </div>
{/block}

{block name="selectedActions"}
  {acl isAllowed="ARTICLE_UPDATE"}
    <li class="quicklinks" ng-if="config.locale.multilanguage">
      <div class="dropdown">
        <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" uib-tooltip="{t}Translate selected{/t}" tooltip-placement="bottom">
          <i class="fa fa-globe fa-lg"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right no-padding" aria-labelledby="dropdownMenuButton">
          <li ng-repeat="(locale_key, locale_name) in data.extra.options.available" ng-show="locale_key != data.extra.locale" class="dropdown-item" ng-class="{ 'disabled': selectedItemsAreTranslatedTo(locale_key) }">
          <a href="#" ng-click="!selectedItemsAreTranslatedTo(locale_key) && translateSelected(locale_key)" >{t 1="[% locale_name %]"}Translate into %1{/t}</a>
          </li>
        </ul>
      </div>
    </li>
    <li class="quicklinks">
      <span class="h-seperate"></span>
    </li>
  {/acl}
  {acl isAllowed="ARTICLE_AVAILABLE"}
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="patchSelected('content_status', 1)" uib-tooltip="{t}Publish{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-check fa-lg"></i>
      </button>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" href="#" ng-click="patchSelected('content_status', 0)" uib-tooltip="{t}Unpublish{/t}" tooltip-placement="bottom">
        <i class="fa fa-times fa-lg"></i>
      </button>
    </li>
    <li class="quicklinks">
      <span class="h-seperate"></span>
    </li>
  {/acl}
  {acl isAllowed="ARTICLE_DELETE"}
    <li class="quicklinks">
      <a class="btn btn-link" href="#" ng-click="sendToTrash()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom">
        <i class="fa fa-trash-o fa-lg"></i>
      </a>
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
    <onm-category-selector ng-model="criteria.category_id" label-text="{t}Category{/t}" default-value-text="{t}Any{/t}" placeholder="{t}Any{/t}" />
  </li>
  <li class="hidden-xs ng-cloak m-r-10 quicklinks">
    {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
  </li>
  <li class="hidden-xs hidden-sm ng-cloak m-r-10 quicklinks">
    {include file="ui/component/select/author.tpl" blog="true" label="true" ngModel="criteria.fk_author"}
  </li>
{/block}

{block name="list"}
  {include file="article/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-delete-selected">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-update-selected">
    {include file="common/modals/_modalBatchUpdate.tpl"}
  </script>
  <script type="text/ng-template" id="modal-translate-selected">
    {include file="common/modals/_translate_selected.tpl"}
  </script>
{/block}
