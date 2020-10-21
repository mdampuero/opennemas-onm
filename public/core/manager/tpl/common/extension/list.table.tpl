<div class="column-filters-toggle ng-cloak" ng-click="app.columns.collapsed = !app.columns.collapsed" ng-if="!flags.http.loading && items.length > 0 && (!isModeSupported() || app.mode === 'list')">
  <span class="column-filters-ellipsis"></span>
</div>
<div class="column-filters collapsed ng-cloak" ng-class="{ 'collapsed': app.columns.collapsed }" ng-if="!flags.http.loading && items.length > 0 && (!isModeSupported() || app.mode === 'list')">
  <h5>{t}Columns{/t}</h5>
  <div>
    {block name="columns"}{/block}
  </div>
</div>
<div class="grid simple ng-cloak no-animate" ng-show="!flags.http.loading && items.length > 0 && (!isModeSupported() || app.mode === 'list')">
  <div class="grid-body no-padding">
    <div class="table-wrapper ng-cloak">
      <div class="grid-overlay" ng-if="loading"></div>
      <table class="table table-fixed table-hover no-margin">
        <thead>
          <tr>
            <th class="text-center v-align-middle" width="50">
              <div class="checkbox checkbox-default">
                <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                <label for="select-all"></label>
              </div>
            </th>
            {block name="columnsHeader"}{/block}
          </tr>
        </thead>
        <tbody>
          <tr data-id="[% getItemId(item) %]" ng-class="{ row_selected: isSelected(getItemId(item)) }" ng-repeat="item in items">
            <td class="text-center v-align-middle">
              <div class="checkbox check-default" ng-if="isSelectable(item)">
                <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="getItemId(item)" type="checkbox">
                <label for="checkbox[%$index%]"></label>
              </div>
            </td>
            {block name="columnsBody"}
              <td class="v-align-middle" ng-if="isColumnEnabled('title')">
                <div class="table-text">
                  [% item.title %]
                </div>
                <div class="listing-inline-actions m-t-10">
                  {block name="itemActions"}{/block}
                </div>
              </td>
              <td class="text-center v-align-middle" ng-if="isColumnEnabled('created')">
                <div>
                  <i class="fa fa-calendar"></i>
                  [% item.created | moment : 'YYYY-MM-DD' %]
                </div>
                <small class="text-bold">
                  <i class="fa fa-clock-o"></i>
                  [% item.created | moment : 'HH:mm:ss' %]
                </small>
              </td>
              <td class="text-center v-align-middle" ng-if="isColumnEnabled('changed')">
                <div>
                  <i class="fa fa-calendar"></i>
                  [% item.changed | moment : 'YYYY-MM-DD' %]
                </div>
                <small class="text-bold">
                  <i class="fa fa-clock-o"></i>
                  [% item.changed | moment : 'HH:mm:ss' %]
                </small>
              </td>
              <td class="text-center v-align-middle" ng-if="isColumnEnabled('starttime')">
                <span class="text-bold" ng-if="!item.starttime">
                  ∞
                </span>
                <div ng-if="item.starttime">
                  <i class="fa fa-calendar"></i>
                  [% item.starttime | moment : 'YYYY-MM-DD' %]
                </div>
                <small class="text-bold" ng-if="item.starttime">
                  <i class="fa fa-clock-o"></i>
                  [% item.starttime | moment : 'HH:mm:ss' %]
                </small>
              </td>
              <td class="text-center v-align-middle" ng-if="isColumnEnabled('endtime')">
                <span class="text-bold" ng-if="!item.endtime">
                  ∞
                </span>
                <div ng-if="item.endtime">
                  <i class="fa fa-calendar"></i>
                  [% item.endtime | moment : 'YYYY-MM-DD' %]
                </div>
                <small class="text-bold" ng-if="item.endtime">
                  <i class="fa fa-clock-o"></i>
                  [% item.endtime | moment : 'HH:mm:ss' %]
                </small>
              </td>
              <td class="text-center v-align-middle" ng-if="isColumnEnabled('category')">
                {block name="categoryColumn"}
                  <small class="text-italic" ng-if="!item.category_id && !item.categories">
                    &lt;{t}No category{/t}&gt;
                  </small>
                  <a class="label label-default m-r-5 text-bold" href="[% routing.generate('backend_category_show', { id: item.category_id }) %]" ng-if="item.category_id">
                    [% (categories | filter: { id: item.category_id })[0].title %]
                  </a>
                  <a class="label label-default m-r-5 text-bold" href="[% routing.generate('backend_category_show', { id: id }) %]" ng-if="item.categories" ng-repeat="id in item.categories">
                    [% (categories | filter: { id: id })[0].title %]
                  </a>
                {/block}
              </td>
              <td class="v-align-middle" ng-if="isColumnEnabled('tags')">
                <small class="text-italic" ng-if="!item.tags || item.tags.length === 0">
                  &lt;{t}No tags{/t}&gt;
                </small>
                <div class="inline m-r-5 m-t-5" ng-repeat="id in item.tags" ng-if="!(data.extra.tags | filter : { id: id })[0].locale || (data.extra.tags | filter : { id: id })[0].locale === config.locale.selected">
                  <a class="label label-defaul label-info text-bold" href="[% routing.generate('backend_tag_show', { id: (data.extra.tags | filter : { id: id })[0].id }) %]">
                    [% (data.extra.tags | filter : { id: id })[0].name %]
                  </a>
                </div>
              </td>
              <td class="text-center v-align-middle" ng-if="isColumnEnabled('author')">
                <small class="text-italic" ng-if="!item.fk_author || (data.extra.authors | filter : { id: item.fk_author }).length === 0">
                  &lt;{t}No author{/t}&gt;
                </small>
                <a class="text-bold" href="[% routing.generate('backend_author_show', { id: item.fk_author }) %]" ng-if="item.fk_author && (data.extra.authors | filter : { id: item.fk_author }).length > 0">
                  [% (data.extra.authors | filter : { id: item.fk_author })[0].name %]
                </a>
              </td>
            {/block}
            {block name="customColumnsBody"}
            {/block}
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
