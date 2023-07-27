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
    <div class="pointer" ng-model="item" ng-click="open('modal-image', item)">
      <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item" transform="zoomcrop,220,220"></dynamic-image>
    </div>
  </td>
{/block}

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
  {acl isAllowed="PHOTO_RESOLUTION"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('size')" width="150">
      <span class="m-l-5">
        {t}Size{/t}
      </span>
    </th>
  {/acl}
  {acl isAllowed="PHOTO_SIZE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('resolution')" width="150">
      <span class="m-l-5">
        {t}Resolution{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="customColumnsBody"}
  {acl isAllowed="PHOTO_RESOLUTION"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('size')">
      <span class="badge badge-default text-bold">
        [% item.size | number : 2 %] KB
      </span>
    </td>
  {/acl}
  {acl isAllowed="PHOTO_SIZE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('resolution')">
      <span class="badge badge-default text-bold">
        [% item.width %] x [% item.height %]
      </span>
    </td>
  {/acl}
{/block}

{block name="itemActions"}
  {acl isAllowed="PHOTO_UPDATE"}
    <a class="btn btn-white btn-small" href="[% routing.generate('backend_photo_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
      <i class="fa fa-pencil m-r-5"></i>
    </a>
    <translator item="data.items[$index]" keys="data.extra.keys" language="data.extra.locale.selected" link="[% routing.generate('backend_photo_show', { id: getItemId(item) }) %]" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" ng-class="{ 'dropup': $index >= data.items.length - 1 }" options="data.extra.locale" text="{t}Edit{/t}"></translator>
  {/acl}
  <a class="btn btn-white btn-small" href="{$smarty.const.INSTANCE_MEDIA}[% extra.paths.photo + item.path %]" type="button" target="_blank" uib-tooltip="{t}Link{/t}" tooltip-placement="top">
    <i class="fa fa-external-link m-r-5"></i>
  </a>
  <a class="btn btn-white btn-small" ng-click="launchPhotoEditor(item)" type="button" uib-tooltip="{t}Transform the image{/t}" tooltip-placement="top">
    <i class="fa fa-sliders m-r-5"></i>
  </a>
  {acl isAllowed="PHOTO_DELETE"}
    <button class="btn btn-white btn-small" ng-click="delete(item.pk_content)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
      <i class="fa fa-trash-o m-r-5 text-danger"></i>
    </button>
  {/acl}
{/block}
