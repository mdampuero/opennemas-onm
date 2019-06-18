<div class="column-filters-toggle ng-cloak" ng-click="app.columns.collapsed = !app.columns.collapsed" ng-if="!flags.http.loading && items.length > 0 && (ignoreMode || app.mode === 'list')">
  <span class="column-filters-ellipsis"></span>
</div>
<div class="column-filters collapsed ng-cloak" ng-class="{ 'collapsed': app.columns.collapsed }" ng-if="!flags.http.loading && items.length > 0 && (ignoreMode || app.mode === 'list')">
  <h5>{t}Columns{/t}</h5>
  <div>
  {block name="commonColumns"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-media" checklist-model="app.columns.selected" checklist-value="'media'" type="checkbox">
      <label for="checkbox-media">
        {t}Media{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-title" checklist-model="app.columns.selected" checklist-value="'title'" disabled type="checkbox">
      <label for="checkbox-title">
        {t}Title{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-created" checklist-model="app.columns.selected" checklist-value="'created'" type="checkbox">
      <label for="checkbox-created">
        {t}Created{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-changed" checklist-model="app.columns.selected" checklist-value="'changed'" type="checkbox">
      <label for="checkbox-changed">
        {t}Updated{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-starttime" checklist-model="app.columns.selected" checklist-value="'starttime'" type="checkbox">
      <label for="checkbox-starttime">
        {t}Start date{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-endtime" checklist-model="app.columns.selected" checklist-value="'endtime'" type="checkbox">
      <label for="checkbox-endtime">
        {t}End date{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-support" checklist-model="app.columns.selected" checklist-value="'category'" type="checkbox">
      <label for="checkbox-support">
        {t}Category{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-contents" checklist-model="app.columns.selected" checklist-value="'tags'" type="checkbox">
      <label for="checkbox-contents">
        {t}Tags{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-theme" checklist-model="app.columns.selected" checklist-value="'author'" type="checkbox">
      <label for="checkbox-theme">
        {t}Author{/t}
      </label>
    </div>
    {/block}
    {block name="customColumns"}{/block}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-published" checklist-model="app.columns.selected" checklist-value="'content_status'" type="checkbox">
      <label for="checkbox-published">
        {t}Published{/t}
      </label>
    </div>
  </div>
</div>
<div class="grid simple ng-cloak no-animate" ng-show="!flags.http.loading && items.length > 0 && (ignoreMode || app.mode === 'list')">
  <div class="grid-body no-padding">
    <div class="table-wrapper ng-cloak">
      <table class="table table-fixed table-hover no-margin">
        <thead>
          <tr>
            {block name="commonColumnsHeader"}
              <th class="text-center v-align-middle" width="50">
                <div class="checkbox checkbox-default">
                  <input id="select-all" ng-checked="areAllSelected()" ng-click="toggleAll();" ng-model="selected.all" type="checkbox">
                  <label for="select-all"></label>
                </div>
              </th>
              <th class="text-center v-align-middle" ng-if="isColumnEnabled('media')" width="80">
              </th>
              <th class="v-align-middle" ng-if="isColumnEnabled('title')" width="400">
                {t}Title{/t}
              </th>
              <th class="text-center v-align-middle" ng-if="isColumnEnabled('created')" width="150">
                {t}Created{/t}
              </th>
              <th class="text-center v-align-middle" ng-if="isColumnEnabled('changed')" width="150">
                {t}Updated{/t}
              </th>
              <th class="text-center v-align-middle" ng-if="isColumnEnabled('starttime')" width="150">
                {t}Start date{/t}
              </th>
              <th class="text-center v-align-middle" ng-if="isColumnEnabled('endtime')" width="150">
                {t}End date{/t}
              </th>
              <th class="text-center v-align-middle" ng-if="isColumnEnabled('category')" width="200">
                {t}Category{/t}
              </th>
              <th class="v-align-middle" ng-if="isColumnEnabled('tags')" width="200">
                {t}Tags{/t}
              </th>
              <th class="text-center v-align-middle" ng-if="isColumnEnabled('author')" width="200">
                {t}Author{/t}
              </th>
            {/block}
            {block name="customColumnsHeader"}{/block}
          </tr>
        </thead>
        <tbody>
          <tr data-id="[% getItemId(item) %]" ng-class="{ row_selected: isSelected(getItemId(item)) }" ng-repeat="item in items">
            <td class="text-center v-align-middle">
              <div class="checkbox check-default">
                <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="getItemId(item)" type="checkbox">
                <label for="checkbox[%$index%]"></label>
              </div>
            </td>
            {block name="commonColumnsBody"}
              <td class="v-align-middle" ng-if="isColumnEnabled('title')">
                <div class="table-text" title="[% item.title %]">
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
                <a class="label label-default m-r-5 text-bold" href="[% routing.generate('backend_category_show', { id: item.pk_fk_content_category }) %]">
                  [% (categories | filter: { pk_content_category: item.pk_fk_content_category })[0].title %]
                </a>
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
                <a href="[% routing.generate('backend_author_show', { id: item.fk_author }) %]" ng-if="item.fk_author && (data.extra.authors | filter : { id: item.fk_author }).length > 0">
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
  <div class="grid-footer clearfix ng-cloak">
    <div class="pull-right">
      <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
    </div>
  </div>
</div>
