{extends file="common/extension/list.table.tpl"}

{block name="columns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
    <label for="checkbox-name">
      {t}Name{/t}
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
{/block}

{block name="columnsBody"}
  <td class="text-center v-align-middle">
    [% item.id %]
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('name')" title="[% item.name %]">
    <div class="table-text">
      [% item.name %]
    </div>
  </td>
{/block}
