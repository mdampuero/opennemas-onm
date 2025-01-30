{extends file="common/extension/list.table.tpl"}

{block name="columns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
    <label for="checkbox-name">
      {t}Name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-service" checklist-model="columns.selected" checklist-value="'service'" type="checkbox">
    <label for="checkbox-service">
      {t}Service{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-default" checklist-model="columns.selected" checklist-value="'default'" type="checkbox">
    <label for="checkbox-default">
      {t}Default model{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-model" checklist-model="columns.selected" checklist-value="'model'" type="checkbox">
    <label for="checkbox-model">
      {t}Model{/t}
    </label>
  </div>

{/block}

{block name="columnsHeader"}
  <th class="pointer text-center" ng-click="sort('id')" width="50">
    {t}#{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc' }"></i>
  </th>
  <th class="pointer" ng-click="sort('name')" ng-show="isColumnEnabled('name')" width="250">
    {t}Name{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
  </th>
  <th class="text-center" ng-show="isColumnEnabled('service')">
    {t}Service{/t}
  </th>
  <th class="text-center" ng-show="isColumnEnabled('default')">
    {t}Default model{/t}
  </th>
  <th class="text-center" ng-show="isColumnEnabled('model')">
    {t}Model{/t}
  </th>
{/block}

{block name="columnsBody"}
  <td class="text-center v-align-middle">
    [% item.id %]
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('name')" title="[% item.name %]">
    <div class="table-text">
      [% item.name %]
    </div>
    <div class="listing-inline-actions">
      <a ng-if="item.domains.length > 1" class="btn btn-default btn-small" target="_blank" href="http://[% item.domains[item.main_domain - 1] %]/admin/openai/config/" ng-if="security.hasInstance(item.internal_name) && security.hasPermission('INSTANCE_UPDATE')" title="{t}Edit{/t}">
        <i class="fa fa-cog m-r-5"></i>
        {t}Configuration{/t}
      </a>
      <a ng-if="item.domains.length <= 1" class="btn btn-default btn-small" target="_blank" href="http://[% item.domains[item.main_domain] %]/admin/openai/config/" ng-if="security.hasInstance(item.internal_name) && security.hasPermission('INSTANCE_UPDATE')" title="{t}Edit{/t}">
        <i class="fa fa-cog m-r-5"></i>
        {t}Configuration{/t}
      </a>
    </div>
  </td>
  <td class="text-center v-align-middle" title="{t}Service{/t}" ng-show="isColumnEnabled('service')">
    <span class="badge text-capitalize" ng-class="{ 'badge-primary': item.ai_config.service === 'onmai', 'badge-danger': item.ai_config.service !== 'onmai' }" >
      [% item.ai_config.service %]
    </span>
  </td>
  <td class="text-center v-align-middle" title="{t}Default model{/t}" ng-show="isColumnEnabled('default')">
    <span class="badge badge-danger" ng-if="item.ai_config.model && item.ai_config.service === 'onmai'">
      {t}No{/t}
    </span>
    <span class="badge badge-success" ng-if="!item.ai_config.model && item.ai_config.service === 'onmai'">
      {t}Yes{/t}
    </span>
    <span class="badge" ng-if="item.ai_config.service !== 'onmai'">
      {t}N/A{/t}
    </span>
  </td>
  <td class="text-center v-align-middle" title="{t}Model{/t}" ng-show="isColumnEnabled('model')">
    <span class="badge badge-info" ng-if="item.ai_config.model">
      [% item.ai_config.model %]
    </span>
    <span class="badge" ng-if="!item.ai_config.model" >
      [% extra.model %]
    </span>
  </td>
{/block}
