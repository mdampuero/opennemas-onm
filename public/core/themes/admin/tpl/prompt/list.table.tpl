
{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-name" checklist-model="app.columns.selected" checklist-value="'name'" type="checkbox">
    <label for="checkbox-name">
      {t}Prompt{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-field" checklist-model="app.columns.selected" checklist-value="'field'" type="checkbox">
    <label for="checkbox-field">
      {t}Field{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-mode" checklist-model="app.columns.selected" checklist-value="'mode'" type="checkbox">
    <label for="checkbox-mode">
      {t}Mode{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-tone" checklist-model="app.columns.selected" checklist-value="'tone'" type="checkbox">
    <label for="checkbox-tone">
      {t}Default tone{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-role" checklist-model="app.columns.selected" checklist-value="'role'" type="checkbox">
    <label for="checkbox-role">
      {t}Default role{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" ng-if="isColumnEnabled('name')"  width="500">
    {t}Prompt{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('field')">
    {t}Field{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('mode')">
    {t}Mode{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('tone')">
    {t}Default tone{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('role')">
    {t}Default role{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle" ng-if="isColumnEnabled('name')">
    <div class="table-text">
      [% item.name %]
    </div>
    <div class="listing-inline-actions btn-group">
      <a class="btn btn-white btn-small" href="[% routing.generate('backend_openai_prompt_show', { id: getItemId(item) }) %]" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
        <i class="fa fa-pencil text-success_"></i>
      </a>
      <button class="btn btn-white btn-small" ng-click="delete(item.id)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
        <i class="fa fa-trash-o text-danger"></i>
      </button>
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('field')">
    <div class="table-text">
      [% item.field %]
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('mode')">
    <div class="table-text">
      [% item.mode %]
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('tone')">
    <div class="table-text">
      [% item.tone %]
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('role')">
    <div class="table-text">
      [% item.role %]
    </div>
  </td>
{/block}
