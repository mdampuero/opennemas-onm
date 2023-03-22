{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Tags{/t}
{/block}

{block name="ngInit"}
  ng-controller="TagListCtrl" ng-init="init()"
{/block}

{block name="icon"}
  <i class="fa fa-tags m-r-10"></i>
{/block}

{block name="title"}
  {t}Tags{/t}
{/block}

{block name="primaryActions"}
  <li class="quicklinks">
    <a class="btn btn-link" href="{url name=backend_settings_tag}" title="{t}Config tag module{/t}">
      <span class="fa fa-cog fa-lg"></span>
    </a>
  </li>
  <li class="quicklinks">
    <span class="h-seperate"></span>
  </li>
  {acl isAllowed="TAG_CREATE"}
    <li class="quicklinks">
      <a class="btn btn-loading btn-success text-uppercase" href="[% routing.generate('backend_tag_create') %]">
        <i class="fa fa-plus m-r-5"></i>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="TAG_DELETE"}
    <li class="quicklinks">
      <button class="btn btn-link" href="#" ng-click="deleteSelected('backend_ws_tag_delete')">
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
      <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.name }" name="name" ng-keyup="searchByKeypress($event)" ng-model="criteria.name" placeholder="{t}Search{/t}" type="text">
      <span class="input-group-addon input-group-addon-inside pointer ng-cloak no-animate" ng-click="clear('name')" ng-show="criteria.name">
        <i class="fa fa-times"></i>
      </span>
    </div>
  </li>
  <li class="quicklinks hidden-xs ng-cloak" ng-if="config.locale.multilanguage">
    <ui-select name="language" theme="select2" ng-model="criteria.locale">
      <ui-select-match>
        <strong>{t}Language{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="locale.id as locale in config.locale.available | filter: { name: $select.search }">
        <div ng-bind-html="locale.name"></div>
      </ui-select-choices>
    </ui-select>
  </li>
{/block}

{block name="list"}
  {include file="tag/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
{/block}
