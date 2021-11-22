{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Opinions{/t}
{/block}

{block name="ngInit"}
  ng-controller="OpinionListCtrl" ng-init="forcedLocale = '{$locale}'; init()"
{/block}

{block name="icon"}
  <i class="fa fa-quote-right m-r-10"></i>
{/block}

{block name="title"}
  {t}Opinions{/t}
{/block}

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      {acl isAllowed="OPINION_SETTINGS"}
        <li class="quicklinks">
          <a class="btn btn-link" href="{url name=backend_opinions_config}" title="{t}Config opinion module{/t}">
            <i class="fa fa-cog fa-lg"></i>
          </a>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
      {/acl}
      {acl isAllowed="OPINION_CREATE"}
        <li class="quicklinks">
          <a class="btn btn-success text-uppercase" href="{url name=backend_opinion_create}" title="{t}New opinion{/t}" id="create-button">
            <i class="fa fa-plus"></i>
            {t}Create{/t}
          </a>
        </li>
      {/acl}
    </ul>
  </div>
{/block}

{block name="selectedActions"}
  {acl isAllowed="CONTENT_OTHER_UPDATE"}
    {acl isAllowed="OPINION_AVAILABLE"}
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
    {acl isAllowed="OPINION_HOME"}
      <li class="quicklinks hidden-xs">
        <a class="btn btn-link" href="#" ng-click="patchSelected('in_home', 1)" uib-tooltip="{t escape="off"}In home{/t}" tooltip-placement="bottom">
          <i class="fa fa-home fa-lg"></i>
        </a>
      </li>
      <li class="quicklinks hidden-xs">
        <a class="btn btn-link" href="#" ng-click="patchSelected('in_home', 0)" uib-tooltip="{t escape="off"}Drop from home{/t}" tooltip-placement="bottom">
          <i class="fa fa-home fa-lg"></i>
          <i class="fa fa-times fa-sub text-danger"></i>
        </a>
      </li>
      <li class="quicklinks hidden-xs">
        <span class="h-seperate"></span>
      </li>
    {/acl}
  {/acl}
  {acl isAllowed="OPINION_DELETE"}
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
    <li class="hidden-xs m-r-10 ng-cloak quicklinks">
      {include file="ui/component/select/opinion_blog.tpl" label="true" ngModel="criteria.blog"}
    </li>
    <li class="hidden-xs ng-cloak m-r-10 quicklinks">
      {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
    </li>
    <li class="hidden-xs hidden-sm ng-cloak m-r-10 quicklinks">
      {include file="ui/component/select/author.tpl" blog="true" label="true" ngModel="criteria.fk_author"}
    </li>
{/block}

{block name="list"}
  {include file="opinion/list.table.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.trash.tpl"}
  </script>
  <script type="text/ng-template" id="modal-delete-selected">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-update-selected">
    {include file="common/modals/_modalBatchUpdate.tpl"}
  </script>
{/block}
