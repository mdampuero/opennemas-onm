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
            <a class="btn btn-success text-uppercase" ng-href="[% routing.ngGenerate('manager_notification_create') %]">
              <i class="fa fa-plus m-r-5"></i>
              {t}Create{/t}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
{include file='common/selected_navbar.tpl' list="notification"}
<div class="page-navbar filters-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <div class="input-group input-group-animated">
            <span class="input-group-addon">
              <i class="fa fa-search fa-lg"></i>
            </span>
            <input class="input-min-45 input-150" ng-class="{ 'dirty': criteria.title_like[0].value }" ng-keyup="searchByKeypress($event)" ng-model="criteria.title_like[0].value" placeholder="{t}Search by title{/t}" type="text">
            <span class="input-group-addon input-group-addon-inside pointer no-animate ng-hide" ng-click="criteria.title_like[0].value = null" ng-show="criteria.title_like[0].value">
              <i class="fa fa-times"></i>
            </span>
          </div>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
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
          <button class="btn btn-link" ng-click="resetFilters()" uib-tooltip="{t}Reset filters{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-fire fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="list()" uib-tooltip="{t}Reload{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-lg fa-refresh" ng-class="{ 'fa-spin': loading }"></i>
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
  <div class="p-b-100 p-t-100 text-center" ng-if="items.length == 0">
    <i class="fa fa-7x fa-user-secret"></i>
    <h2 class="m-b-50">{t}There is nothing to see here, kid.{/t}</h2>
  </div>
  <div class="grid simple" ng-if="items.length > 0">
    <div class="column-filters-toggle hidden-sm" ng-click="toggleColumns()" ng-if="items.length > 0"></div>
    <div class="column-filters collapsed hidden-sm" ng-class="{ 'collapsed': columns.collapsed }" ng-if="items.length > 0">
      <h5>{t}Columns{/t}</h5>
      <div class="row">
        <div class="col-sm-6 col-md-3 column">
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-title" checklist-model="columns.selected" checklist-value="'title'" type="checkbox">
            <label for="checkbox-title">
              {t}Title{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-instance" checklist-model="columns.selected" checklist-value="'instance'" type="checkbox">
            <label for="checkbox-instance">
              {t}Instance{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-type" checklist-model="columns.selected" checklist-value="'type'" type="checkbox">
            <label for="checkbox-type">
              {t}Type{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-style" checklist-model="columns.selected" checklist-value="'style'" type="checkbox">
            <label for="checkbox-style">
              {t}Style{/t}
            </label>
          </div>
        </div>
        <div class="col-sm-6 col-md-3 column">
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-l10n" checklist-model="columns.selected" checklist-value="'l10n'" type="checkbox">
            <label for="checkbox-l10n">
              {t}L10n{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-start" checklist-model="columns.selected" checklist-value="'start'" type="checkbox">
            <label for="checkbox-start">
              {t}Start{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-end" checklist-model="columns.selected" checklist-value="'end'" type="checkbox">
            <label for="checkbox-end">
              {t}End{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-fixed" checklist-model="columns.selected" checklist-value="'fixed'" type="checkbox">
            <label for="checkbox-fixed">
              {t}Fixed{/t}
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="grid-body no-padding">
      <div class="table-wrapper">
        <div class="grid-overlay" ng-if="loading"></div>
        <table class="table table-hover no-margin">
          <thead>
            <tr>
              <th width="15">
                <div class="checkbox checkbox-default">
                  <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                  <label for="select-all"></label>
                </div>
              </th>
              <th class="pointer" ng-click="sort('id')" width="50">
                {t}#{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc' }"></i>
              </th>
              <th class="pointer" ng-click="sort('title')" ng-show="isColumnEnabled('title')">
                {t}Title{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('title') == 'asc', 'fa fa-caret-down': isOrderedBy('title') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('instances')" ng-show="isColumnEnabled('instances')" width="130">
                {t}Instance{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('instances') == 'asc', 'fa fa-caret-down': isOrderedBy('instances') == 'desc'}"></i>
              </th>
              <th class="text-center" width="60">
                l10n
              </th>
              <th class="pointer text-center" ng-click="sort('start')" ng-show="isColumnEnabled('start')" width="250">
                {t}Start{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('start') == 'asc', 'fa fa-caret-down': isOrderedBy('start') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('end')" ng-show="isColumnEnabled('end')" width="250">
                {t}End{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('end') == 'asc', 'fa fa-caret-down': isOrderedBy('end') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('fixed')" ng-show="isColumnEnabled('fixed')" width="75">
                {t}Fixed{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('fixed') == 'asc', 'fa fa-caret-down': isOrderedBy('fixed') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('forced')" width="85">
                {t}Forced{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('forced') == 'asc', 'fa fa-caret-down': isOrderedBy('forced') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('enabled')" width="85">
                {t}Enabled{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('enabled') == 'asc', 'fa fa-caret-down': isOrderedBy('enabled') == 'desc'}"></i>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
              <td>
                <div class="checkbox check-default">
                  <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                  <label for="checkbox[%$index%]"></label>
                </div>
              </td>
              <td>
                [% item.id %]
              </td>
              <td ng-show="isColumnEnabled('title')">
                <a ng-href="[% item.show_url %]" title="{t}Edit{/t}">
                  [% item.title['en'] %]
                </a>
                <div class="listing-inline-actions">
                  <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_notification_show', { id: item.id }) %]" title="{t}Edit{/t}">
                    <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                  </a>
                  <button class="btn btn-link text-danger" ng-click="delete(item)" type="button">
                    <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                  </button>
                </div>
              </td>
              <td class="text-center" ng-show="isColumnEnabled('instances')">
                <div ng-repeat="id in item.instances">
                  [% extra.instances[id].name %]
                </div>
              </td>
              <td class="text-center">
                <span class="orb orb-success" ng-if="countStringsLeft(item) === 0" uib-tooltip="{t}Translations completed{/t}">
                  <i class="fa fa-check" ng-if="countStringsLeft(item) === 0"></i>
                </span>
                <span class="orb orb-danger" ng-if="countStringsLeft(item) > 0" uib-tooltip="[% countStringsLeft(item) %] {t}translations left{/t}">
                  [% countStringsLeft(item) %]
                </span>
              </td>
              <td class="text-center" ng-show="isColumnEnabled('start')">
                [% item.start %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('end')">
                [% item.end %]
              </td>
              <td class="text-center">
                <button class="btn btn-white" ng-click="patch(item, 'fixed', item.fixed == 1 ? 0 : 1)" type="button">
                  <i class="fa" ng-class="{ 'fa-lock text-success' : !item.fixedLoading && item.fixed == 1, 'fa-unlock text-error': !item.fixedLoading && item.fixed == 0, 'fa-circle-o-notch fa-spin': item.fixedLoading }"></i>
                </button>
              </td>
              <td class="text-center">
                <button class="btn btn-white" ng-click="patch(item, 'forced', item.forced == 1 ? 0 : 1)" type="button">
                  <i class="fa" ng-class="{ 'fa-eye text-success' : !item.forcedLoading && item.forced == 1, 'fa-eye-slash text-error': !item.forcedLoading && item.forced == 0, 'fa-circle-o-notch fa-spin': item.forcedLoading }"></i>
                </button>
              </td>
              <td class="text-center">
                <button class="btn btn-white" ng-click="patch(item, 'enabled', item.enabled == 1 ? 0 : 1)" type="button">
                  <i class="fa" ng-class="{ 'fa-check text-success' : !item.enabledLoading && item.enabled == 1, 'fa-times text-error': !item.enabledLoading && item.enabled == 0, 'fa-circle-o-notch fa-spin': item.enabledLoading }"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-footer clearfix">
      <div class="pull-right">
        <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
      </div>
    </div>
  </div>
</div>
<script type="text/ng-template" id="modal-confirm">
  {include file="common/modal_confirm.tpl"}
</script>
