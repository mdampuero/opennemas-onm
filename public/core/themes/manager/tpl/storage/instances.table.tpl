{extends file="common/extension/list.table.tpl"}

{block name="columns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
    <label for="checkbox-name">
      {t}Name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-provider" checklist-model="columns.selected" checklist-value="'provider_type'" type="checkbox">
    <label for="checkbox-provider">
      {t}Provider{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-endpoint" checklist-model="columns.selected" checklist-value="'endpoint'" type="checkbox">
    <label for="checkbox-endpoint">
      {t}Upload endpoint{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-bucket" checklist-model="columns.selected" checklist-value="'bucket'" type="checkbox">
    <label for="checkbox-bucket">
      {t}Bucket{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-public_endpoint" checklist-model="columns.selected" checklist-value="'public_endpoint'" type="checkbox">
    <label for="checkbox-public_endpoint">
      {t}Download endpoint{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-manager_config" checklist-model="columns.selected" checklist-value="'manager_config'" type="checkbox">
    <label for="checkbox-manager_config">
      {t}Manager config{/t}
    </label>
  </div>
{/block}

{block name="columnsHeader"}
  <th class="pointer text-center" ng-click="sort('id')" width="50">
    {t}#{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc' }"></i>
  </th>
  <th class="pointer" ng-click="sort('name')" ng-show="isColumnEnabled('name')" >
    {t}Name{/t}
    <i
      ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
  </th>
  <th class="pointer" ng-show="isColumnEnabled('provider_type')" >
    {t}Provider{/t}
  </th>
  <th class="pointer" ng-show="isColumnEnabled('endpoint')" >
    {t}Upload endpoint{/t}
  </th>
  <th class="pointer" ng-show="isColumnEnabled('bucket')" >
    {t}Bucket{/t}
  </th>
  <th class="pointer" ng-show="isColumnEnabled('public_endpoint')" >
    {t}Download endpoint{/t}
  </th>
  <th class="pointer text-center" ng-show="isColumnEnabled('manager_config')">
    {t}Manager config{/t}
  </th>
{/block}

{block name="columnsBody"}
  <td class="text-center v-align-middle">
    [% item.id %]
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('name')" title="[% item.name %]">
    <div class="table-text">
      [% item.name %]
      <div class="listing-inline-actions">
        <a ng-click="openStorageSettings(item)" class="btn btn-default btn-small" title="{t}Edit{/t}">
          <i class="fa fa-pencil m-r-5"></i>
          {t}Config{/t}
        </a>
        <a ng-if="item.storage_settings && item.storage_settings.provider" ng-click="useManagerConfig(item)" class="btn btn-danger btn-small" title="{t}Use manager config{/t}">
          <i class="fa fa-wrench m-r-5"></i>
          {t}Use manager config{/t}
        </a>
      </div>
    </div>
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('provider_type')">
    <span class="badge badge-info" ng-if="isBunnyProvider(item.storage_settings.provider)">
      {t}Bunny Stream{/t}
    </span>
    <span class="badge badge-default" ng-if="isS3Provider(item.storage_settings.provider)">
      {t}S3 Provider{/t}
    </span>
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('endpoint')" title="[% getProviderField(item.storage_settings.provider, 'endpoint') %]">
    <div class="table-text">
      [% getProviderField(item.storage_settings.provider, 'endpoint') %]
    </div>
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('bucket')" title="[% getProviderField(item.storage_settings.provider, 'bucket') %]">
    <div class="table-text">
      [% getProviderField(item.storage_settings.provider, 'bucket') %]
    </div>
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('public_endpoint')" title="[% getProviderField(item.storage_settings.provider, 'public_endpoint') %]">
    <div class="table-text">
      [% getProviderField(item.storage_settings.provider, 'public_endpoint') %]
    </div>
  </td>
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('manager_config')" title="{t}Manager config{/t}">
    <span class="badge badge-success" ng-if="!(item.storage_settings && item.storage_settings.provider)">
      {t}Yes{/t}
    </span>
    <span class="badge badge-danger" ng-if="item.storage_settings && item.storage_settings.provider">
      {t}No{/t}
    </span>
  </td>
{/block}