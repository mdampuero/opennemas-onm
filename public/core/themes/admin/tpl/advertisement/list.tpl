{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
 > {t}Advertisement{/t}
{/block}

{block name="ngInit"}
ng-controller="AdvertisementListCtrl"
ng-init="init();
advertisement_positions = {json_encode($advertisement_positions)|clear_json};
type = {json_encode($types)|clear_json};
status = [
  { name: '{t}All{/t}', value: null },
  { name: '{t}Published{/t}', value: 1 },
  { name: '{t}No published{/t}', value: 0 }
];"
{/block}

{block name="icon"}

{/block}

{block name="title"}
  {t}Advertisement{/t}
{/block}


{block name="primaryActions"}
  {acl isAllowed="CATEGORY_CREATE"}
    <li class="quicklinks">
      <a class="btn btn-loading btn-success text-uppercase" href="{url name="admin_ad_create"}">
        <i class="fa fa-plus m-r-5"></i>
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
  <li class="quicklinks ng-cloak dropdown visible-lg">
    <div class="select2-container">
      <a class="select2-choice dropdown-toggle" type="button" data-toggle="dropdown">
        <strong>{t}Time Range{/t}</strong>
        [% criteria.starttime %]
        <span ng-if="criteria.starttime && criteria.endtime"> - </span>
        [% criteria.endtime %]
        <span class="caret"></span>
      </a>
      <ul class="ui-select-dropdown dropdown-menu p-10" ng-click="$event.stopPropagation()">
        <li>
          <div class="grid-collapse-body ng-cloak">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon add-on" onclick="document.getElementById('starttime').focus();">
                    <i class="fa fa-calendar m-r-5"></i> {t}Start date{/t}
                </span>
                <input class="input-min-45 input-300" type="datetime" id="starttime" autocomplete="off" name="starttime" datetime-picker ng-model="tempCriteria.starttime" />
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon add-on" onclick="document.getElementById('endtime').focus();">
                  <i class="fa fa-calendar m-r-5"></i> {t}End date{/t}
                </span>
                <input class="input-min-45 input-300" type="datetime" id="endtime" autocomplete="off" name="endtime" datetime-picker ng-model="tempCriteria.endtime" />
              </div>
            </div>
            <div class="form-group text-right m-b-0">
              <button class="btn btn-default pull-left" ng-click="cancelFilter()">{t}Cancel{/t}</button>
              <button class="btn btn-primary" ng-click="applyFilter()">{t}Confirm{/t}</button>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </li>
{/block}

{block name="list"}
  {include file="advertisement/list.table.tpl"}
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
