{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Menus{/t}
{/block}

{block name="ngInit"}
  ng-controller="MenuListCtrl" ng-init="forcedLocale = '{$locale}'; init()"
{/block}

{block name="icon"}
  <i class="fa fa-list-alt m-r-10"></i>
{/block}

{block name="title"}
  {t}Menus{/t}
{/block}

{block name="translator"}{/block}

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      {acl isAllowed="MENU_CREATE"}
        <li class="quicklinks">
            <a class="btn btn-success text-uppercase" href="{url name=backend_menu_create}" title="{t}New menu{/t}" id="create-button">
              <i class="fa fa-plus"></i>
              {t}Create{/t}
            </a>
        </li>
      {/acl}
    </ul>
  </div>
{/block}

{block name="selectedActions"}
  {acl isAllowed="MENU_DELETE"}
    <li class="quicklinks">
      <a class="btn btn-link" href="#" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom">
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
      <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.name }" name="name" ng-keyup="searchByKeypress($event)" ng-model="criteria.name" placeholder="{t}Search{/t}" type="text">
      <span class="input-group-addon input-group-addon-inside pointer ng-cloak no-animate" ng-click="clear('name')" ng-show="criteria.name">
        <i class="fa fa-times"></i>
      </span>
    </div>
  </li>
{/block}

{block name="list"}
  {include file="menus/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="photo/modals/modal.delete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-batch-remove-permanently">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>
{/block}
