{extends file="common/extension/list.table.tpl"}

{block name="commonColumns" prepend}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-logo" checklist-model="app.columns.selected" checklist-value="'logo'" type="checkbox">
    <label for="checkbox-logo">
      {t}Logo{/t}
    </label>
    <input id="checkbox-featured-inner" checklist-model="app.columns.selected" checklist-value="'featured_inner'" type="checkbox">
    <label for="checkbox-featured-inner">
      {t}Featured in inner{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader" prepend}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('logo')" width="120">
    {t}Logo{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('featured_inner')" width="120">
    {t}Inner{/t}
  </th>
{/block}

{block name="commonColumnsBody" prepend}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('logo')">
    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" type="logo" ng-model="item" only-image="true" transform="zoomcrop,220,220">
    </dynamic-image>
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
  {acl isAllowed="COMPANY_AVAILABLE"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-published" checklist-model="app.columns.selected" checklist-value="'content_status'" type="checkbox">
      <label for="checkbox-published">
        {t}Published{/t}
      </label>
    </div>
  {/acl}
{/block}

{block name="customColumnsHeader"}
  {acl isAllowed="COMPANY_AVAILABLE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
      <span class="m-l-5">
        {t}Published{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="customColumnsBody"}
  {acl isAllowed="COMPANY_AVAILABLE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')">
      <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading == 1, 'fa-check text-success': !item.content_statusLoading == 1 && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading == 1 && item.content_status == 0 }"></i>
      </button>
    </td>
  {/acl}
{/block}

{block name="itemActions"}
  {acl isAllowed="COMPANY_UPDATE"}
    <a class="btn btn-white btn-small" href="[% routing.generate('backend_company_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
      <i class="fa fa-pencil"></i>
    </a>
    <translator item="data.items[$index]" keys="data.extra.keys" language="data.extra.locale.selected" link="[% routing.generate('backend_company_show', { id: getItemId(item) }) %]" ng-class="{ 'dropup': $index >= items.length - 1 }" class="btn-group" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
  {/acl}
  {acl isAllowed="COMPANY_ADMIN"}
    <button class="btn btn-white btn-small" ng-click="createCopy(item)" type="button" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available" uib-tooltip="{t}Duplicate{/t}" tooltip-placement="top">
      <i class="fa fa-copy"></i>
    </button>
  {/acl}
  <a ng-if="item.slug" class="btn btn-white btn-small" href="[% getFrontendUrl(item) %]" target="_blank" uib-tooltip="{t}Link{/t}" tooltip-placement="top">
    <i class="fa fa-external-link"></i>
  </a>
  <a ng-if="!item.slug" class="btn btn-white btn-small" disabled>
    <i class="fa fa-external-link"></i>
  </a>
  {acl isAllowed="COMPANY_DELETE"}
    <button class="btn btn-white btn-small" ng-click="sendToTrash(item)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
      <i class="fa fa-trash-o text-danger"></i>
    </button>
  {/acl}
{/block}

