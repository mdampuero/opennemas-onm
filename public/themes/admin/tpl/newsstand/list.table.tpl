{extends file="common/extension/list.table.tpl"}

{block name="commonColumns" prepend}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-media" checklist-model="app.columns.selected" checklist-value="'media'" type="checkbox">
    <label for="checkbox-media">
      {t}Media{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader" prepend}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('media')" width="80">
  </th>
{/block}

{block name="commonColumnsBody" prepend}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('media')">
    <img class="no-margin img-thumbnail" ng-src="[% data.extra.paths.newsstand + '/' + item.path + '/' + item.thumbnail %]">
  </td>
{/block}

{block name="customColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-date" checklist-model="app.columns.selected" checklist-value="'date'" type="checkbox">
    <label for="checkbox-date">
      {t}Date{/t}
    </label>
  </div>
  {acl isAllowed="KIOSKO_FAVORITE"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-favorite" checklist-model="app.columns.selected" checklist-value="'favorite'" type="checkbox">
      <label for="checkbox-favorite">
        {t}Favorite{/t}
      </label>
    </div>
  {/acl}
  {acl isAllowed="KIOSKO_AVAILABLE"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-published" checklist-model="app.columns.selected" checklist-value="'content_status'" type="checkbox">
      <label for="checkbox-published">
        {t}Published{/t}
      </label>
    </div>
  {/acl}
{/block}

{block name="customColumnsHeader"}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('date')" width="150">
    {t}Date{/t}
  </th>
  {acl isAllowed="KIOSKO_FAVORITE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('favorite')" width="150">
      <span class="m-l-5">
        {t}Favorite{/t}
      </span>
    </th>
  {/acl}
  {acl isAllowed="KIOSKO_AVAILABLE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
      <span class="m-l-5">
        {t}Published{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="customColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('date')">
    <i class="fa fa-calendar"></i>
    [% item.date %]
  </td>
  {acl isAllowed="KIOSKO_AVAILABLE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('favorite')">
      <button class="btn btn-white" ng-click="patch(item, 'favorite', item.favorite != 1 ? 1 : 0 )" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.favoriteLoading, 'fa-star text-warning' : !item.favoriteLoading && item.favorite == 1, 'fa-star': !item.favoriteLoading && item.in_home == 0 }"></i>
      </button>
    </td>
  {/acl}
  {acl isAllowed="KIOSKO_AVAILABLE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')">
      <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading, 'fa-check text-success' : !item.content_statusLoading && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading && item.content_status == 0 }"></i>
      </button>
    </td>
  {/acl}
{/block}

{block name="itemActions"}
  {acl isAllowed="KIOSKO_UPDATE"}
    <a class="btn btn-small" href="[% routing.generate('backend_newsstand_show', { id: item.id }) %]">
      <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
    </a>
  {/acl}
  {acl isAllowed="KIOSKO_DELETE"}
    <button class="btn btn-danger btn-small" ng-click="delete(item.pk_kiosko)" type="button">
      <i class="fa fa-trash-o m-r-5"></i>{t}Remove{/t}
    </button>
  {/acl}
{/block}
