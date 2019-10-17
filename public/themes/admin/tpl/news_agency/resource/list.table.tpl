{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('title')">
    <input id="checkbox-title" checklist-model="app.columns.selected" checklist-value="'title'" disabled type="checkbox">
    <label for="checkbox-title">
      {t}Title{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('server')">
    <input id="checkbox-server" checklist-model="app.columns.selected" checklist-value="'server'" type="checkbox">
    <label for="checkbox-server">
      {t}Server{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('created')">
    <input id="checkbox-created" checklist-model="app.columns.selected" checklist-value="'created'" type="checkbox">
    <label for="checkbox-created">
      {t}Created{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('category')">
    <input id="checkbox-category" checklist-model="app.columns.selected" checklist-value="'category'" type="checkbox">
    <label for="checkbox-category">
      {t}Category{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('priority')">
    <input id="checkbox-priority" checklist-model="app.columns.selected" checklist-value="'priority'" type="checkbox">
    <label for="checkbox-priority">
      {t}Priority{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" width="400" ng-if="isColumnEnabled('title')">
    {t}Title{/t}
  </th>
  <th class="text-center v-align-middle" width="140" ng-if="isColumnEnabled('server')">
    {t}Server{/t}
  </th>
  <th class="text-center v-align-middle" width="100" ng-if="isColumnEnabled('created')">
    {t}Date{/t}
  </th>
  <th class="text-center v-align-middle" width="140" ng-if="isColumnEnabled('category')">
    {t}Category{/t}
  </th>
  <th class="text-center v-align-middle" width="100" ng-if="isColumnEnabled('priority')">
    {t}Priority{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle" ng-if="isColumnEnabled('title')">
    <div class="pointer p-b-10" ng-click="expanded[$index] = !expanded[$index]">
      <i class="fa fa-caret-right m-r-5" ng-class="{ 'fa-caret-down': expanded[$index], 'fa-caret-right': !expanded[$index] }" ng-if="item.related.length > 0" style="width: 8px;"></i>
      <div class="table-text">
        [% item.title %]
      </div>
    </div>
    <div ng-show="!expanded[$index]" >
      <span ng-repeat="id in item.related">
        <img class="img-thumbnail" ng-class="{ 'selected': item.import && item.import.indexOf(id) !== -1 }" ng-if="extra.related[id].type === 'photo'" ng-src="[% routing.generate('backend_ws_news_agency_show_image', { source: extra.related[id].source, id: extra.related[id].id }) %]" style="height: 48px;" />
      </span>
    </div>
    <div class="related clearfix p-b-10" ng-show="expanded[$index] && item.related.length > 0">
      <div class="p-b-10" ng-class="{ 'col-xs-4': extra.related[id].type !== 'text' }" ng-repeat="id in item.related">
        <div class="checkbox check-default" ng-class="{ 'selected': item.import && item.import.indexOf(id) !== -1 }">
          <input id="checkbox-related-[% item.id %]-related-[% $index %]" checklist-model="item.import" checklist-value="id" ng-disabled="!isSelected(item.id) || (item.import.length > 1 && item.import.indexOf(id) === -1)" type="checkbox">
          <label for="checkbox-related-[% item.id %]-related-[% $index %]" ng-class="{ 'p-t-7 p-l-7': extra.related[id].type !== 'text' }">
            <i class="fa m-l-30 m-r-5 fa-file-text-o" ng-show="extra.related[id].type === 'text'"></i>
            <span ng-if="extra.related[id].type === 'text'">[% extra.related[id].title %]</span>
            <div class="img-thumbnail-wrapper">
              <img class="img-thumbnail" ng-class="{ 'selected': item.import && item.import.indexOf(id) !== -1 }" ng-src="[% routing.generate('backend_ws_news_agency_show_image', { source: extra.related[id].source, id: extra.related[id].id }) %]" />
              <span class="badge badge-success no-animate" ng-if="imported.indexOf(item.urn) !== -1">{t}Imported{/t}</span>
            </div>
          </label>
        </div>
        <div class="m-l-15" ng-show="extra.related[id].type === 'text'">
          <div class="listing-inline-actions">
            <a class="btn btn-default btn-small" ng-click="open('modal-view-item', extra.related[id])" title="{t}View{/t}">
              <i class="fa fa-eye m-r-5"></i>
              {t}Preview{/t}
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="listing-inline-actions">
      <a class="btn btn-default btn-small" ng-click="preview(item)">
        <i class="fa fa-eye m-r-5"></i>
        {t}Preview{/t}
      </a>
      <span class="btn btn-success btn-small" ng-if="imported.indexOf(item.urn) !== -1">{t}Imported{/t}</span>
      <button class="btn btn-info btn-small" ng-click="import(item)" ng-if="imported.indexOf(item.urn) === -1">
        <span class="fa fa-cloud-download m-r-5"></span>
        {t}Import{/t}
      </button>
    </div>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('server')">
    <a class="badge badge-default text-bold" ng-href="[% routing.generate('backend_news_agency_server_show', { id: item.source }) %]" style="background-color:[% data.extra.servers[item.source].color %];">
      [% data.extra.servers[item.source].name %]
    </a>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('created')">
    <span class="text-bold" ng-if="!item.created_time">
      ∞
    </span>
    <div ng-if="item.created_time">
      <i class="fa fa-calendar"></i>
      [% item.created_time | moment : 'YYYY-MM-DD' %]
    </div>
    <small class="text-bold" ng-if="item.created_time">
      <i class="fa fa-clock-o"></i>
      [% item.created_time | moment : 'HH:mm:ss' %]
    </small>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('category')">
    <span class="label label-default text-bold">
      [% item.category %]
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('priority')">
    <span class="badge text-bold" ng-class="{ 'badge-danger': item.priority == 1, 'badge-warning': item.prority == 2, 'badge-info': item.priority == 3 }">
      [% item.priority %]
    </span>
  </td>
{/block}
