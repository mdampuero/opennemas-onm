{extends file="common/extension/list.table.tpl"}

{block name="customColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-favorite" checklist-model="app.columns.selected" checklist-value="'favorite'" type="checkbox">
    <label for="checkbox-favorite">
      {t}Favorite{/t}
    </label>
  </div>
  {acl isAllowed="ATTACHMENT_AVAILABLE"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-published" checklist-model="app.columns.selected" checklist-value="'content_status'" type="checkbox">
      <label for="checkbox-published">
        {t}Published{/t}
      </label>
    </div>
  {/acl}
{/block}

{block name="customColumnsHeader"}
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
    <a class="btn btn-default btn-small" href="[% routing.generate('backend_attachment_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
      <i class="fa fa-pencil m-r-5"></i>
      {t}Edit{/t}
    </a>
    <translator item="data.items[$index]" keys="data.extra.keys" language="data.extra.locale.selected" link="[% routing.generate('backend_attachment_show', { id: getItemId(item) }) %]" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" ng-class="{ 'dropup': $index >= data.items.length - 1 }" options="data.extra.locale" text="{t}Edit{/t}"></translator>
  {/acl}
  {acl isAllowed="ATTACHMENT_DELETE"}
    <button class="btn btn-danger btn-small" ng-click="delete(item.pk_content)" type="button">
      <i class="fa fa-trash-o m-r-5"></i>
      {t}Delete{/t}
    </button>
  {/acl}
  <div class="btn-group" ng-class="{ 'dropup': $index >= items.length - 1 }">
    <button class="btn btn-small btn-white dropdown-toggle" data-toggle="dropdown" type="button">
      <i class="fa fa-ellipsis-h"></i>
    </button>
    <ul class="dropdown-menu no-padding">
      <li>
        <a href="[% data.extra.paths.attachment + item.path %]" target="_blank">
          <i class="fa fa-download m-r-5"></i>
          {t}Download{/t}
        </a>
      </li>
    </ul>
  </div>
{/block}
