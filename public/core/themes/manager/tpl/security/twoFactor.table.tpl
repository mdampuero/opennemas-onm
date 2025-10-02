{extends file="common/extension/list.table.tpl"}

{block name="columns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
    <label for="checkbox-name">
      {t}Name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-internal-name" checklist-model="columns.selected" checklist-value="'internal_name'" type="checkbox">
    <label for="checkbox-internal-name">
      {t}Internal name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-two-factor" checklist-model="columns.selected" checklist-value="'two_factor_enabled'" type="checkbox">
    <label for="checkbox-two-factor">
      {t}2FA{/t}
    </label>
  </div>
{/block}

{block name="columnsHeader"}
  <th class="pointer text-center" ng-click="sort('id')" width="50">
    {t}#{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc' }"></i>
  </th>
  <th class="pointer" ng-click="sort('name')" ng-show="isColumnEnabled('name')">
    {t}Name{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc' }"></i>
  </th>
  <th class="pointer" ng-click="sort('internal_name')" ng-show="isColumnEnabled('internal_name')">
    {t}Internal name{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('internal_name') == 'asc', 'fa fa-caret-down': isOrderedBy('internal_name') == 'desc' }"></i>
  </th>
  <th class="text-center" ng-show="isColumnEnabled('two_factor_enabled')" width="120">
    {t}2FA{/t}
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
        <button class="btn btn-danger btn-small" ng-click="deleteSession(item)" ng-disabled="item.deleteSessionLoading || bulkDeleteSessionLoading" type="button">
          <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.deleteSessionLoading, 'fa-trash': !item.deleteSessionLoading }"></i>
          <span class="m-l-5">{t}Borrar sesión{/t}</span>
        </button>
        <button class="btn btn-default btn-small" ng-click="toggleTwoFactor(item)" ng-disabled="item.twoFactorLoading" type="button">
          <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.twoFactorLoading, 'fa-unlock-alt text-success': !item.twoFactorLoading && !item.two_factor_enabled, 'fa-lock text-error': !item.twoFactorLoading && item.two_factor_enabled }"></i>
          <span class="m-l-5" ng-if="!item.twoFactorLoading && item.two_factor_enabled">{t}Desactivar 2FA{/t}</span>
          <span class="m-l-5" ng-if="!item.twoFactorLoading && !item.two_factor_enabled">{t}Activar 2FA{/t}</span>
          <span class="m-l-5" ng-if="item.twoFactorLoading">{t}Procesando{/t}</span>
        </button>
      </div>
    </div>
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('internal_name')" title="[% item.internal_name %]">
    <div class="table-text">
      [% item.internal_name %]
    </div>
  </td>
  
  <td class="text-center v-align-middle" ng-show="isColumnEnabled('two_factor_enabled')" title="{t}Two-factor authentication{/t}">
    <span class="badge badge-success" style="font-size:0.8rem" ng-if="item.two_factor_enabled">
      {t}Yes{/t}
    </span>
    <span class="badge badge-danger" style="font-size:0.8rem" ng-if="!item.two_factor_enabled">
      {t}No{/t}
    </span>
  </td>
{/block}