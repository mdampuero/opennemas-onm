{extends file="common/extension/list.table.tpl"}

{block name="commonColumns" prepend}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-media" checklist-model="app.columns.selected" checklist-value="'media'" type="checkbox">
    <label for="checkbox-media">
      {t}Media{/t}
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
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('media')" width="80">
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('featured_inner')" width="120">
    {t}Inner{/t}
  </th>
{/block}

{block name="commonColumnsBody" prepend}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('media')">
    <div class="chart-container" style="height: 100%; position: relative; width: 100%;" ng-if="item.items && item.items.length > 0">
      <canvas class="chart chart-pie" chart-data="chart.data[$index]" chart-labels="chart.labels[$index]" chart-options="options"></canvas>
    </div>
    <small class="text-italic" ng-if="!item.items || item.items.length === 0">
      -
    </small>
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
    <input id="checkbox-votes" checklist-model="app.columns.selected" checklist-value="'votes'" type="checkbox">
    <label for="checkbox-votes">
      {t}Votes{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-close" checklist-model="app.columns.selected" checklist-value="'close'" type="checkbox">
    <label for="checkbox-close">
      {t}Vote end date{/t}
    </label>
  </div>
  {acl isAllowed="POLL_HOME"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-home" checklist-model="app.columns.selected" checklist-value="'home'" type="checkbox">
      <label for="checkbox-home">
        {t}Home{/t}
      </label>
    </div>
  {/acl}
  {acl isAllowed="POLL_FAVORITE"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-favorite" checklist-model="app.columns.selected" checklist-value="'favorite'" type="checkbox">
      <label for="checkbox-favorite">
        {t}Favorite{/t}
      </label>
    </div>
  {/acl}
  {acl isAllowed="POLL_AVAILABLE"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-published" checklist-model="app.columns.selected" checklist-value="'content_status'" type="checkbox">
      <label for="checkbox-published">
        {t}Published{/t}
      </label>
    </div>
  {/acl}
{/block}

{block name="customColumnsHeader"}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('votes')" width="80">
    <span class="m-l-5">
      {t}Votes{/t}
    </span>
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('close')" width="150">
    <span class="m-l-5">
      {t}Vote end date{/t}
    </span>
  </th>
  {acl isAllowed="POLL_HOME"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('home')" width="150">
      <span class="m-l-5">
        {t}Home{/t}
      </span>
    </th>
  {/acl}
  {acl isAllowed="POLL_FAVORITE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('favorite')" width="150">
      <span class="m-l-5">
        {t}Favorite{/t}
      </span>
    </th>
  {/acl}
  {acl isAllowed="POLL_AVAILABLE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
      <span class="m-l-5">
        {t}Published{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="customColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('votes')">
    <span class="badge badge-default">
      <strong>
        [% data.extra.total_votes[item.pk_content] %]
      </strong>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('close')">
    <strong ng-if="!item.closetime">
      ∞
    </strong>
    <div ng-if="item.closetime">
      <i class="fa fa-calendar"></i>
      [% item.closetime | moment : 'YYYY-MM-DD' %]
    </div>
    <small ng-if="item.closetime">
      <i class="fa fa-clock-o"></i>
      <strong>
        [% item.closetime | moment : 'HH:mm:ss' %]
      </strong>
    </small>
  </td>
  {acl isAllowed="POLL_HOME"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('home')">
      <button class="btn btn-white" ng-click="patch(item, 'in_home', item.in_home != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.in_homeLoading == 1, 'fa-home text-info': item.in_homeLoading !== 1 && item.in_home == 1, 'fa-home': !item.in_homeLoading == 1 && item.in_home == 0 }"></i>
        <i class="fa fa-times fa-sub text-danger" ng-if="item.in_homeLoading !== 1 && item.in_home == 0"></i>
      </button>
    </td>
  {/acl}
  {acl isAllowed="POLL_FAVORITE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('favorite')">
      <button class="btn btn-white" ng-click="patch(item, 'favorite', item.favorite != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.favoriteLoading == 1, 'fa-star text-warning': !item.favoriteLoading == 1 && item.favorite == 1, 'fa-star-o': !item.favoriteLoading == 1 && item.favorite != 1 }"></i>
      </button>
    </td>
  {/acl}
  {acl isAllowed="POLL_AVAILABLE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')">
      <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading == 1, 'fa-check text-success': !item.content_statusLoading == 1 && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading == 1 && item.content_status == 0 }"></i>
      </button>
    </td>
  {/acl}
{/block}

{block name="itemActions"}
  {acl isAllowed="POLL_UPDATE"}
    <a class="btn btn-white btn-small" href="[% routing.generate('backend_poll_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
      <i class="fa fa-pencil text-success_"></i>
    </a>
    <translator item="data.items[$index]" keys="data.extra.keys" language="data.extra.locale.selected" link="[% routing.generate('backend_poll_show', { id: getItemId(item) }) %]" ng-class="{ 'dropup': $index >= data.items.length - 1 }" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
  {/acl}
  <a class="btn btn-white btn-small" href="[% getFrontendUrl(item) %]" target="_blank" uib-tooltip="{t}Link{/t}" tooltip-placement="top">
    <i class="fa fa-external-link"></i>
    <span class="m-l-5" ng-if="item.params.bodyLink.length > 0">
      <small>
        {t}External{/t}
      </small>
    </span>
  </a>
  {acl isAllowed="POLL_DELETE"}
    <button class="btn btn-white btn-small" ng-click="sendToTrash(item)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
      <i class="fa fa-trash-o text-danger"></i>
    </button>
  {/acl}
{/block}
