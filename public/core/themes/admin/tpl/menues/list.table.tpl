{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
    <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('name')">
      <input id="checkbox-name" checklist-model="app.columns.selected" checklist-value="'name'" type="checkbox">
      <label for="checkbox-name">
        {t}Name{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('position')">
      <input id="checkbox-position" checklist-model="app.columns.selected" checklist-value="'position'" type="checkbox">
      <label for="checkbox-position">
        {t}Position{/t}
      </label>
    </div>
{/block}

{block name="commonColumnsHeader"}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('name')" width="200">
    {t}Name{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('position')" width="200">
    {t}Position{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
<td class="v-align-middle" ng-if="isColumnEnabled('name')">
  <div class="table-text">
    [% item.name %]
  </div>
  <div class="listing-inline-actions m-t-10">
  {block name="itemActions"}
  {acl isAllowed="MENU_UPDATE"}
    <a class="btn btn-default btn-small" href="[% routing.generate('backend_menu_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
      <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
    </a>
    <translator item="data.items[$index]" keys="data.extra.keys" link="[% routing.generate('backend_menu_show', { id: getItemId(item) }) %]" ng-class="{ 'dropup': $index >= items.length - 1 }" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
  {/acl}
  {acl isAllowed="MENU_DELETE"}
    <button class="btn btn-danger btn-small" ng-click="sendToTrash(item)" type="button">
      <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
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

{* {block name="customColumns"}
{/block}

{block name="customColumnsHeader"}
{/block}

{block name="customColumnsBody"}
{/block} *}

