{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Letters{/t}
{/block}

{block name="ngInit"}
  ng-controller="LetterListCtrl" ng-init="forcedLocale = '{$locale}'; init()"
{/block}

{block name="icon"}
  <i class="fa fa-envelope m-r-10"></i>
{/block}

{block name="title"}
  {t}Letters{/t}
{/block}

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      {acl isAllowed="LETTER_CREATE"}
        <li class="quicklinks">
            <a class="btn btn-success text-uppercase" href="{url name=backend_letter_create}" title="{t}New letter{/t}" id="create-button">
              <i class="fa fa-plus"></i>
              {t}Create{/t}
            </a>
        </li>
      {/acl}
    </ul>
  </div>
{/block}

{block name="selectedActions"}
  {acl isAllowed="LETTER_AVAILABLE"}
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
  {acl isAllowed="LETTER_DELETE"}
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
  <li class="hidden-xs ng-cloak m-r-10 quicklinks">
    <ui-select ng-init="status = [ { name: '{t}Any{/t}', value: null }, { name: '{t}Published{/t}', value: 1 }, { name: '{t}No published{/t}', value: 0 }, { name: '{t}Pending{/t}', value: 2 } ]" ng-model="criteria.content_status" theme="select2">
      <ui-select-match>
        <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.value as item in status | filter: { name: $select.search }">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>

{/block}

{block name="list"}
  {include file="letter/list.table.tpl"}
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
{/block}

