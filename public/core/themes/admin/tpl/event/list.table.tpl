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
    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="getFeaturedMedia(item, 'featured_frontpage').path" only-image="true" transform="zoomcrop,220,220"></dynamic-image>
  </td>
{/block}

{block name="customColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-type" checklist-model="app.columns.selected" checklist-value="'type'" type="checkbox">
    <label for="checkbox-type">
      {t}Type{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-start" checklist-model="app.columns.selected" checklist-value="'start'" type="checkbox">
    <label for="checkbox-start">
      {t}Start{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-end" checklist-model="app.columns.selected" checklist-value="'end'" type="checkbox">
    <label for="checkbox-end">
      {t}End{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-published" checklist-model="app.columns.selected" checklist-value="'content_status'" type="checkbox">
    <label for="checkbox-published">
      {t}Published{/t}
    </label>
  </div>
{/block}

{block name="customColumnsHeader"}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('type')" width="150">
    {t}Type{/t}
  </th>
  <th class="text-center v-align-middle pointer" ng-click="sort('event_start_date')" ng-if="isColumnEnabled('start')" width="150">
    {t}Start{/t}
    <i ng-class="{ 'fa fa-caret-up': isOrderedBy('event_start_date') == 'asc', 'fa fa-caret-down': isOrderedBy('event_start_date') == 'desc' }"></i>
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('end')" width="150">
    {t}End{/t}
  </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
    {t}Published{/t}
  </th>
{/block}

{block name="customColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('type')">
    <div>
      [% data.extra.events[item.event_type].name %]
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('start')">
    <span ng-show="!item.event_start_date && !item.event_start_hour">?</span>
    <div ng-show="item.event_start_date">
      <i class="fa fa-calendar"></i>
      [% item.event_start_date %]
    </div>
    <small ng-show="item.event_start_hour">
      <i class="fa fa-clock-o"></i>
      <strong>[% item.event_start_hour %]</strong>
    </small>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('end')">
    <span ng-show="!item.event_end_date && !item.event_end_hour">?</span>
    <div ng-show="item.event_end_date">
      <i class="fa fa-calendar"></i>
      [% item.event_end_date %]
    </div>
    <small ng-show="item.event_end_hour">
      <i class="fa fa-clock-o"></i>
      <strong>[% item.event_end_hour %]</strong>
    </small>
  </td>
  {acl isAllowed="EVENT_AVAILABLE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')">
      <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading, 'fa-check text-success' : !item.content_statusLoading && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading && item.content_status == 0 }"></i>
      </button>
    </td>
  {/acl}
{/block}

{block name="itemActions"}
  {acl isAllowed="EVENT_UPDATE"}
    <a class="btn btn-white btn-small" href="[% routing.generate('backend_event_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
      <i class="fa fa-pencil"></i>
    </a>
    <translator item="data.items[$index]" keys="data.extra.keys" language="data.extra.locale.selected" link="[% routing.generate('backend_event_show', { id: getItemId(item) }) %]" ng-class="{ 'dropup': $index >= items.length - 1 }" class="btn-group" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
  {/acl}
  {acl isAllowed="EVENT_ADMIN"}
    <button class="btn btn-white btn-small" ng-click="createCopy(item)" type="button" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Duplicate{/t}" tooltip-placement="top">
      <i class="fa fa-copy"></i>
    </button>
  {/acl}
  <a ng-if="item.slug" class="btn btn-white btn-small" href="[% getFrontendUrl(item) %]" target="_blank" uib-tooltip="{t}Link{/t}" tooltip-placement="top">
    <i class="fa fa-external-link"></i>
  </a>
  <a ng-if="!item.slug" class="btn btn-white btn-small" disabled>
    <i class="fa fa-external-link"></i>
  </a>
  {acl isAllowed="EVENT_DELETE"}
    <button class="btn btn-white btn-small" ng-click="sendToTrash(item)" type="button" uib-tooltip="{t}Remove{/t}" tooltip-placement="top">
      <i class="fa fa-trash-o text-danger"></i>
    </button>
  {/acl}
{/block}
