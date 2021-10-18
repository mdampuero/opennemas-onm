{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-frontpage" checklist-model="app.columns.selected" checklist-value="'featured_frontpage'" type="checkbox">
    <label for="checkbox-featured-frontpage">
      {t}Featured in frontpage{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-featured-inner" checklist-model="app.columns.selected" checklist-value="'featured_inner'" type="checkbox">
    <label for="checkbox-featured-inner">
      {t}Featured in inner{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-title" checklist-model="app.columns.selected" checklist-value="'title'" type="checkbox">
    <label for="checkbox-title">
      {t}Title{/t}
    </label>
  </div>
  {acl isAllowed="LETTER_AVAILABLE"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-published" checklist-model="app.columns.selected" checklist-value="'content_status'" type="checkbox">
      <label for="checkbox-published">
        {t}Published{/t}
      </label>
    </div>
  {/acl}
{/block}

{block name="commonColumnsHeader"}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('featured_frontpage')" width="120">
    {t}Frontpage{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('featured_inner')" width="120">
    {t}Inner{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('title')" width="400">
    {t}Title{/t}
  </th>
  {acl isAllowed="LETTER_AVAILABLE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
      <span class="m-l-5">
        {t}Published{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="commonColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('featured_frontpage')">
    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" type="featured_frontpage" ng-model="item" only-image="true" transform="zoomcrop,220,220">
      <div class="badge badge-default text-bold text-uppercase">
        {include file="common/component/icon/content_type_icon.tpl" iField="getFeaturedMedia(item, 'featured_frontpage')" iFlagName=true iFlagIcon=true}
      </div>
    </dynamic-image>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('featured_inner')">
    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" type="featured_inner" ng-model="item" only-image="true" transform="zoomcrop,220,220">
      <div class="badge badge-default text-bold text-uppercase">
        {include file="common/component/icon/content_type_icon.tpl" iField="getFeaturedMedia(item, 'featured_inner')" iFlagName=true iFlagIcon=true}
      </div>
    </dynamic-image>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('title')">
    <span uib-tooltip="[% item.body | striptags | limitTo: 140 %]...">[% item.title %]</span>
    <div class="small-text">
      <strong>{t}Date{/t}:</strong> [% item.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
    </div>
    <div class="small-text">
      <strong>{t}Author{/t}:</strong> [% item.author %] ([% item.email %])
    </div>
    <div class="listing-inline-actions">
      {acl isAllowed="LETTER_UPDATE"}
        <a class="btn btn-default btn-small" href="[% routing.generate('backend_letter_show', { id: getItemId(item) }) %]">
          <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
        </a>
      {/acl}
      {acl isAllowed="LETTER_DELETE"}
        <button class="btn btn-danger btn-small" ng-click="sendToTrash(item)" type="button">
          <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
        </button>
      {/acl}
    </div>
  </td>
  {acl isAllowed="LETTER_AVAILABLE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')">
      <span ng-show="item.content_status != 2">
        <button class="btn btn-white" ng-class="{ statusLoading: item.content_statusLoading == 1, published: item.content_status == 1, unpublished: (item.content_status == 2 || item.content_status == 0) }" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button" uib-tooltip-html="item.content_status !== 1 ? '{t}Rejected{/t}' : '{t}Accepted{/t}'">
          <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading, 'fa-check text-success' : !item.content_statusLoading && item.content_status == 1, 'fa-times text-error': !item.content_statusLoading && item.content_status == 0 }"></i>
        </button>
      </span>
      <span ng-show="item.content_status == 2">
        <div class="btn-group open-on-hover">
          <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" uib-tooltip="{t}Pending{/t}">
            <i class="fa fa-clock-o text-warning"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-right no-padding">
            <li><a href="#" ng-click="patch(item, 'content_status', 0)"><i class="fa fa-times text-error"></i> {t}Reject{/t}</a> </li>
            <li><a href="#" ng-click="patch(item, 'content_status', 1)"><i class="fa fa-check text-success"></i> {t}Accept{/t}</a></li>
          </ul>
        </div>
      </span>
    </td>
  {/acl}
{/block}

