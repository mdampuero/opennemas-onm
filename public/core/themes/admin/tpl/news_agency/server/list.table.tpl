{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-title" checklist-model="app.columns.selected" checklist-value="'name'" disabled type="checkbox">
    <label for="checkbox-title">
      {t}Name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-url" checklist-model="app.columns.selected" checklist-value="'url'" type="checkbox">
    <label for="checkbox-url">
      {t}URL{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-synchronization" checklist-model="app.columns.selected" checklist-value="'synchronization'" type="checkbox">
    <label for="checkbox-synchronization">
      {t}Synchronization{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-synchronized" checklist-model="app.columns.selected" checklist-value="'synchronized'" type="checkbox">
    <label for="checkbox-synchronized">
      {t}Synchronized{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-color" checklist-model="app.columns.selected" checklist-value="'color'" type="checkbox">
    <label for="checkbox-color">
      {t}Color{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-automatic" checklist-model="app.columns.selected" checklist-value="'automatic'" type="checkbox">
    <label for="checkbox-automatic">
      {t}Automatic{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-enabled" checklist-model="app.columns.selected" checklist-value="'enabled'" type="checkbox">
    <label for="checkbox-enabled">
      {t}Enabled{/t}
    </label>
  </div>
{/block}

{block name="customColumns"}{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" width="20">
    #
  </th>
  <th class="v-align-middle" width="400">
    {t}Name{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('url')" width="400">
    {t}URL{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('synchronization')" width="150">
    {t}Synchronization{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('synchronized')" width="150">
    {t}Synchronized{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('color')" width="50">
    <i class="fa fa-paint-brush"></i>
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('automatic')" width="110">
    {t}Automatic{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('enabled')" width="80">
    {t}Enabled{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle">
    [% getItemId(item) %]
  </td>
  <td class="v-align-middle">
    <div class="table-text">
      [% item.name %]
    </div>
    <div class="listing-inline-actions m-t-10">
      <a class="btn btn-default btn-small" href="[% routing.generate(routes.redirect, { id: getItemId(item) }) %]">
        <i class="fa fa-pencil"></i>
        {t}Edit{/t}
      </a>
      <button class="btn btn-danger btn-small" ng-click="delete(getItemId(item))" type="button">
        <i class="fa fa-trash-o"></i>
        {t}Remove{/t}
      </button>
      {acl isAllowed="MASTER"}
        <div class="btn-group" ng-class="{ 'dropup': $index >= items.length - 1 }">
          <button class="btn btn-small btn-white dropdown-toggle" data-toggle="dropdown" type="button">
            <i class="fa fa-ellipsis-h"></i>
          </button>
          <ul class="dropdown-menu no-padding">
            <li ng-if="item.activated == 1">
              <a href="#" ng-click="synchronizeItem(getItemId(item))">
                <i class="fa fa-retweet m-r-5"></i>
                {t}Synchronize{/t}
              </a>
            </li>
            <li>
              <a href="#" ng-click="emptyItem(getItemId(item))">
                <i class="fa fa-fire m-r-5"></i>
                {t}Delete contents{/t}
              </a>
            </li>
          </ul>
        </div>
      {/acl}
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('url')">
    <div class="table-text">
      <a href="[% item.url %]" target="_blank">
        [% item.url %]
      </a>
    </div>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('synchronization')">
    <span class="badge badge-default text-bold text-uppercase">
      [% data.extra.sync_from[item.sync_from] %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('synchronized')">
    <span class="text-bold" ng-if="!data.extra.stats[item.id]">
      ∞
    </span>
    <div ng-if="data.extra.stats[item.id]">
      <i class="fa fa-calendar"></i>
      [% data.extra.stats[item.id] | moment : 'YYYY-MM-DD': null : '{$app.locale->getTimeZone()->getName()}' %]
    </div>
    <small class="text-bold" ng-if="data.extra.stats[item.id]">
      <i class="fa fa-clock-o"></i>
      [% data.extra.stats[item.id] | moment : 'HH:mm:ss' : null : '{$app.locale->getTimeZone()->getName()}' %]
    </small>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('color')">
    <span class="badge badge-default" ng-style="{ 'background-color': item.color }" ng-show="item.color">
      &nbsp;&nbsp;
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('automatic')">
    <i class="fa" ng-class="{ 'fa-check text-success': item.auto_import == 1, 'fa-times text-danger': !item.auto_import || item.auto_import == 0 }"></i>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('enabled')">
    <button class="btn btn-white" ng-click="patch(item, 'activated', item.activated != 1 ? 1 : 0)" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.activatedLoading, 'fa-check text-success' : !item.activatedLoading && item.activated == 1, 'fa-times text-danger': !item.activatedLoading && !item.activated || item.activated == 0 }"></i>
    </button>
  </td>
{/block}
