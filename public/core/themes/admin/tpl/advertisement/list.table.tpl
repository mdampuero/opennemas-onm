{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-title" checklist-model="app.columns.selected" checklist-value="'title'" type="checkbox">
    <label for="checkbox-featured-title">
      {t}Title{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-category" checklist-model="app.columns.selected" checklist-value="'category'" type="checkbox">
    <label for="checkbox-featured-category">
      {t}Category{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-starttime" checklist-model="app.columns.selected" checklist-value="'starttime'" type="checkbox">
    <label for="checkbox-featured-starttime">
      {t}Start date{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-endtime" checklist-model="app.columns.selected" checklist-value="'endtime'" type="checkbox">
    <label for="checkbox-featured-endtime">
      {t}End date{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-devices" checklist-model="app.columns.selected" checklist-value="'devices'" type="checkbox">
    <label for="checkbox-featured-devices">
      {t}Devices{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-position" checklist-model="app.columns.selected" checklist-value="'position'" type="checkbox">
    <label for="checkbox-featured-position">
      {t}Position{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-type" checklist-model="app.columns.selected" checklist-value="'type'" type="checkbox">
    <label for="checkbox-featured-type">
      {t}Type{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-published" checklist-model="app.columns.selected" checklist-value="'published'" type="checkbox">
    <label for="checkbox-featured-published">
      {t}Published{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" ng-if="isColumnEnabled('title')" width="400">
    {t}Title{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('category')" width="150">
    {t}Category{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('starttime')" width="150">
    {t}Start date{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('endtime')" width="150">
    {t}End date{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('devices')" width="150">
    {t}Devices{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('position')" width="150">
    {t}Position{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('type')" width="80">
    {t}Type{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('published')" width="150">
    {t}Published{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle" ng-if="isColumnEnabled('title')">
    <span class="small-text visible-xs-inline-block visible-sm-inline-block">
      <i class="fa fa-file-picture-o fa-lg m-r-5 text-success" ng-if="item.with_script == 0 && item.is_flash != 1" title="{t}Media element (jpg, png, gif){/t}"></i>
      <i class="fa fa-file-video-o fa-lg m-r-5 text-danger" ng-if="item.with_script == 0 && item.is_flash == 1" title="{t}Media flash element (swf){/t}"></i>
      <i class="fa fa-file-code-o fa-lg m-r-5 text-info" ng-if="item.with_script == 1" title="Javascript"></i>
      <i class="fa fa-gg fa-lg m-r-5 text-info" ng-if="item.with_script == 2" title="OpenX"></i>
      <i class="fa fa-google fa-lg m-r-5 text-danger" ng-if="item.with_script == 3" title="Google DFP"></i>
      <i class="fa fa-plus-square fa-lg m-r-5 text-warning" ng-if="item.with_script == 4" title="Smart Adserver"></i>
    </span>
    [% item.title %]
    <div class="table-text">
      <span class="hidden-lg">
        <span ng-show="item.positions.length > 1" tooltip-class="text-left" {* uib-tooltip-template="'ad_position_template'" *} tooltip-placement="bottom-left">{t 1="[% item.positions.length %]"}%1 positions{/t},</span>
        <span ng-show="item.positions.length == 1"><span ng-repeat="value in item.positions | limitTo:1">[% map[value].name %]</span>,</span>
        <span ng-show="item.positions.length == 0">{t}No positions assigned{/t},</span>
        <span ng-show="item.num_clic_count == 0">{t}No clicks{/t}</span>
        <span ng-show="item.num_clic_count > 0">{t 1="[% item.num_clic_count %]"}%1 clicks{/t}</span>
      </span>
    </div>
    <div class="listing-inline-actions m-t-10 btn-group">
      {block name="itemActions"}{/block}
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('category')">
    {block name="categoryColumn"}
      <small class="text-italic" ng-if="!item.fk_content_categories">
        {t}All{/t}
      </small>
      <div class="table-text">
      [% categories %]
        <a class="label label-default m-r-5 text-bold" href="[% routing.generate('backend_category_show', { id: item.fk_content_categories }) %]" ng-if="item.fk_content_categories">
          [% (categories | filter: { id: item.fk_content_categories } : true).title %]
        </a>
      </div>
    {/block}
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
  <td class="v-align-middle" ng-if="isColumnEnabled('devices')">
    <small class="text-italic">
      <span ng-if="item.params.devices.desktop" class="d-block">
        <i class="fa fa-desktop" title="Desktop"></i> Desktop
      </span>
      <span ng-if="item.params.devices.tablet" class="d-block">
        <i class="fa fa-tablet" title="Tablet"></i> Tablet
      </span>
      <span ng-if="item.params.devices.phone" class="d-block">
        <i class="fa fa-mobile" title="Phone"></i> Phone
      </span>
    </small>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('position')">
    <span ng-repeat="value in item.positions | limitTo:3">
      [% map[value].name %]
    </span>
    <span ng-show="item.positions.length > 3" tooltip-class="text-left" tooltip-placement="bottom-left">{t 1="[% item.positions.length %]"}%1 positions{/t}</span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('type')">
    <i class="fa fa-file-picture-o fa-lg m-r-5 text-success" ng-if="item.with_script == 0 && item.is_flash != 1" title="{t}Media element (jpg, png, gif){/t}"></i>
    <i class="fa fa-file-video-o fa-lg m-r-5 text-danger" ng-if="item.with_script == 0 && item.is_flash == 1" title="{t}Media flash element (swf){/t}"></i>
    <i class="fa fa-file-code-o fa-lg m-r-5 text-info" ng-if="item.with_script == 1" title="Javascript"></i>
    <i class="fa fa-gg fa-lg m-r-5 text-info" ng-if="item.with_script == 2" title="OpenX"></i>
    <i class="fa fa-google fa-lg m-r-5 text-danger" ng-if="item.with_script == 3" title="Google DFP"></i>
    <i class="fa fa-plus-square fa-lg m-r-5 text-warning" ng-if="item.with_script == 4" title="Smart Adserver"></i>
  </td>
  {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('published')">
    <button class="btn btn-white" ng-click="patchItem($index, item.id, 'backend_ws_content_set_content_status', 'content_status', item.content_status != 1 ? 1 : 0, 'loading')" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.loading, 'fa-check text-success' : !item.loading && item.content_status == '1', 'fa-times text-error': !item.loading && item.content_status == '0' }"></i>
    </button>
  </td>
  {/acl}
{/block}

<!-- If you want to add a new action, you can add it here -->
{block name="itemActions"}
  {acl isAllowed="ADVERTISEMENT_UPDATE"}
    <a class="btn btn-white btn-small" href="[% routing.generate('backend_advertisement_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
      <i class="fa fa-pencil"></i>
    </a>
    <translator item="data.items[$index]" keys="data.extra.keys" language="data.extra.locale.selected" link="[% routing.generate('backend_advertisement_show', { id: getItemId(item) }) %]" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" ng-class="{ 'dropup': $index >= data.items.length - 1 }" class="btn-group" options="data.extra.locale" text="{t}Edit{/t}"></translator>
  {/acl}
  <a class="btn btn-white btn-small" href="[% getFrontendUrl(item) %]" target="_blank" uib-tooltip="{t}Link{/t}" tooltip-placement="top">
    <i class="fa fa-external-link"></i>
  </a>
  {acl isAllowed="ADVERTISEMENT_DELETE"}
    <button class="btn btn-white btn-small" ng-click="sendToTrash(item)" type="button" uib-tooltip="{t}Remove{/t}" tooltip-placement="top">
      <i class="fa fa-trash-o text-danger"></i>
    </button>
  {/acl}
{/block}
