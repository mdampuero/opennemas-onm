
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
    <input id="checkbox-context" checklist-model="app.columns.selected" checklist-value="'context'" type="checkbox">
    <label for="checkbox-context">
      {t}Context{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" ng-if="isColumnEnabled('name')">
    {t}Prompt{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('field')" width="200">
    {t}Field{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('context')">
    {t}Context{/t}
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
  <td class="v-align-middle" ng-if="isColumnEnabled('context')">
    <div class="table-text">
      [% item.context %]
    </div>
  </td>
{/block}
