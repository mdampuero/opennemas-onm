{extends file="common/extension/list.table.tpl"}

{block name="customColumns"}
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('user')">
    <input id="checkbox-theme" checklist-model="app.columns.selected" checklist-value="'user'" type="checkbox" disabled>
    <label for="checkbox-theme">
      {t}Author{/t}
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

{block name="customColumnsHeader"}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('user')" width="150">
    {t}Author{/t}
  </th>
  {acl isAllowed="LETTER_AVAILABLE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
      <span class="m-l-5">
        {t}Published{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="customColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('user')">
    <div class="small-text">
      <p> <strong>[% item.author %]</strong> </p>
      <p> [% item.email %] </p>
      <p ng-if="item.ip"> ([% item.ip %]) </p>
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
        <div class="btn-group open-on-hover" ng-class="{ 'dropup': $index >= items.length - 1 }">
          <button type="button"  class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" uib-tooltip="{t}Pending{/t}">
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

{block name="itemActions"}
  {acl isAllowed="LETTER_UPDATE"}
    <a class="btn btn-white btn-small" href="[% routing.generate('backend_letter_show', { id: getItemId(item) }) %]" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
      <i class="fa fa-pencil m-r-5"></i>
    </a>
  {/acl}
  <a class="btn btn-white btn-small" href="[% getFrontendUrl(item) %]" target="_blank" uib-tooltip="{t}Link{/t}" tooltip-placement="top">
    <i class="fa fa-external-link m-r-5"></i>
  </a>
  {acl isAllowed="LETTER_DELETE"}
    <button class="btn btn-white btn-small" ng-click="sendToTrash(item)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
      <i class="fa fa-trash-o m-r-5 text-danger"></i>
    </button>
  {/acl}
{/block}
