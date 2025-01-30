{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Prompts{/t}
{/block}

{block name="ngInit"}
  ng-controller="PromptListCtrl" ng-init="init()"
{/block}

{block name="icon"}
  <i class="fa fa-terminal m-r-10"></i>
{/block}

{block name="title"}
  {t}Prompts{/t}
{/block}

{block name="primaryActions"}
  {acl isAllowed="PROMPT_CREATE"}
    <li class="quicklinks">
      <a class="btn btn-loading btn-success text-uppercase" href="[% routing.generate('backend_onmai_prompt_create') %]">
        <i class="fa fa-plus m-r-5"></i>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="PROMPT_DELETE"}
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
  <li class="hidden-xs m-r-10 ng-cloak quicklinks" >
    <ui-select name="mode" ng-change="onChangeRouteList()" class="form-control" theme="select2" ng-model="routes.getList" search-enabled="false" required
      ng-init="options = [ { name: '{t}Own prompts{/t}', key: 'api_v1_backend_onmai_prompt_get_list'}, { name: '{t}System prompts{/t}', key: 'api_v1_backend_onmai_prompt_get_list_manager'} ]">
      <ui-select-match>
        [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.key as item in options | filter: $select.search">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>

{/block}

{block name="list"}
  {include file="prompt/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
{/block}
