{extends file="common/extension/list.table.tpl"}

{block name="itemActions"}
  <div class="listing-inline-actions btn-group">
    {acl isAllowed="WIDGET_UPDATE"}
      <a class="btn btn-white btn-small" href="[% routing.generate('backend_widget_show', { id: getItemId(item) }) %]" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
        <i class="fa fa-pencil m-r-5"></i>
      </a>
    {/acl}
    {acl isAllowed="WIDGET_DELETE"}
      <button class="btn btn-white btn-small" ng-click="sendToTrash(item)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
        <i class="fa fa-trash-o m-r-5 text-danger">
        </i>
      </button>
    {/acl}
  </div>
{/block}

{block name="commonColumns" prepend}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-media" checklist-model="app.columns.selected" checklist-value="'media'" type="checkbox">
    <label for="checkbox-media">
      {t}Type{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader" prepend}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('media')" width="80">
    {t}Type{/t}
  </th>
{/block}

{block name="commonColumnsBody" prepend}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('media')">
    <i class="fa fa-lg fa-code" ng-if="!item.widget_type" uib-tooltip="HTML"></i>
    <i class="fa fa-lg fa-cog" ng-if="item.widget_type" uib-tooltip="{t}IntelligentWidget{/t}"></i>
  </td>
{/block}

{block name="customColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-content" checklist-model="app.columns.selected" checklist-value="'content'" type="checkbox">
    <label for="checkbox-content">
      {t}Content{/t}
    </label>
  </div>
  {acl isAllowed="WIDGET_AVAILABLE"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-published" checklist-model="app.columns.selected" checklist-value="'content_status'" type="checkbox">
      <label for="checkbox-published">
        {t}Published{/t}
      </label>
    </div>
  {/acl}
{/block}

{block name="customColumnsHeader" prepend}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('content')" width="150">
    {t}Content{/t}
  </th>
  {acl isAllowed="WIDGET_AVAILABLE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
      <span class="m-l-5">
        {t}Published{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="customColumnsBody" prepend}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('content')">
    <span class="label label-default" ng-if="item.widget_type && item.class">
      <strong>
        [% item.class %]
      </strong>
    </span>
    <small class="text-italic" ng-if="item.widget_type && !item.class">
      &lt;{t}Not selected{/t}&gt;
    </small>
    <span ng-if="!item.widget_type">
      -
    </span>
  </td>
  {acl isAllowed="WIDGET_AVAILABLE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')">
      <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading == 1, 'fa-check text-success': !item.content_statusLoading == 1 && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading == 1 && item.content_status == 0 }"></i>
      </button>
    </td>
  {/acl}
{/block}
