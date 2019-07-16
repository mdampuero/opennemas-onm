{extends file="common/extension/list.table.tpl"}

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
  {acl isAllowed="ATTACHMENT_HOME"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('home')" width="150">
      <span class="m-l-5">
        {t}Home{/t}
      </span>
    </th>
  {/acl}
  {acl isAllowed="ATTACHMENT_FAVORITE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('favorite')" width="150">
      <span class="m-l-5">
        {t}Favorite{/t}
      </span>
    </th>
  {/acl}
  {acl isAllowed="ATTACHMENT_AVAILABLE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
      <span class="m-l-5">
        {t}Published{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="customColumnsBody"}
  {acl isAllowed="ATTACHMENT_HOME"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('home')">
      <button class="btn btn-white" ng-click="patch(item, 'in_home', item.in_home != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.in_homeLoading == 1, 'fa-home text-info': item.in_homeLoading !== 1 && item.in_home == 1, 'fa-home': !item.in_homeLoading == 1 && item.in_home == 0 }"></i>
        <i class="fa fa-times fa-sub text-danger" ng-if="item.in_homeLoading !== 1 && item.in_home == 0"></i>
      </button>
    </td>
  {/acl}
  {acl isAllowed="ATTACHMENT_FAVORITE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('favorite')">
      <button class="btn btn-white" ng-click="patch(item, 'favorite', item.favorite != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.favoriteLoading == 1, 'fa-star text-warning': !item.favoriteLoading == 1 && item.favorite == 1, 'fa-star-o': !item.favoriteLoading == 1 && item.favorite != 1 }"></i>
      </button>
    </td>
  {/acl}
  {acl isAllowed="ATTACHMENT_AVAILABLE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')">
      <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading == 1, 'fa-check text-success': !item.content_statusLoading == 1 && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading == 1 && item.content_status == 0 }"></i>
      </button>
    </td>
  {/acl}
{/block}

{block name="itemActions"}
  {acl isAllowed="ATTACHMENT_UPDATE"}
    <a class="btn btn-default btn-small" href="[% routing.generate('backend_attachment_show', { id: getItemId(item) }) %]">
      <i class="fa fa-pencil m-r-5"></i>
      {t}Edit{/t}
    </a>
  {/acl}
  {acl isAllowed="ATTACHMENT_DELETE"}
    <button class="btn btn-danger btn-small" ng-click="delete(item.pk_content)" type="button">
      <i class="fa fa-trash-o m-r-5"></i>
      {t}Delete{/t}
    </button>
  {/acl}
  <a class="btn btn-white btn-small" href="[% data.extra.paths.attachment + item.path %]" target="_blank">
    <i class="fa fa-download m-r-5"></i>
    {t}Download{/t}
  </a>
{/block}
