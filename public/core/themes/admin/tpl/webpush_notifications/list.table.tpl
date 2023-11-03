{extends file="common/extension/list.table.tpl"}

{block name="customColumns"}
    <div class="checkbox column-filters-checkbox">
      <input id="image" checklist-model="app.columns.selected" checklist-value="'image'" type="checkbox">
      <label for="image">
        {t}Image{/t}
      </label>
    </div>
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

{block name="customColumnsHeader"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('image' )" width="200">
      {t}Image{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('transaction_id')" width="200">
      {t}Transaction Id{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('status')" width="200">
      {t}Status{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('send_date')" width="200">
      {t}Send date{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('impressions')" width="150">
      {t}Impressions{/t}
      <i class="fa fa-info-circle text-info" uib-tooltip-html="'{t}Times a notification was{/t}<br>{t}displayed to the user{/t}'" tooltip-placement="bottom"></i>
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('clicks')" width="150">
      {t}Clicks{/t}
      <i class="fa fa-info-circle text-info" uib-tooltip-html="'{t}Times a notification was{/t}<br>{t}clicked by the user{/t}'" tooltip-placement="bottom"></i>
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('closed')" width="150">
      {t}Closed{/t}
      <i class="fa fa-info-circle text-info" uib-tooltip-html="'{t}Times a notification was{/t}<br>{t}closed by the user{/t}'" tooltip-placement="bottom"></i>
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('ctr')" width="150">
      {t}CTR{/t}
      <i class="fa fa-info-circle text-info" uib-tooltip-html="'{t}Interactions (Clicks + Closed){/t}<br>{t}divided by Impressions{/t}'" tooltip-placement="bottom"></i>
    </th>
{/block}
{block name="customColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('image')">
  <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.image" transform="zoomcrop,220,220">
  </dynamic-image>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('transaction_id')">
    <span>
      <strong>
        [% item.transaction_id %]
      </strong>
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
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('impressions')">
    <span>
      <strong>
        [% item.impressions | number:0 %]
      </strong>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('clicks')">
    <span>
      <strong>
        [% item.clicks %]
      </strong>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('closed')">
    <span>
      <strong>
        [% item.closed %]
      </strong>
    </span>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('ctr')">
    <span>
      <strong>
        [%  ((item.clicks + item.closed) / item.impressions * 100) | number:0 %] %
      </strong>
    </span>
  </td>
{/block}


