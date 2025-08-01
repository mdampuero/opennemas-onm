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
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('tags')">
    <input id="checkbox-tags" checklist-model="app.columns.selected" checklist-value="'tags'" type="checkbox">
    <label for="checkbox-tags">
      {t}Tags{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('author')">
    <input id="checkbox-author" checklist-model="app.columns.selected" checklist-value="'author'" type="checkbox">
    <label for="checkbox-author">
      {t}Author{/t}
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
  <th class="v-align-middle" ng-if="isColumnEnabled('title')" width="400">
    {t}Title{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('server')" width="140">
    {t}Server{/t}
  </th>
  <th class="text-center v-align-middle pointer" ng-click="sort('created_time')" ng-if="isColumnEnabled('created')" width="110">
    {t}Date{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('created_time') == 'asc', 'fa fa-caret-down': isOrderedBy('created_time') == 'desc' }"></i>
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('category')" width="140">
    {t}Category{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('tags')" width="200">
    {t}Tags{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('author')" width="200">
    {t}Author{/t}
  </th>
  <th class="text-center v-align-middle pointer" ng-click="sort('priority')" ng-if="isColumnEnabled('priority')" width="100">
    {t}Priority{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('priority') == 'asc', 'fa fa-caret-down': isOrderedBy('priority') == 'desc' }"></i>
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle" ng-if="isColumnEnabled('title')">
    <div class="pointer" ng-click="expanded[$index] = !expanded[$index]">
      <div class="table-text">
        <i class="fa fa-caret-right m-r-5" ng-class="{ 'fa-caret-down': expanded[$index], 'fa-caret-right': !expanded[$index] }" ng-if="item.related.length > 0"></i>
        [% item.title %]
      </div>
    </div>
    <div class="m-b-10 m-t-5" ng-show="!expanded[$index] && item.related.length > 0">
      <div ng-if="data.extra.related[id].type === 'text'" ng-repeat="id in item.related track by $index">
        <small>
          &angrt;
          <i class="fa m-r-5 fa-file-text-o" ng-show="data.extra.related[id].type === 'text'"></i>
          [% data.extra.related[id].title %]
        </small>
      </div>
      <img class="img-thumbnail m-r-10 m-t-10" ng-class="{ 'selected': selected.related && selected.related.indexOf(id) !== -1 }" ng-if="data.extra.related[id].type === 'photo'" ng-repeat="id in item.related track by $index" ng-src="[% routing.generate(routes.getContent, { id: id }) %]" style="height: 40px;">
    </div>
    <div class="related row m-b-10 m-l-30" ng-show="expanded[$index] && item.related.length > 0">
      <div class="m-t-10" ng-if="data.extra.related[id].type === 'text'" ng-repeat="id in item.related track by $index">
        <div class="checkbox check-default" ng-class="{ 'selected': selected.related && selected.related.indexOf(id) !== -1 }">
          <input id="checkbox-related-[% item.id %]-related-[% $index %]" checklist-model="selected.related" checklist-value="id" ng-disabled="!isSelected(item.id)" type="checkbox">
          <label for="checkbox-related-[% item.id %]-related-[% $index %]">
            <i class="fa m-l-30 m-r-5 fa-file-text-o" ng-show="data.extra.related[id].type === 'text'"></i>
            <span ng-if="data.extra.related[id].type === 'text'">[% data.extra.related[id].title %]</span>
          </label>
        </div>
      </div>
      <div class="col-xs-4 m-t-10" ng-if="data.extra.related[id].type === 'photo'" ng-repeat="id in item.related track by $index">
        <div class="checkbox check-default" ng-class="{ 'selected': selected.related && selected.related.indexOf(id) !== -1 }">
          <input id="checkbox-related-[% item.id %]-related-[% $index %]" checklist-model="selected.related" checklist-value="id" ng-disabled="!isSelected(item.id)" type="checkbox">
          <label for="checkbox-related-[% item.id %]-related-[% $index %]" ng-class="{ 'p-t-7 p-l-7': data.extra.related[id].type !== 'text' }">
            <div class="img-thumbnail-wrapper">
              <img class="img-thumbnail" ng-class="{ 'selected': selected.related && selected.related.indexOf(id) !== -1 }" ng-src="[% routing.generate(routes.getContent, { id: id }) %]" />
              <span class="badge badge-success" ng-if="isImported(item)">
                <i class="fa fa-check"></i>
                {t}Imported{/t}
              </span>
            </div>
          </label>
        </div>
        <div class="m-l-15" ng-show="data.extra.related[id].type === 'text'">
          <div class="listing-inline-actions btn-group">
            <a class="btn btn-white btn-small" ng-click="open('modal-view-item', data.extra.related[id])" title="{t}View{/t}" uib-tooltip="{t}Preview{/t}" tooltip-placement="top">
              <i class="fa fa-eye m-r-5"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="listing-inline-actions btn-group">
      <a class="btn btn-white btn-small" ng-click="preview(item)" uib-tooltip="{t}Preview{/t}" tooltip-placement="top">
        <i class="fa fa-eye"></i>
      </a>
      <span class="btn btn-white btn-small" ng-if="isImported(item)" uib-tooltip="{t}Imported{/t}" tooltip-placement="top">
        <i class="fa fa-check text-success"></i>
      </span>
      <button class="btn btn-white btn-small" ng-click="importItem(item)" ng-if="!isImported(item)" uib-tooltip="{t}Import{/t}" tooltip-placement="top">
        <span class="fa fa-cloud-download text-info"></span>
      </button>
    </div>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('server')">
    <a class="badge badge-default m-w-100 text-bold" ng-href="[% routing.generate('backend_news_agency_server_show', { id: item.source }) %]" ng-style="{ 'background-color' : data.extra.servers[item.source].color }">
      <div class="nowrap">
        [% data.extra.servers[item.source].name %]
      </div>
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
    <small class="text-italic" ng-if="!item.category">
      &lt;{t}No category{/t}&gt;
    </small>
    <div class="table-text" ng-if="item.category">
      <span class="label label-default text-bold">
        [% item.category %]
      </span>
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('tags')">
    <small class="text-italic" ng-if="!item.tags || item.tags.split(',').length === 0">
      &lt;{t}No tags{/t}&gt;
    </small>
    <div class="inline m-r-5 m-t-5" ng-if="item.tags" ng-repeat="tag in item.tags.split(',')">
      <span class="label label-defaul label-info text-bold">
        [% tag %]
      </span>
    </div>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('author')">
    <small class="text-italic" ng-if="!item.author">
      &lt;{t}No author{/t}&gt;
    </small>
    [% item.author %]
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('priority')">
    <span class="badge text-bold" ng-class="{ 'badge-danger': item.priority == 1, 'badge-warning': item.priority == 2, 'badge-info': item.priority == 3 }">
      [% item.priority %]
    </span>
  </td>
{/block}
