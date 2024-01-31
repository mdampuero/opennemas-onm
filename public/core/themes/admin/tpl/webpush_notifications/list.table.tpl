{extends file="common/extension/list.table.tpl"}

{block name="itemActionsWrapper"}
{/block}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="image" checklist-model="app.columns.selected" checklist-value="'image'" type="checkbox">
    <label for="image">
      {t}Image{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('title')">
    <input id="checkbox-title" checklist-model="app.columns.selected" checklist-value="'title'" type="checkbox">
    <label for="checkbox-title">
      {t}Title{/t}
    </label>
  </div>
{/block}

{block name="customColumns"}
    <div class="checkbox column-filters-checkbox">
      <input id="transaction_id" checklist-model="app.columns.selected" checklist-value="'transaction_id'" type="checkbox">
      <label for="transaction_id">
        {t}Identificator{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="status" checklist-model="app.columns.selected" checklist-value="'status'" type="checkbox">
      <label for="status">
        {t}Status{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="send_date" checklist-model="app.columns.selected" checklist-value="'send_date'" type="checkbox">
      <label for="send_date">
        {t}Send date{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="total_sent" checklist-model="app.columns.selected" checklist-value="'total_sent'" type="checkbox">
      <label for="total_sent">
        {t}Total Sent{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="impressions" checklist-model="app.columns.selected" checklist-value="'impressions'" type="checkbox">
      <label for="impressions">
        {t}Impressions{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="clicks" checklist-model="app.columns.selected" checklist-value="'clicks'" type="checkbox">
      <label for="clicks">
        {t}Clicks{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="closed" checklist-model="app.columns.selected" checklist-value="'closed'" type="checkbox">
      <label for="closed">
        {t}Closed{/t}
      </label>
    </div>
    <div class="checkbox column-filters-checkbox">
      <input id="ctr" checklist-model="app.columns.selected" checklist-value="'ctr'" type="checkbox">
      <label for="ctr">
        {t}CTR{/t}
      </label>
    </div>
{/block}

{block name="commonColumnsHeader"}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('image' )" width="100">
    {t}Image{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('title')" width="400">
    {t}Title{/t}
  </th>
{/block}

{block name="customColumnsHeader"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('transaction_id')" width="150">
      {t}Transaction Id{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('status')" width="120">
      {t}Status{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('send_date')" width="150">
      {t}Send date{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('total_sent')" width="120">
      <i class="fa fa-info-circle text-info" uib-tooltip-html="'{t}Total notification sending attempts{/t}'" tooltip-placement="bottom"></i>
      {t}Total Sent{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('impressions')" width="155">
      <i class="fa fa-info-circle text-info" uib-tooltip-html="'{t}Times a notification was{/t}<br/>{t}displayed to the user{/t}'" tooltip-placement="bottom"></i>
      {t}Impressions{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('clicks')" width="120">
      <i class="fa fa-info-circle text-info" uib-tooltip-html="'{t}Times a notification was{/t}<br/>{t}clicked by the user{/t}'" tooltip-placement="bottom"></i>
      {t}Clicks{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('closed')" width="120">
      <i class="fa fa-info-circle text-info" uib-tooltip-html="'{t}Times a notification was{/t}<br/>{t}closed by the user{/t}'" tooltip-placement="bottom"></i>
      {t}Closed{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('ctr')" width="100">
      <i class="fa fa-info-circle text-info" uib-tooltip-html="'({t}Clicks + Closed{/t})<br>/ {t}Impressions{/t}'" tooltip-placement="bottom"></i>
      {t}CTR{/t}
    </th>
{/block}

{block name="commonColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('image')">
    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="data.extra.photos[item.image].path" transform="zoomcrop,220,220">
    </dynamic-image>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('title')">
    <div class="table-text">
      <a class="text-black" href="[% routing.generate('backend_article_show', { id: item.fk_content }) %]"">[% item.title %]</a>
    </div>
  </td>
{/block}

{block name="customColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('transaction_id')">
    <span>
      <code class="text-info" ng-if="item.transaction_id">
        <small>[% item.transaction_id %]</small>
      </code>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('status')">
    <span class="ng-cloak badge badge-default" ng-class="{ 'badge-danger': item.status == 2, 'badge-warning': item.status == 0, 'badge-success' : item.status == 1 }">
      <strong ng-if="item.status == 0">
        {t}Scheduled{/t}
      </strong>
      <strong ng-if="item.status == 1">
        {t}Sent{/t}
      </strong>
      <strong ng-if="item.status == 2">
        {t}Error{/t}
      </strong>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('send_date')">
    <span class="ng-cloak badge badge-default">
      <strong>
        [% item.send_date %]
      </strong>
    </span>
  </td>
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('total_sent')">
    <span>
      <strong>
        [% item.send_count | number : 0 %]
      </strong>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('impressions')">
    <span>
      <strong>
        [% item.impressions | number : 0 %]
      </strong>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('clicks')">
    <span>
      <strong>
        [% item.clicks | number : 0 %]
      </strong>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('closed')">
    <span>
      <strong>
        [% item.closed | number : 0 %]
      </strong>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('ctr')">
    <span ng-if="item.impressions > 0">
      <strong>
        [% ((item.clicks + item.closed) / item.impressions * 100) | number : 2 %] %
      </strong>
    </span>
  </td>
{/block}


