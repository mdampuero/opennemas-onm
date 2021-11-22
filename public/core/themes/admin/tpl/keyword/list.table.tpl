{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-keyword" checklist-model="app.columns.selected" checklist-value="'keyword'" type="checkbox" >
    <label for="checkbox-keyword">
      {t}Keyword{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-value" checklist-model="app.columns.selected" checklist-value="'value'" type="checkbox" >
    <label for="checkbox-value">
      {t}Value{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-type" checklist-model="app.columns.selected" checklist-value="'type'" type="checkbox" >
    <label for="checkbox-type">
      {t}Type{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" ng-if="isColumnEnabled('keyword')" width="200">
    {t}Keyword{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('value')" width="200">
    <span class="m-l-5">
      {t}Value{/t}
    </span>
  </th>
  <th class="hidden-xs text-center" ng-if="isColumnEnabled('type')" width="150">
    <span class="m-l-5">
      {t}Type{/t}
    </span>
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="hidden-xs text-center" ng-if="isColumnEnabled('check')">
    
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('keyword')">
    <div class="table-text ng-binding">
    [% item.keyword %]
    </div>
    
    <div class="listing-inline-actions">
      {acl isAllowed="KEYWORD_UPDATE"}
        <a class="btn btn-defauilt btn-small" href="[% routing.generate('backend_keyword_show', { id: getItemId(item) }) %]" title="{t}Edit{/t}">
          <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
        </a>
      {/acl}
      {acl isAllowed="KEYWORD_DELETE"}
        <button class="btn btn-danger btn-small" ng-click="delete(item.id)" title="{t}Delete{/t}" type="button">
          <i class="fa fa-trash-o m-r-5"></i>{t}Remove{/t}
        </button>
      {/acl}
    </div>
  </td>

  <td class="text-center v-align-middle" ng-if="isColumnEnabled('value')">
    <div class="table-text ng-binding">
    [% item.value %]
    </div>
  </td>

  <td class="text-center v-align-middle" ng-if="isColumnEnabled('type')">
    <div class="table-text ng-binding">
    [% item.type %]
    </div>
  </td>
{/block}
