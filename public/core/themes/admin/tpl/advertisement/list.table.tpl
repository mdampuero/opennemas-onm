{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('title')">
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
    <input id="checkbox-featured-dimensions" checklist-model="app.columns.selected" checklist-value="'dimensions'" type="checkbox">
    <label for="checkbox-featured-dimensions">
      {t}Dimensions{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-position" checklist-model="app.columns.selected" checklist-value="'position'" type="checkbox">
    <label for="checkbox-featured-position">
      {t}Position{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-clicks" checklist-model="app.columns.selected" checklist-value="'clicks'" type="checkbox">
    <label for="checkbox-featured-clicks">
      {t}Clicks{/t}
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
  <th class="v-align-middle" ng-if="isColumnEnabled('title')" width="300">
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
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('dimensions')" width="150">
    {t}Dimensions{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('position')" width="250">
    {t}Position{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('clicks')" width="75">
    {t}Clicks{/t}
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
    <div class="table-text">
      [% item.title %]
      <span class="hidden-lg">
        <span ng-show="item.positions.length > 1" tooltip-class="text-left" {* uib-tooltip-template="'ad_position_template'" *} tooltip-placement="bottom-left">{t 1="[% item.positions.length %]"}%1 positions{/t},</span>
        <span ng-show="item.positions.length == 1"><span ng-repeat="value in item.positions | limitTo:1">[% map[value].name %]</span>,</span>
        <span ng-show="item.positions.length == 0">{t}No positions assigned{/t},</span>
        <span ng-show="item.num_clic_count == 0">{t}No clicks{/t}</span>
        <span ng-show="item.num_clic_count > 0">{t 1="[% item.num_clic_count %]"}%1 clicks{/t}</span>
      </span>
    </div>
    <div class="listing-inline-actions m-t-10 btn-group">
      {acl isAllowed="ADVERTISEMENT_UPDATE"}
        <a class="btn btn-white btn-small" href="[% routing.generate('admin_advertisement_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
          <i class="fa fa-pencil"></i>
        </a>
        <translator item="data.items[$index]" keys="data.extra.keys" language="data.extra.locale.selected" link="[% routing.generate('backend_advertisement_show', { id: getItemId(item) }) %]" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" ng-class="{ 'dropup': $index >= data.items.length - 1 }" class="btn-group" options="data.extra.locale" text="{t}Edit{/t}"></translator>
      {/acl}
      {acl isAllowed="ADVERTISEMENT_ADMIN"}
          <button class="btn btn-white btn-small" ng-click="createCopy(item)" type="button" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Duplicate{/t}" tooltip-placement="top">
            <i class="fa fa-copy"></i>
          </button>
        {/acl}
      {acl isAllowed="ADVERTISEMENT_DELETE"}
        <button class="btn btn-white btn-small" ng-click="sendToTrash(item)" type="button" uib-tooltip="{t}Remove{/t}" tooltip-placement="top">
          <i class="fa fa-trash-o text-danger"></i>
        </button>
      {/acl}
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('category')">
    {block name="categoryColumn"}
      <small class="text-italic" ng-if="!item.fk_content_categories || item.fk_content_categories.length == 0">
        {t}All{/t}
      </small>
      <small ng-if="item.fk_content_categories.length >= 1">
        {t}Custom{/t}
      </small>
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
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('dimensions')">
    <small class="text-italic">
      <!-- Desktop -->
      <span ng-if="item.params.devices.desktop" class="d-block" style="opacity: 1;">
        <i class="fa fa-desktop" title="Desktop"></i>
        <span ng-repeat="d in item.params.sizes" ng-if="d.device === 'desktop'">
          [% d.width %]x[% d.height %]
        </span>
        <br />
      </span>
      <!-- Tablet -->
      <span ng-if="item.params.devices.tablet" class="d-block" style="opacity: 1;">
        <i class="fa fa-tablet fa-1x" title="Tablet"></i>
        <span ng-repeat="d in item.params.sizes" ng-if="d.device === 'tablet'">
          [% d.width %]x[% d.height %]
        </span>
        <br />
      </span>
      <!-- Phone -->
      <span ng-if="item.params.devices.phone" class="d-block" style="opacity: 1;">
        <i class="fa fa-mobile fa-1x" title="Phone"></i>
        <span ng-repeat="d in item.params.sizes" ng-if="d.device === 'phone'">
          [% d.width %]x[% d.height %]
        </span>
      </span>
    </small>
  </td>
  <td class="ads-listing hidden-xs hidden-sm small-text" ng-if="isColumnEnabled('position')">
    <span ng-repeat="value in item.positions | limitTo:3" class="ad-position">[% map[value].name %]</span>
    <span ng-show="item.positions.length > 3" {* uib-tooltip-template="'ad_position_template'" tooltip-placement="bottom" *}>{t 1="[% item.positions.length - 3 %]"}And %1 more…{/t}</span>
  </td>
  <td class="hidden-xs text-center" ng-if="isColumnEnabled('clicks')">
    [% item.num_clic_count %]
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
