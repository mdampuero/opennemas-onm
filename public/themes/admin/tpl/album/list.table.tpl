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
    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.cover" transform="zoomcrop,220,220"></dynamic-image>
  </td>
{/block}

{block name="customColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-home" checklist-model="app.columns.selected" checklist-value="'home'" type="checkbox">
    <label for="checkbox-home">
      {t}Home{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-favorite" checklist-model="app.columns.selected" checklist-value="'favorite'" type="checkbox">
    <label for="checkbox-favorite">
      {t}Favorite{/t}
    </label>
  </div>
{/block}

{block name="customColumnsHeader"}
  {acl isAllowed="ALBUM_HOME"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('home')" width="150">
      <span class="m-l-5">
        {t}Home{/t}
      </span>
    </th>
  {/acl}
  {acl isAllowed="ALBUM_FAVORITE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('favorite')" width="150">
      <span class="m-l-5">
        {t}Favorite{/t}
      </span>
    </th>
  {/acl}
  {acl isAllowed="ALBUM_AVAILABLE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
      <span class="m-l-5">
        {t}Published{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="customColumnsBody"}
  {acl isAllowed="ALBUM_HOME"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('home')">
      <button class="btn btn-white" ng-click="patch(item, 'in_home', item.in_home != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.in_homeLoading == 1, 'fa-home text-info': item.in_homeLoading !== 1 && item.in_home == 1, 'fa-home': !item.in_homeLoading == 1 && item.in_home == 0 }"></i>
        <i class="fa fa-times fa-sub text-danger" ng-if="item.in_homeLoading !== 1 && item.in_home == 0"></i>
      </button>
    </td>
  {/acl}
  {acl isAllowed="ALBUM_FAVORITE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('favorite')">
      <button class="btn btn-white" ng-click="patch(item, 'favorite', item.favorite != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.favoriteLoading == 1, 'fa-star text-warning': !item.favoriteLoading == 1 && item.favorite == 1, 'fa-star-o': !item.favoriteLoading == 1 && item.favorite != 1 }"></i>
      </button>
    </td>
  {/acl}
  {acl isAllowed="ALBUM_AVAILABLE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')">
      <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading == 1, 'fa-check text-success': !item.content_statusLoading == 1 && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading == 1 && item.content_status == 0 }"></i>
      </button>
    </td>
  {/acl}
{/block}

{block name="itemActions"}
  {acl isAllowed="ALBUM_UPDATE"}
    <a class="btn btn-default btn-small" href="[% routing.generate('backend_album_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
      <i class="fa fa-pencil m-r-5"></i>
      {t}Edit{/t}
    </a>
    <translator item="data.items[$index]" keys="data.extra.keys" link="[% routing.generate('backend_album_show', { id: getItemId(item) }) %]" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" ng-class="{ 'dropup': $index >= data.items.length - 1 }" options="data.extra.locale" text="{t}Edit{/t}"></translator>
  {/acl}
  {acl isAllowed="ALBUM_DELETE"}
    <button class="btn btn-danger btn-small" ng-click="sendToTrash(item)" type="button">
      <i class="fa fa-trash-o m-r-5"></i>
      {t}Remove{/t}
    </button>
  {/acl}
{/block}
