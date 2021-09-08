{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Comments{/t}
{/block}

{block name="ngInit"}
  ng-controller="CommentListCtrl" ng-init="forcedLocale = '{$locale}'; init()"
{/block}

{block name="icon"}
  <i class="fa fa-comment m-r-10"></i>
{/block}

{block name="title"}
  {t}Comments{/t}
{/block}

{block name="primaryActions"}
  <li class="quicklinks">
    <a class="btn btn-link" href="{url name=backend_comments_config}" title="{t}Config comments module{/t}">
      <i class="fa fa-gear fa-lg"></i>
    </a>
  </li>
  <li class="hidden-xs quicklinks">
    <a class="btn btn-white" href="[% getExportUrl() %]">
      <span class="fa fa-download"></span>
      {t}Download{/t}
    </a>
  </li>
{/block}

{block name="selectedActions"}
  {acl isAllowed="COMMENT_AVAILABLE"}
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="patchSelected('status', 'rejected')" uib-tooltip="{t}Reject{/t}" tooltip-placement="bottom" type="button">
      <i class="fa fa-times fa-lg"></i>
      </button>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="patchSelected('status', 'accepted')" uib-tooltip="{t}Accept{/t}" tooltip-placement="bottom" type="button">
      <i class="fa fa-check fa-lg"></i>
      </button>
    </li>
  {/acl}
  {acl isAllowed="COMMENT_DELETE"}
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
      <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.body }" name="name" ng-keyup="searchByKeypress($event)" ng-model="criteria.body" placeholder="{t}Search{/t}" type="text">
      <span class="input-group-addon input-group-addon-inside pointer ng-cloak no-animate" ng-click="clear('body')" ng-show="criteria.body">
        <i class="fa fa-times"></i>
      </span>
    </div>
  </li>
  <li class="ng-cloak quicklinks" ng-init="extra.statuses = {json_encode($extra.statuses)|clear_json}">
    <ui-select name="status" theme="select2" ng-model="criteria.status" data-label="{t}Status{/t}">
      <ui-select-match>
        <strong>{t}Status{/t}:</strong> [% $select.selected.title %]
      </ui-select-match>
      <ui-select-choices repeat="status.value as status in extra.statuses | filter: $select.search">
        <div ng-bind-html="status.title | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
{/block}

{block name="list"}
  {include file="comment/partials/_list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="comment/modals/_modalDelete.tpl"}
  </script>
{/block}
