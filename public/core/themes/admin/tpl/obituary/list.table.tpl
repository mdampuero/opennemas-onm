{extends file="common/extension/list.table.tpl"}

{block name="commonColumns" prepend}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-frontpage" checklist-model="app.columns.selected" checklist-value="'featured_frontpage'" type="checkbox">
    <label for="checkbox-featured-frontpage">
      {t}Featured in frontpage{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-inner" checklist-model="app.columns.selected" checklist-value="'featured_inner'" type="checkbox">
    <label for="checkbox-featured-inner">
      {t}Featured in inner{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader" prepend}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('featured_frontpage')" width="120">
    {t}Frontpage{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('featured_inner')" width="120">
    {t}Inner{/t}
  </th>
{/block}

{block name="commonColumnsBody" prepend}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('featured_frontpage')">
    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" type="featured_frontpage" ng-model="item" only-image="true" transform="zoomcrop,220,220">
      <div class="badge badge-default text-bold text-uppercase">
        {include file="common/component/icon/content_type_icon.tpl" iField="getFeaturedMedia(item, 'featured_frontpage')" iFlagName=true iFlagIcon=true}
      </div>
    </dynamic-image>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('featured_inner')">
    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" type="featured_inner" ng-model="item" only-image="true" transform="zoomcrop,220,220">
      <div class="badge badge-default text-bold text-uppercase">
        {include file="common/component/icon/content_type_icon.tpl" iField="getFeaturedMedia(item, 'featured_inner')" iFlagName=true iFlagIcon=true}
      </div>
    </dynamic-image>
  </td>
{/block}

{block name="customColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-date" checklist-model="app.columns.selected" checklist-value="'date'" type="checkbox">
    <label for="checkbox-date">
      {t}Event date{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-mortuary" checklist-model="app.columns.selected" checklist-value="'mortuary'" type="checkbox">
    <label for="checkbox-mortuary">
      {t}Mortuary{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-website" checklist-model="app.columns.selected" checklist-value="'website'" type="checkbox">
    <label for="checkbox-website">
      {t}Website{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-maps" checklist-model="app.columns.selected" checklist-value="'maps'" type="checkbox">
    <label for="checkbox-maps">
      {t}Google Maps{/t}
    </label>
  </div>
  {acl isAllowed="OBITUARY_AVAILABLE"}
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
    <span class="m-l-5">
      {t}Event date{/t}
    </span>
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('mortuary')" width="150">
    <span class="m-l-5">
      {t}Mortuary{/t}
    </span>
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('website')" width="150">
    <span class="m-l-5">
      {t}Website{/t}
    </span>
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('maps')" width="150">
    <span class="m-l-5">
      {t}Google Maps{/t}
    </span>
  </th>
  {acl isAllowed="OBITUARY_AVAILABLE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
      <span class="m-l-5">
        {t}Published{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="customColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('date')">
    <div class="table-text">
      [% item.date %]
    </div>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('mortuary')">
    <div class="table-text">
      [% item.mortuary %]
    </div>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('website')">
    <div class="table-text" ng-if="item.website">
      <a href="[% item.website %]" target="_blank">
        {t}External link{/t}
      </a>
    </div>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('maps')">
    <div class="table-text" ng-if="item.maps">
      <a href="[% item.maps %]" target="_blank">
        {t}External link{/t}
      </a>
    </div>
  </td>
  {acl isAllowed="OBITUARY_AVAILABLE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')">
      <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading == 1, 'fa-check text-success': !item.content_statusLoading == 1 && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading == 1 && item.content_status == 0 }"></i>
      </button>
    </td>
  {/acl}
{/block}

{block name="itemActions"}
  {acl isAllowed="OBITUARY_UPDATE"}
    <a class="btn btn-default btn-small" href="[% routing.generate('backend_obituaries_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
      <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
    </a>
    <translator item="data.items[$index]" keys="data.extra.keys" link="[% routing.generate('backend_obituaries_show', { id: getItemId(item) }) %]" ng-class="{ 'dropup': $index >= items.length - 1 }" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
  {/acl}
  {acl isAllowed="OBITUARY_DELETE"}
    <button class="btn btn-danger btn-small" ng-click="sendToTrash(item)" type="button">
      <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
    </button>
  {/acl}
  <div class="btn-group" ng-class="{ 'dropup': $index >= items.length - 1 }">
    <button class="btn btn-small btn-white dropdown-toggle" data-toggle="dropdown" type="button">
      <i class="fa fa-ellipsis-h"></i>
    </button>
    <ul class="dropdown-menu no-padding">
      <li>
        <a href="[% getFrontendUrl(item) %]" target="_blank">
          <i class="fa fa-external-link m-r-5"></i>
          {t}Link{/t}
          <span class="m-l-5" ng-if="item.params.bodyLink.length > 0">
            <small>
              ({t}External{/t})
            </small>
          </span>
        </a>
      </li>
    </ul>
  </div>
{/block}

