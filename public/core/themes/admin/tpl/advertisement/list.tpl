{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
 > {t}Advertisement{/t}
{/block}

{block name="ngInit"}
ng-controller="AdvertisementListCtrl" ng-init="forcedLocale = '{$locale}'; init(); advertisement_positions = {json_encode($advertisement_positions)|clear_json}; type = {json_encode($types)|clear_json}; status = [{ name: '{t}All{/t}', value: null },{ name: '{t}Published{/t}', value: 1 },{ name: '{t}No published{/t}', value: 0 }];"
{/block}

{block name="icon"}
{/block}

{block name="title"}
  {t}Advertisement{/t}
{/block}


{block name="primaryActions"}
  {acl isAllowed="ADVERTISEMENT_SETTINGS"}
    <li class="quicklinks">
      <a class="btn btn-link" href="{url name=admin_ads_config}">
        <i class="fa fa-cog fa-lg"></i>
      </a>
    </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            {/acl}
            {acl isAllowed="MASTER"}
            <li class="quicklinks">
              <a class="btn btn-white" ng-click="export('api_v1_backend_datatransfer_export')" id="export-button">
                <i class="fa fa-download"></i>
                {t}Export{/t}
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <a class="btn btn-white" ng-click="import()" uib-tooltip="{t}Import{/t}" tooltip-placement="bottom" type="button">
                <i class="fa fa-upload"></i>
                {t}Import{/t}
              </a>
            </li>
    <li class="quicklinks">
      <span class="h-seperate"></span>
    </li>
  {/acl}
  <li class="quicklinks">
    <a class="btn btn-loading btn-success text-uppercase" href="{url name="admin_ad_create"}">
      <i class="fa fa-plus m-r-5"></i>
      {t}Create{/t}
    </a>
  </li>
{/block}

{block name="selectedActions"}
  {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
    <li class="quicklinks">
      <button class="btn btn-link"  ng-click="updateSelectedItems('content_status', 0)" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-times fa-lg"></i>
      </button>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="updateSelectedItems('content_status', 1)" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-check fa-lg"></i>
      </button>
    </li>
          {/acl}
          {acl isAllowed="MASTER"}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="exportSelectedItems('api_v1_backend_datatransfer_export_item')" uib-tooltip="{t}Export{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-download fa-lg"></i>
            </button>
          </li>
  {/acl}
  {acl isAllowed="ADVERTISEMENT_DELETE"}
    <li class="quicklinks">
      <span class="h-seperate"></span>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="sendToTrashSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
  <li class="ng-cloak m-r-10 quicklinks visible-lg">
    <onm-category-selector ng-model="criteria.fk_content_categories" label-text="{t}Category{/t}" default-value-text="{t}Any{/t}" placeholder="{t}Any{/t}" />
  </li>
  <li class="hidden-xs m-r-10 ng-cloak quicklinks">
    <ui-select name="position" theme="select2" ng-model="criteria.position">
      <ui-select-match>
        <strong>{t}Position{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.value as item in advertisement_positions | filter: { name: $select.search }">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="hidden-xs hidden-sm m-r-10 ng-cloak quicklinks">
    <ui-select name="type" theme="select2" ng-model="criteria.with_script">
      <ui-select-match>
        <strong>{t}Type{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.value as item in type | filter: { name: $select.search }">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="quicklinks ng-cloak">
    <ui-select name="status" theme="select2" ng-model="criteria.content_status">
      <ui-select-match>
        <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
      </ui-select-match>
      <ui-select-choices repeat="item.value as item in status | filter: { name: $select.search }">
        <div ng-bind-html="item.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </li>
  <li class="quicklinks hidden-xs ng-cloak">
    <button class="btn btn-link" ng-click="timeFilter()" uib-tooltip="{t}Time Range{/t}" tooltip-placement="bottom" type="button">
      <i class="fa fa-lg fa-clock-o m-l-5 m-r-5"></i>
    </button>
  </li>
  <li class="quicklinks hidden-xs ng-cloak">
    <button class="btn btn-link" ng-click="eraseFilters()" uib-tooltip="{t}Clean Filter{/t}" tooltip-placement="bottom" type="button">
      <i class="fa fa-lg fa-eraser m-l-5 m-r-5"></i>
    </button>
  </li>
{/block}

{block name="list"}
  {include file="advertisement/list.table.tpl"}
{/block}


{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/modals/_modalDelete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-delete-selected">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-update-selected">
    {include file="common/modals/_modalBatchUpdate.tpl"}
  </script>
  <script type="text/ng-template" id="modal-adblock">
    {include file="advertisement/modal.adblock.tpl"}
  </script>
  <script type="text/ng-template" id="modal-duplicate">
    {include file="common/extension/modal.duplicate.tpl"}
  </script>
  <script type="text/ng-template" id="modal-time-range">
    {include file="common/modals/_modalTimeRange.tpl"}
  </script>
  <script type="text/ng-template" id="ad_position_template">
    <div ng-repeat="position in content.positions">[% map[position].name %]</div>
  </script>
  <script type="text/ng-template" id="modal-datatransfer">
    {include file="common/extension/modal.datatransfer.tpl"}
  </script>
{/block}

{block name="footer-js" append}
  {javascripts}
    <script>
    jQuery(document).ready(function($) {
      $('#starttime, #endtime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        useCurrent: false,
      });
    });
    </script>
  {/javascripts}
{/block}
