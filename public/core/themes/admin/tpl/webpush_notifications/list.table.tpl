{extends file="common/extension/list.table.tpl"}

{block name="customColumns"}
    <div class="checkbox column-filters-checkbox">
      <input id="image" checklist-model="app.columns.selected" checklist-value="'image'" type="checkbox">
      <label for="image">
        {t}Image{/t}
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
{/block}

{block name="customColumnsHeader"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('image')" width="200">
      {t}Image{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('status')" width="200">
      {t}Status{/t}
    </th>
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('send_date')" width="200">
      {t}Send date{/t}
    </th>
{/block}

{block name="customColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('image')">
 <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.image" transform="zoomcrop,220,220">
      {* <div class="badge badge-default text-bold text-uppercase">
        {include file="common/component/icon/content_type_icon.tpl" iField="getFeaturedMedia(item, 'featured_frontpage')" iFlagName=true iFlagIcon=true}
      </div> *}
    </dynamic-image>
    [% item.image %]
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
{/block}


