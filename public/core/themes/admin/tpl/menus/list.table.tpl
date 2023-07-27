{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
    <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('name')">
      <input id="checkbox-name" checklist-model="app.columns.selected" checklist-value="'name'" type="checkbox" disabled>
      <label for="checkbox-name">
        {t}Name{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('position')">
      <input id="checkbox-position" checklist-model="app.columns.selected" checklist-value="'position'" type="checkbox" disabled>
      <label for="checkbox-position">
        {t}Position{/t}
      </label>
    </div>
{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" ng-if="isColumnEnabled('name')" width="200">
    {t}Name{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('position')" width="200">
    {t}Position{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
<td class="v-align-middle" ng-if="isColumnEnabled('name')">
  <div class="table-text">
    [% item.name %]
  </div>
  <div class="listing-inline-actions m-t-10 btn-group">
  {block name="itemActions"}
  {acl isAllowed="MENU_UPDATE"}
    <a class="btn btn-white btn-small" href="[% routing.generate('backend_menu_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
      <i class="fa fa-pencil m-r-5"></i>
    </a>
    <translator item="data.items[$index]" keys="keys" language="data.extra.locale.selected" link="[% routing.generate('backend_menu_show', { id: getItemId(item) }) %]" ng-class="{ 'dropup': $index >= items.length - 1 }" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
  {/acl}
  {acl isAllowed="MENU_DELETE"}
    <button class="btn btn-white btn-small" ng-click="delete(item.pk_menu)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
      <i class="fa fa-trash-o m-r-5 text-danger"></i>
    </button>
  {/acl}
  {/block}
  </div>
</td>
<td class="v-align-middle" ng-if="isColumnEnabled('position')">
  <div class="table-text">
    [% item.position %]
  </div>
</td>
{/block}
