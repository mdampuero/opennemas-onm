{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-keyword" checklist-model="app.columns.selected" checklist-value="'keyword'" type="checkbox" disabled >
    <label for="checkbox-keyword">
      {t}Name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-type" checklist-model="app.columns.selected" checklist-value="'type'" type="checkbox" disabled >
    <label for="checkbox-type">
      {t}Type{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-value" checklist-model="app.columns.selected" checklist-value="'value'" type="checkbox" disabled >
    <label for="checkbox-value">
      {t}Value{/t}
    </label>
  </div>

{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" ng-if="isColumnEnabled('keyword')" width="200">
    {t}Name{/t}
  </th>
  <th class="hidden-xs text-center" ng-if="isColumnEnabled('type')" width="200">
    <span class="m-l-5">
      {t}Type{/t}
    </span>
  </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('value')" width="200">
    <span class="m-l-5">
      {t}Value{/t}
    </span>
  </th>
{/block}

{block name="commonColumnsBody"}
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

  <td class="text-center v-align-middle" ng-if="isColumnEnabled('type')">
    <div class="table-text ng-binding">
    <span ng-if="item.type == 'url'"><span class="fa fa-external-link"></span> {t}External link to {/t}</span>
    <span ng-if="item.type == 'intsearch'" ><span class="fa fa-link"></span> {t}Internal search to keyword{/t}</span>
    <span ng-if="item.type == 'email'"><span class="fa fa-envelope"></span> {t}Link to send email to{/t}</span>
    </div>
  </td>

    <td class="text-center v-align-middle" ng-if="isColumnEnabled('value')">
    <div class="table-text ng-binding">
    [% item.value %]
    </div>
  </td>
{/block}
