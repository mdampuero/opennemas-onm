{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('title')">
    <input id="checkbox-title" checklist-model="app.columns.selected" checklist-value="'title'" type="checkbox">
    <label for="checkbox-title">
      {t}Title{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('created')">
    <input id="checkbox-created" checklist-model="app.columns.selected" checklist-value="'created'" type="checkbox">
    <label for="checkbox-created">
      {t}Created{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('changed')">
    <input id="checkbox-changed" checklist-model="app.columns.selected" checklist-value="'changed'" type="checkbox">
    <label for="checkbox-changed">
      {t}Updated{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('starttime')">
    <input id="checkbox-starttime" checklist-model="app.columns.selected" checklist-value="'starttime'" type="checkbox">
    <label for="checkbox-starttime">
      {t}Start date{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('endtime')">
    <input id="checkbox-endtime" checklist-model="app.columns.selected" checklist-value="'endtime'" type="checkbox">
    <label for="checkbox-endtime">
      {t}End date{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('tags')">
    <input id="checkbox-contents" checklist-model="app.columns.selected" checklist-value="'tags'" type="checkbox">
    <label for="checkbox-contents">
      {t}Tags{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isColumnHidden('author')">
    <input id="checkbox-theme" checklist-model="app.columns.selected" checklist-value="'author'" type="checkbox">
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

{block name="commonColumnsHeader"}
  <th class="v-align-middle" ng-if="isColumnEnabled('title')" width="400">
    {t}Title{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('created')" width="150">
    {t}Created{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('changed')" width="150">
    {t}Updated{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('starttime')" width="150">
    {t}Start date{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('endtime')" width="150">
    {t}End date{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('tags')" width="200">
    {t}Tags{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('author')" width="200">
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

{block name="commonColumnsBody"}
  <td class="v-align-middle" ng-if="isColumnEnabled('title')">
    <div class="table-text">
      [% item.title %]
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
      <div class="btn-group" ng-class="{ 'dropup': $index >= items.length - 1 }">
        <button class="btn btn-small btn-white dropdown-toggle" data-toggle="dropdown" type="button">
          <i class="fa fa-ellipsis-h"></i>
        </button>
        <ul class="dropdown-menu no-padding">
          <li>
            <a href="[% getFrontendUrl(item) %]" target="_blank">
              <i class="fa fa-external-link m-r-5"></i>
              {t}Link{/t}
              <span class="m-l-5" ng-if="item.params.bodyLink.length > 0">
                <small>
                  ({t}External{/t})
                </small>
              </span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('created')">
    <div>
      <i class="fa fa-calendar"></i>
      [% item.created | moment : 'YYYY-MM-DD' %]
    </div>
    <small class="text-bold">
      <i class="fa fa-clock-o"></i>
      [% item.created | moment : 'HH:mm:ss' %]
    </small>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('changed')">
    <div>
      <i class="fa fa-calendar"></i>
      [% item.changed | moment : 'YYYY-MM-DD' %]
    </div>
    <small class="text-bold">
      <i class="fa fa-clock-o"></i>
      [% item.changed | moment : 'HH:mm:ss' %]
    </small>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('starttime')">
    <span class="text-bold" ng-if="!item.starttime">
      ∞
    </span>
    <div ng-if="item.starttime">
      <i class="fa fa-calendar"></i>
      [% item.starttime | moment : 'YYYY-MM-DD' %]
    </div>
    <small class="text-bold" ng-if="item.starttime">
      <i class="fa fa-clock-o"></i>
      [% item.starttime | moment : 'HH:mm:ss' %]
    </small>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('endtime')">
    <span class="text-bold" ng-if="!item.endtime">
      ∞
    </span>
    <div ng-if="item.endtime">
      <i class="fa fa-calendar"></i>
      [% item.endtime | moment : 'YYYY-MM-DD' %]
    </div>
    <small class="text-bold" ng-if="item.endtime">
      <i class="fa fa-clock-o"></i>
      [% item.endtime | moment : 'HH:mm:ss' %]
    </small>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('tags')">
    <small class="text-italic" ng-if="!item.tags || item.tags.length === 0">
      &lt;{t}No tags{/t}&gt;
    </small>
    <div class="inline m-r-5 m-t-5" ng-repeat="id in item.tags" ng-if="!(data.extra.tags | filter : { id: id })[0].locale || (data.extra.tags | filter : { id: id })[0].locale === config.locale.selected">
      <a class="label label-defaul label-info text-bold" href="[% routing.generate('backend_tag_show', { id: (data.extra.tags | filter : { id: id })[0].id }) %]">
        [% (data.extra.tags | filter : { id: id })[0].name %]
      </a>
    </div>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('author')">
    <div class="small-text">
      [% item.author %] ([% item.email %])
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
