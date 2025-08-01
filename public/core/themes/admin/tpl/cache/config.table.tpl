{extends file="common/extension/list.table.tpl"}

{block name="commonColumnsHeader"}
  <th class="v-align-middle">
    {t}Cache group{/t}
  </th>
  <th width="200">
    {t}Expire time{/t}
  </th>
{/block}

{block name="columns"}{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle">
    [% item.name %]
  </td>
  <td class="text-center v-align-middle">
    <input max="86400" min="0" ng-model="item.cache_lifetime" type="number">
  </td>
{/block}

{block name="customColumnsHeader"}
  <th class="text-center v-align-middle" width="150">
    {t}Enabled{/t}
  </th>
{/block}

{block name="customColumnsBody"}
  <td class="text-center v-align-middle">
    <button class="btn btn-white" ng-click="item.caching != 1 ? item.caching = 1 : item.caching = 0" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.cachingLoading == 1, 'fa-check text-success': !item.cachingLoading == 1 && item.caching == 1, 'fa-times text-danger': !item.cachingLoading == 1 && item.caching == 0 }"></i>
    </button>
  </td>
{/block}

