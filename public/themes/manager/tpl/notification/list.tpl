<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_notifications_list') %]">
              <i class="fa fa-bell"></i>
              {t}Notifications{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-success" ng-href="[% routing.ngGenerate('manager_notification_create') %]">
              <i class="fa fa-plus fa-lg"></i>
              {t}Create{/t}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
{include file='common/selected_navbar.tpl' list="instance"}
<div class="page-navbar filters-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="m-r-10 input-prepend inside search-input no-boarder">
          <span class="add-on">
            <span class="fa fa-search fa-lg"></span>
          </span>
          <input class="no-boarder" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by title{/t}" ng-model="criteria.title_like[0].value" type="text" style="width:250px;"/>
        </li>
        <li class="quicklinks hidden-xs ng-cloak">
          <ui-select name="view" theme="select2" ng-model="pagination.epp">
            <ui-select-match>
              <strong>{t}View{/t}:</strong> [% $select.selected %]
            </ui-select-match>
            <ui-select-choices repeat="item in views | filter: $select.search">
              <div ng-bind-html="item | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
          </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="criteria = {  title_like: [ { value: '', operator: 'like' } ]}; orderBy = [ { name: 'title', value: 'desc' } ]; pagination = { page: 1, epp: 25 }">
            <i class="fa fa-trash-o fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="list()">
            <i class="fa fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': loading, 'fa-repeat': !loading }"></i>
          </button>
        </li>
      </ul>
      <ul class="nav quick-section pull-right">
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks form-inline pagination-links">
          <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="content">
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="table-wrapper">
        <div class="grid-overlay" ng-if="loading"></div>
        <table class="table table-hover no-margin">
          <thead ng-if="items.length >= 0">
            <tr>
              <th style="width:15px;">
                <div class="checkbox checkbox-default">
                  <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                  <label for="select-all"></label>
                </div>
              </th>
              <th class="pointer" ng-click="sort('id')" width="50">
                {t}#{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc' }"></i>
              </th>
              <th class="pointer" ng-click="sort('title')" ng-show="isColumnEnabled('name')">
                {t}Title{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('title') == 'asc', 'fa fa-caret-down': isOrderedBy('title') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('instance')" width="10">
                {t}Instance{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('instance') == 'asc', 'fa fa-caret-down': isOrderedBy('instance') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('type')" width="100" width=10>
                {t}Type{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('type') == 'asc', 'fa fa-caret-down': isOrderedBy('type') == 'desc'}"></i>
              </th>
              <th class="text-center" width="60">
                l10n
              </th>
              <th class="pointer text-center" ng-click="sort('start')" width="250">
                {t}Start{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('start') == 'asc', 'fa fa-caret-down': isOrderedBy('start') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('end')" width="250">
                {t}End{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('end') == 'asc', 'fa fa-caret-down': isOrderedBy('end') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('end')" width="10">
                {t}Fixed{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('end') == 'asc', 'fa fa-caret-down': isOrderedBy('end') == 'desc'}"></i>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr ng-if="items.length == 0">
              <td class="empty" colspan="10">{t}There is no available instances yet{/t}</td>
            </tr>
            <tr ng-if="items.length >= 0" ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
              <td>
                <div class="checkbox check-default">
                  <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                  <label for="checkbox[%$index%]"></label>
                </div>
              </td>
              <td>
                [% item.id %]
              </td>
              <td ng-show="isColumnEnabled('name')">
                <a ng-href="[% item.show_url %]" title="{t}Edit{/t}">
                  [% item.title['en'] %]
                </a>
                <div class="listing-inline-actions">
                  <a class="link" ng-href="[% routing.ngGenerate('manager_notification_show', { id: item.id }) %]" title="{t}Edit{/t}">
                    <i class="fa fa-pencil"></i>{t}Edit{/t}
                  </a>
                  <button class="link link-danger" ng-click="delete(item)" type="button">
                    <i class="fa fa-trash-o"></i>{t}Delete{/t}
                  </button>
                </div>
              </td>
              <td>
                <div ng-repeat="id in item.instances">
                  [% extra.instances[id].name %]
                </div>
              </td>
              <td class="text-center">
                <i class="fa text-[% item.style %] p-b-10 p-l-10 p-r-10 p-t-10" ng-class="{ 'fa-comment': item.type === 'comment', 'fa-database': item.type === 'media', 'fa-envelope': item.type === 'email', 'fa-support': item.type === 'help', 'fa-info': item.type !== 'comment' && item.type !== 'media' && item.type !== 'email' && item.type !== 'help' && item.type !== 'user', 'fa-users': item.type === 'user' }"></i>
              </td>
              <td class="text-center">
                <span class="orb orb-success" ng-if="countStringsLeft(item) === 0" tooltip="{t}Translations completed{/t}">
                  <i class="fa fa-check""countStringsLeft(item) === 0"></i>
                </span>
                <span class="orb orb-danger" ng-if="countStringsLeft(item) > 0" tooltip="[% countStringsLeft(item) %] {t}translations left{/t}">
                  [% countStringsLeft(item) %]
                </span>
              </td>
              <td class="text-center">
                [% item.start %]
              </td>
              <td class="text-center">
                [% item.end %]
              </td>
              <td>
                <button class="btn btn-white" type="button" ng-click="setEnabled(item, item.fixed == '1' ? '0' : '1')">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.loading, 'fa-check text-success' : !item.loading &&item.fixed == '1', 'fa-times text-error': !item.loading && item.fixed == '0' }"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-footer clearfix">
      <div class="pull-right" ng-if="items.length > 0">
        <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
      </div>
    </div>
  </div>
</div>
<script type="text/ng-template" id="modal-confirm">
  {include file="common/modal_confirm.tpl"}
</script>
