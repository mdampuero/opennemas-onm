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
