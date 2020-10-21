{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Authors{/t}
{/block}

{block name="ngInit"}
  ng-controller="AuthorListCtrl" ng-init="init();backup.master = {if $app.security->hasPermission('MASTER')}true{else}false{/if};backup.id = {$app.user->id}"
{/block}

{block name="icon"}
  <i class="fa fa-edit m-r-10"></i>
  <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/566184-opennemas-gesti%C3%B3n-de-autores" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
    <i class="fa fa-question"></i>
  </a>
{/block}

{block name="title"}
  {t}Authors{/t}
{/block}

{block name="primaryActions"}
  {acl isAllowed="AUTHOR_CREATE"}
    <li class="quicklinks">
      <a class="btn btn-success text-uppercase" href="[% routing.generate('backend_author_create') %]">
        <i class="fa fa-plus"></i>
        {t}Create{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="AUTHOR_DELETE"}
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="deleteSelected(item.id)" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
{/block}

{block name="list"}
  {include file="author/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
{/block}
