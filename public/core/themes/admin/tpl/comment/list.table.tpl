{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-avatar" checklist-model="app.columns.selected" checklist-value="'avatar'" type="checkbox">
    <label for="checkbox-avatar">
      {t}Avatar{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-comment" checklist-model="app.columns.selected" checklist-value="'comment'" type="checkbox">
    <label for="checkbox-comment">
      {t}Comment{/t}
    </label>
  </div>
  {acl isAllowed="COMMENT_AVAILABLE"}
    <div class="checkbox column-filters-checkbox">
      <input id="checkbox-published" checklist-model="app.columns.selected" checklist-value="'status'" type="checkbox">
      <label for="checkbox-published">
        {t}Published{/t}
      </label>
    </div>
  {/acl}
{/block}

{block name="commonColumnsHeader"}
  <th class="hidden-xs text-center" ng-if="isColumnEnabled('avatar')" width="40">
    <i class="fa fa-picture-o"></i>
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('comment')" width="400">
    {t}Comment{/t}
  </th>
  {acl isAllowed="COMMENT_AVAILABLE"}
    <th class="text-center v-align-middle" ng-if="isColumnEnabled('status')" width="150">
      <span class="m-l-5">
        {t}Published{/t}
      </span>
    </th>
  {/acl}
{/block}

{block name="commonColumnsBody"}
  <td class="hidden-xs text-center" ng-if="isColumnEnabled('avatar')">
    <gravatar class="gravatar img-thumbnail img-thumbnail-circle" ng-model="item.author_email" size="40" ></gravatar>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('comment')">
    <small class="gravatar">
      <div class="submitted-on">
        <strong>{t}Author:{/t}</strong> [% item.author %] <span ng-if="item.author_email">([% item.author_email %])</span>
        - <span class="hidden-xs">[% item.author_ip %]</span>
      </div>
      <div class="submitted-on"><strong>{t}Submitted on:{/t}</strong> [% item.date | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : extra.dateTimezone.timezone %]</div>
      <div class="on-response-to"><strong>{t}In response to{/t}:</strong> <a ng-href="/content/[% item.content_id %]" target="_blank">
      [% localizeText(extra.contents[item.content_id].title) %]
      <span ng-if="extra.contents[item.content_id].title.length > 100">...</span></a></div>
    </small>
    <div ng-bind-html="item.body"></div>
    <div class="listing-inline-actions">
      {acl isAllowed="COMMENT_UPDATE"}
        <a class="btn btn-defauilt btn-small" href="[% routing.generate('backend_comment_show', { id: getItemId(item) }) %]" title="{t}Edit{/t}">
          <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
        </a>
      {/acl}
      {acl isAllowed="COMMENT_DELETE"}
        <button class="btn btn-danger btn-small" ng-click="delete(item.id)" title="{t}Delete{/t}" type="button">
          <i class="fa fa-trash-o m-r-5"></i>{t}Remove{/t}
        </button>
      {/acl}
    </div>
  </td>
  {acl isAllowed="COMMENT_AVAILABLE"}
    <td class="text-center v-align-middle" ng-if="isColumnEnabled('status')">
      <span ng-show="item.status != 'pending'">
        <button class="btn btn-white" ng-class="{ statusLoading: item.statusLoading == 1, published: item.status == 'accepted', unpublished: (item.status == 'rejected' || item.status == 'pending') }" ng-click="patch(item, 'status', item.status != 'accepted' ? 'accepted' : 'rejected')" type="button" uib-tooltip-html="item.status !== 'accepted' ? '{t}Rejected{/t}' : '{t}Accepted{/t}'">
          <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.statusLoading, 'fa-check text-success' : !item.statusLoading && item.status == 'accepted', 'fa-times text-error': !item.statusLoading && (item.status == 'pending' || item.status == 'rejected') }"></i>
        </button>
      </span>
      <span ng-show="item.status == 'pending'">
        <div class="btn-group open-on-hover">
          <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" uib-tooltip="{t}Pending{/t}">
            <i class="fa fa-clock-o text-warning"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-right no-padding">
            <li><a href="#" ng-click="patch(item, 'status', 'rejected')"><i class="fa fa-times text-error"></i> {t}Reject{/t}</a> </li>
            <li><a href="#" ng-click="patch(item, 'status', 'accepted')"><i class="fa fa-check text-success"></i> {t}Accept{/t}</a></li>
          </ul>
        </div>
      </span>
    </td>
  {/acl}
{/block}
