{extends file="common/extension/list.table.tpl"}

{block name="customColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-published" checklist-model="app.columns.selected" checklist-value="'content_status'" type="checkbox">
    <label for="checkbox-published">
      {t}Published{/t}
    </label>
  </div>
{/block}

{block name="customColumnsHeader"}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
    {t}Published{/t}
  </th>
{/block}

{block name="customColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')">
    {acl isAllowed="STATIC_PAGE_AVAILABLE"}
      <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading, 'fa-check text-success' : !item.content_statusLoading && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading && item.content_status == 0 }"></i>
      </button>
    {/acl}
  </td>
{/block}

{block name="itemActions"}
  {acl isAllowed="STATIC_PAGE_UPDATE"}
    <a class="btn btn-default btn-small" href="[% routing.generate('backend_static_page_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
      <i class="fa fa-pencil m-r-5"></i>
      {t}Edit{/t}
    </a>
    <translator item="data.items[$index]" keys="data.extra.keys" link="[% routing.generate('backend_static_page_show', { id: getItemId(item) }) %]" ng-class="{ 'dropup': $index >= items.length - 1 }" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
  {/acl}
  {acl isAllowed="STATIC_PAGE_DELETE"}
    <button class="btn btn-danger btn-small" ng-click="sendToTrash(item)" type="button">
      <i class="fa m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': item.in_litterLoading, 'fa-trash-o': !item.in_litterLoading }"></i>{t}Delete{/t}
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
        </a>
      </li>
    </ul>
  </div>
{/block}
