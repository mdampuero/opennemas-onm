{extends file="common/extension/list.table.tpl"}

{block name="columns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
    <label for="checkbox-name">
      {t}Name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-default" checklist-model="columns.selected" checklist-value="'default'" type="checkbox">
    <label for="checkbox-default">
      {t}Default setting{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-model" checklist-model="columns.selected" checklist-value="'model'" type="checkbox">
    <label for="checkbox-model">
      {t}Model{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-temperature" checklist-model="columns.selected" checklist-value="'temperature'" type="checkbox">
    <label for="checkbox-temperature">
      {t}Temperature{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-tokens" checklist-model="columns.selected" checklist-value="'tokens'" type="checkbox">
    <label for="checkbox-tokens">
      {t}Max tokens{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-frequency" checklist-model="columns.selected" checklist-value="'frequency'" type="checkbox">
    <label for="checkbox-frequency">
      {t}Frequency penalty{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-presence" checklist-model="columns.selected" checklist-value="'presence'" type="checkbox">
    <label for="checkbox-presence">
      {t}Presence penalty{/t}
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
  <th class="text-center" ng-show="isColumnEnabled('default')">
    {t}Default setting{/t}
  </th>
  <th class="text-center" ng-show="isColumnEnabled('model')">
    {t}Model{/t}
  </th>
  <th class="text-center" ng-show="isColumnEnabled('temperature')">
    {t}Temperature{/t}
  </th>
  <th class="text-center" ng-show="isColumnEnabled('tokens')">
    {t}Max tokens{/t}
  </th>
  <th class="text-center" ng-show="isColumnEnabled('frequency')">
    {t}Frequency penalty{/t}
  </th>
  <th class="text-center" ng-show="isColumnEnabled('presence')">
    {t}Presence penalty{/t}
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
  <td class="text-center v-align-middle" title="{t}Default setting{/t}" ng-show="isColumnEnabled('default')">
    <span class="badge badge-danger" ng-if="item.ai_config.setting.default != true">
      {t}No{/t}
    </span>
    <span class="badge badge-success" ng-if="item.ai_config.setting.default == true">
      {t}Yes{/t}
    </span>
  </td>
  <td class="text-center v-align-middle" title="{t}Model{/t}" ng-show="isColumnEnabled('model')">
    <span class="badge" ng-if="!item.ai_config.setting.model">
      [% extra.model %]
    </span>
    <span class="badge badge-info" ng-if="item.ai_config.setting.model" >
      [% item.ai_config.setting.model %]
    </span>
  </td>
  <td class="text-center v-align-middle" title="{t}Temperature{/t}" ng-show="isColumnEnabled('temperature')">
    <span class="badge" ng-if="!item.ai_config.setting.temperature">
      [% extra.temperature %]
    </span>
    <span class="badge badge-info" ng-if="item.ai_config.setting.temperature" >
      [% item.ai_config.setting.temperature %]
    </span>
  </td>
  <td class="text-center v-align-middle" title="{t}Max tokens{/t}" ng-show="isColumnEnabled('tokens')">
    <span class="badge" ng-if="!item.ai_config.setting.max_tokens">
      [% extra.max_tokens %]
    </span>
    <span class="badge badge-info" ng-if="item.ai_config.setting.max_tokens" >
      [% item.ai_config.setting.max_tokens %]
    </span>
  </td>
  <td class="text-center v-align-middle" title="{t}Frequency penalty{/t}" ng-show="isColumnEnabled('frequency')">
    <span class="badge" ng-if="!item.ai_config.setting.frequency_penalty">
      [% extra.frequency_penalty %]
    </span>
    <span class="badge badge-info" ng-if="item.ai_config.setting.frequency_penalty" >
      [% item.ai_config.setting.frequency_penalty %]
    </span>
  </td>
  <td class="text-center v-align-middle" title="{t}Presence penalty{/t}" ng-show="isColumnEnabled('presence')">
    <span class="badge" ng-if="!item.ai_config.setting.presence_penalty">
      [% extra.presence_penalty %]
    </span>
    <span class="badge badge-info" ng-if="item.ai_config.setting.presence_penalty" >
      [% item.ai_config.setting.presence_penalty %]
    </span>
  </td>
{/block}
