{extends file="common/extension/list.table.tpl"}

{block name="customColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-size" checklist-model="app.columns.selected" checklist-value="'size'" type="checkbox">
    <label for="checkbox-size">
      {t}Size{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-resolution" checklist-model="app.columns.selected" checklist-value="'resolution'" type="checkbox">
    <label for="checkbox-resolution">
      {t}Resolution{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-media" checklist-model="app.columns.selected" checklist-value="'media'" type="checkbox">
    <label for="checkbox-media">
      {t}Media{/t}
    </label>
  </div>
{/block}

{block name="customColumnsHeader"}
  {acl isAllowed="PHOTO_SIZE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('resolution')" width="150">
      <span class="m-l-5">
        {t}Resolution{/t}
      </span>
    </th>
  {/acl}
  {acl isAllowed="PHOTO_RESOLUTION"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('size')" width="150">
      <span class="m-l-5">
        {t}Size{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="customMediaHeader"}
  {acl isAllowed="PHOTO_MEDIA"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('media')" width="150">
      <span class="m-l-5">
        {t}Media{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="customMediaColumn"}
    {acl isAllowed="PHOTO_MEDIA"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('media')" width="150">
      <div style="height: 120px; width: 120px; margin: auto;">
        <dynamic-image class="dynamic-img-thumbnail-wrapper img-thumbnail"  instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item" only-image="true" transform="zoomcrop,220,220"></dynamic-image>
      </div>
    </td>
    {/acl}
{/block}

{block name="customColumnsBody"}
  {acl isAllowed="PHOTO_SIZE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('resolution')">
      [% item.width %]X[% item.height %]
    </td>
  {/acl}
  {acl isAllowed="PHOTO_RESOLUTION"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('size')">
      [% item.size %] KB
    </td>
  {/acl}
{/block}
