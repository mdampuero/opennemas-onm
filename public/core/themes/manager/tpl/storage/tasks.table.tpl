{extends file="common/extension/list.table.tpl"}

{block name="columns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
    <label for="checkbox-name">
      {t}Name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-command" checklist-model="columns.selected" checklist-value="'command'" type="checkbox">
    <label for="checkbox-command">
      {t}Command{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-params" checklist-model="columns.selected" checklist-value="'params'" type="checkbox">
    <label for="checkbox-params">
      {t}Params{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-instance" checklist-model="columns.selected" checklist-value="'instance_id'" type="checkbox">
    <label for="checkbox-instance">
      {t}Instance{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-status" checklist-model="columns.selected" checklist-value="'status'" type="checkbox">
    <label for="checkbox-status">
      {t}Status{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-created" checklist-model="columns.selected" checklist-value="'created'" type="checkbox">
    <label for="checkbox-created">
      {t}Created{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-updated" checklist-model="columns.selected" checklist-value="'updated'" type="checkbox">
    <label for="checkbox-updated">
      {t}Updated{/t}
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
  <th class="pointer" ng-click="sort('command')" ng-show="isColumnEnabled('command')" width="250">
    {t}Command{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('command') == 'asc', 'fa fa-caret-down': isOrderedBy('command') == 'desc'}"></i>
  </th>
  <th class="pointer" ng-click="sort('params')" ng-show="isColumnEnabled('params')" width="250">
    {t}Params{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('params') == 'asc', 'fa fa-caret-down': isOrderedBy('params') == 'desc'}"></i>
  </th>
  <th class="pointer" ng-click="sort('instance_id')" ng-show="isColumnEnabled('instance_id')" width="250">
    {t}Instance{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('instance_id') == 'asc', 'fa fa-caret-down': isOrderedBy('instance_id') == 'desc'}"></i>
  </th>
  <th class="pointer text-center" ng-click="sort('status')" ng-show="isColumnEnabled('status')" width="250">
    {t}Status{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('status') == 'asc', 'fa fa-caret-down': isOrderedBy('status') == 'desc'}"></i>
  </th>
  <th class="pointer text-center" ng-click="sort('created')" ng-show="isColumnEnabled('created')" width="250">
    {t}Created{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('created') == 'asc', 'fa fa-caret-down': isOrderedBy('created') == 'desc'}"></i>
  </th>
  <th class="pointer text-center" ng-click="sort('updated')" ng-show="isColumnEnabled('updated')" width="250">
    {t}Updated{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('updated') == 'asc', 'fa fa-caret-down': isOrderedBy('updated') == 'desc'}"></i>
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
    <button class="btn btn-danger btn-small" ng-click="delete(item)" type="button">
      <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
    </button>
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('command')" title="[% item.command %]">
    <div class="table-text">
      [% item.command %]
    </div>
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('params')" title="[% item.params %]">
    <div class="table-text">
      [% item.params %]
    </div>
  </td>
  <td class="v-align-middle" ng-show="isColumnEnabled('instance_id')" title="[% item.instance_id %]">
    <div class="table-text">
      [% item.instance_id %]
    </div>
  </td>
  <td class="text-center v-align-middle" title="{t}Status{/t}" ng-show="isColumnEnabled('status')">
    <span class="badge text-capitalize" ng-class="{ 'badge-warning': item.status === 'pending', 'badge-danger': item.status === 'error' , 'badge-info': item.status === 'processing' }" >
      [% item.status %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('created')">
    <div>
      <i class="fa fa-calendar"></i>
      [% item.created | moment : 'YYYY-MM-DD' %]
    </div>
    <small class="text-bold">
      <i class="fa fa-clock-o"></i>
      [% item.created | moment : 'HH:mm:ss' %]
    </small>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('updated')">
    <ng-container ng-if="item.updated">
    <div>
      <i class="fa fa-calendar"></i>
      [% item.updated | moment : 'YYYY-MM-DD' %]
    </div>
    <small class="text-bold">
      <i class="fa fa-clock-o"></i>
      [% item.updated | moment : 'HH:mm:ss' %]
    </small>
    </ng-container>
  </td>
{/block}
