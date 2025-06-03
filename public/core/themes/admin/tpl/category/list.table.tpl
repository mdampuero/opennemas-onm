
{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-name" checklist-model="app.columns.selected" checklist-value="'name'" type="checkbox" disabled="true">
    <label for="checkbox-name">
      {t}Name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-slug" checklist-model="app.columns.selected" checklist-value="'slug'" type="checkbox">
    <label for="checkbox-slug">
      {t}Slug{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-cover" checklist-model="app.columns.selected" checklist-value="'cover'" type="checkbox">
    <label for="checkbox-cover">
      {t}Cover{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-color" checklist-model="app.columns.selected" checklist-value="'color'" type="checkbox">
    <label for="checkbox-color">
      {t}Color{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-contents" checklist-model="app.columns.selected" checklist-value="'contents'" type="checkbox">
    <label for="checkbox-contents">
     {t}Contents{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-visibility" checklist-model="app.columns.selected" checklist-value="'visibility'" type="checkbox">
    <label for="checkbox-visibility">
     {t}Visibility{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-enabled" checklist-model="app.columns.selected" checklist-value="'enabled'" type="checkbox">
    <label for="checkbox-enabled">
     {t}Enabled{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-rss" checklist-model="app.columns.selected" checklist-value="'rss'" type="checkbox">
    <label for="checkbox-rss">
     {t}RSS{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-manual" checklist-model="app.columns.selected" checklist-value="'manual'" type="checkbox">
    <label for="checkbox-manual">
     {t}Manual{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" width="400" ng-if="isColumnEnabled('name')">
    {t}Name{/t}
  </th>
  <th class="v-align-middle" width="200" ng-if="isColumnEnabled('slug')">
    {t}Slug{/t}
  </th>
  <th class="text-center v-align-middle" width="80" ng-if="isColumnEnabled('cover')">
    <i class="fa fa-picture-o"></i>
  </th>
  <th class="text-center v-align-middle" width="80" ng-if="isColumnEnabled('color')">
    <i class="fa fa-paint-brush"></i>
  </th>
  <th class="text-center v-align-middle" width="100" ng-if="isColumnEnabled('contents')">
    {t}Contents{/t}
  </th>
  <th class="text-center v-align-middle" width="100" ng-if="isColumnEnabled('visibility')">
    {t}Visible{/t}
  </th>
  <th class="text-center v-align-middle" width="100" ng-if="isColumnEnabled('enabled')">
    {t}Enabled{/t}
  </th>
  <th class="text-center v-align-middle" width="100" ng-if="isColumnEnabled('rss')">
    {t}RSS{/t}
  </th>
  <th class="text-center v-align-middle" width="100" ng-if="isColumnEnabled('manual')">
    {t}Manual{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle" ng-if="isColumnEnabled('name')">
    <div class="[% 'm-l-' + 30 * levels[getItemId(item)] %]">
      <div class="table-text ng-binding">
        [% item.title %]
      </div>
      <div class="listing-inline-actions btn-group">
        <translator item="data.items[$index]" keys="data.extra.keys" language="data.extra.locale.selected" link="[% routing.generate('backend_category_show', { id: getItemId(item) }) %]" ng-class="{ 'dropup': $index >= items.length - 1 }" class="btn-group" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
        <a class="btn btn-white btn-small" href="[% routing.generate('backend_category_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
          <i class="fa fa-pencil"></i>
        </a>
        {acl isAllowed="MASTER"}
          <a class="btn btn-white btn-small"  href="#" ng-click="move(getItemId(item), item)" uib-tooltip="{t}Move contents{/t}" tooltip-placement="top">
            <i class="fa fa-flip-horizontal fa-reply"></i>
          </a>
          <a class="btn btn-white btn-small" href="#" ng-click="empty(getItemId(item))" uib-tooltip="{t}Delete contents{/t}" tooltip-placement="top">
            <i class="fa fa-fire"></i>
          </a>
        {/acl}
        <div uib-tooltip="{t}Only empty categories can be deleted{/t}" tooltip-enable="data.extra.stats[getItemId(item)] > 0" tooltip-class="tooltip-danger">
          <button class="btn btn-white btn-small" ng-click="delete(getItemId(item))" ng-disabled="data.extra.stats[getItemId(item)] > 0" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('slug')">
    [% item.name %]
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('cover')">
    <dynamic-image class="img-thumbnail" instance="{$app.instance->getMediaShortPath()}/" ng-model="item.logo_id" only-image="true"></dynamic-image>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('color')">
    <span class="badge badge-white" ng-if="item.color" ng-style="{ 'background-color': item.color}">&nbsp;&nbsp;</span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('contents')">
    <span class="badge badge-default" ng-class="{ 'badge-danger': !data.extra.stats[getItemId(item)] || data.extra.stats[getItemId(item)] == 0 }">
      <strong>
        [% data.extra.stats[getItemId(item)] ? data.extra.stats[getItemId(item)] : 0 %]
      </strong>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('visibility')">
    <button class="btn btn-white" ng-click="patch(item, 'visible', item.visible != 1 ? 1 : 0)" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.visibleLoading, 'fa-check text-success' : !item.visibleLoading && item.visible == '1', 'fa-times text-error': !item.visibleLoading && item.visible == '0' }"></i>
    </button>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('enabled')">
    <button class="btn btn-white" ng-click="patch(item, 'enabled', item.enabled != 1 ? 1 : 0)" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.enabledLoading, 'fa-check text-success' : !item.enabledLoading && item.enabled == '1', 'fa-times text-error': !item.enabledLoading && item.enabled == '0' }"></i>
    </button>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('rss')">
    <button class="btn btn-white" ng-click="patch(item, 'rss', item.rss != 1 ? 1 : 0)" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.rssLoading, 'fa-check text-success' : !item.rssLoading && item.rss == '1', 'fa-times text-error': !item.rssLoading && item.rss == '0' }"></i>
    </button>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('manual')">
    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.manualLoading, 'fa-check text-success' : !item.manualLoading && item.params.manual == '1', 'fa-times text-error': !item.manualLoading && item.params.manual != '1' }"></i>
  </td>
{/block}
