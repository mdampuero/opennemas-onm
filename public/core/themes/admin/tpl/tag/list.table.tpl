{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-name" checklist-model="app.columns.selected" checklist-value="'name'" type="checkbox">
    <label for="checkbox-name">
      {t}Name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-slug" checklist-model="app.columns.selected" checklist-value="'slug'" type="checkbox">
    <label for="checkbox-slug">
      {t}Slug{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="data.extra.locale.multilanguage">
    <input id="checkbox-published" checklist-model="app.columns.selected" checklist-value="'locale'" type="checkbox">
    <label for="checkbox-published">
      {t}Locale{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-contents" checklist-model="app.columns.selected" checklist-value="'contents'" type="checkbox">
    <label for="checkbox-contents">
     {t}Contents{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" ng-click="sort('name')" ng-if="isColumnEnabled('name')" width="400">
    {t}Name{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
  </th>
  <th class="v-align-middle" ng-click="sort('slug')" ng-if="isColumnEnabled('slug')" width="200">
    {t}Slug{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('slug') == 'asc', 'fa fa-caret-down': isOrderedBy('slug') == 'desc'}"></i>
  </th>
  <th class="text-center v-align-middle" ng-click="sort('locale')" ng-if="data.extra.locale.multilanguage && isColumnEnabled('locale')" ng-if="isColumnEnabled('locale')" width="200">
    {t}Locale{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('locale') == 'asc', 'fa fa-caret-down': isOrderedBy('locale') == 'desc'}"></i>
  </th>
  <th class="text-center v-align-middle" class="text-center" ng-if="isColumnEnabled('contents')" width="120">
    {t}Contents{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle" ng-if="isColumnEnabled('name')">
    <div class="table-text">
      [% item.name %]
    </div>
    <div class="listing-inline-actions btn-group">
      <a class="btn btn-white btn-small" href="[% routing.generate('backend_tag_show', { id: getItemId(item) }) %]" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
        <i class="fa fa-pencil text-success_"></i>
      </a>
      <button class="btn btn-white btn-small" ng-click="delete(item.id)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
        <i class="fa fa-trash-o text-danger"></i>
      </button>
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('slug')">
    <div class="table-text">
      [% item.slug %]
    </div>
  </td>
  <td class="text-center v-align-middle" ng-if="data.extra.locale.multilanguage && isColumnEnabled('locale')">
    <div class="table-text" ng-if="item.locale">
      [% data.extra.locale.available[item.locale] %]
    </div>
    <small class="text-italic" ng-if="!item.locale">
      &lt;{t}Any{/t}&gt;
    </small>
  </td>
  <td class="text-center" ng-if="isColumnEnabled('contents')">
    <span class="badge badge-default text-bold" ng-class="{ 'badge-danger': !data.extra.stats[getItemId(item)] || data.extra.stats[getItemId(item)] == 0 }">
      [% data.extra.stats[getItemId(item)] ? data.extra.stats[getItemId(item)] : 0 %]
    </span>
  </td>
{/block}
