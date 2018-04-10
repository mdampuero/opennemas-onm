<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_users_list') %]">
              <i class="fa fa-user"></i>
              {t}Users{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks" ng-if="security.hasPermission('USER_CREATE')">
            <a class="btn btn-success text-uppercase" ng-href="[% routing.ngGenerate('manager_user_create') %]">
              <i class="fa fa-plus m-r-5"></i>
              {t}Create{/t}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.items.length == 0 }">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section pull-left">
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right" type="button">
            <i class="fa fa-arrow-left fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <h4>
            [% selected.items.length %] <span class="hidden-xs">{t}items selected{/t}</span>
          </h4>
        </li>
      </ul>
      <ul class="nav quick-section pull-right">
        <li class="quicklinks" ng-if="security.hasPermission('USER_UPDATE')">
          <button class="btn btn-link" ng-click="patchSelected('activated', 0)" uib-tooltip="{t}Disabled{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-times fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks" ng-if="security.hasPermission('USER_UPDATE')">
          <button class="btn btn-link" ng-click="patchSelected('activated', 1)" uib-tooltip="{t}Enabled{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-check fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks" ng-if="security.hasPermission('USER_UPDATE') && security.hasPermission('USER_DELETE')">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks" ng-if="security.hasPermission('USER_DELETE')">
          <button class="btn btn-link" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-trash-o fa-lg"></i>
          </button>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="page-navbar filters-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <div class="input-group input-group-animated">
            <span class="input-group-addon">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.name }" ng-keyup="searchByKeypress($event)" ng-model="criteria.name" placeholder="{t}Search by name or username{/t}" type="text">
            <span class="input-group-addon input-group-addon-inside pointer no-animate" ng-click="clear('name')" ng-show="criteria.name">
              <i class="fa fa-times"></i>
            </span>
          </div>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <ui-select ng-model="criteria.user_group_id" theme="select2">
            <ui-select-match>
              <strong>{t}User Group{/t}:</strong> [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="item.pk_user_group as item in toArray(addEmptyValue(extra.user_groups, 'pk_user_group'))">
              <div ng-bind-html="item.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </li>
        <li class="quicklinks hidden-xs ng-cloak" ng-init="activated = [ { name: '{t}Any{/t}', value: null}, { name: '{t}Enabled{/t}', value: 1}, { name: '{t}Disabled{/t}', value: 0 } ]">
          <ui-select name="activated" theme="select2" ng-model="criteria.activated">
            <ui-select-match>
              <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="item.value as item in activated  | filter: $select.search">
              <div ng-bind-html="item.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </li>
        <li class="quicklinks hidden-xs">
          <ui-select name="view" ng-model="criteria.epp" theme="select2" >
            <ui-select-match>
              <strong>{t}View{/t}:</strong> [% $select.selected %]
            </ui-select-match>
            <ui-select-choices repeat="item in views | filter: $select.search">
              <div ng-bind-html="item | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="resetFilters()" uib-tooltip="{t}Reset filters{/t}" tooltip-placement="bottom">
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
        <li class="quicklinks form-inline pagination-links">
          <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="items">
  <div class="p-b-100 p-t-100 text-center" ng-if="items.length == 0">
    <i class="fa fa-7x fa-user-secret"></i>
    <h2 class="m-b-50">{t}There is nothing to see here, kid.{/t}</h2>
  </div>
  <div class="grid simple" ng-if="items.length > 0">
    <div class="column-filters-toggle hidden-sm" ng-click="toggleColumns()"></div>
    <div class="column-filters collapsed hidden-sm" ng-class="{ 'collapsed': columns.collapsed }">
      <h5>{t}Columns{/t}</h5>
      <div class="row">
        <div class="col-sm-6 col-md-3 column">
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
            <label for="checkbox-name">
              {t}Name{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-username" checklist-model="columns.selected" checklist-value="'username'" type="checkbox">
            <label for="checkbox-username">
              {t}Username{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-usergroups" checklist-model="columns.selected" checklist-value="'usergroups'" type="checkbox">
            <label for="checkbox-usergroups">
              {t}User groups{/t}
            </label>
          </div>
          <div class="checkbox check-default p-b-5">
            <input id="checkbox-enabled" checklist-model="columns.selected" checklist-value="'enabled'" type="checkbox">
            <label for="checkbox-enabled">
              {t}Enabled{/t}
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="grid-body no-padding">
      <div class="table-wrapper">
        <div class="grid-overlay" ng-if="loading"></div>
        <table class="table no-margin">
          <thead>
            <tr>
              <th width="15">
                <div class="checkbox checkbox-default">
                  <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                  <label for="select-all"></label>
                </div>
              </th>
              <th class="pointer" ng-click="sort('id')" width="50">
                #
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('name')" ng-if="isColumnEnabled('name')">
                {t}Full name{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('username')" ng-if="isColumnEnabled('username')" width="300">
                {t}Username{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('username') == 'asc', 'fa fa-caret-down': isOrderedBy('username') == 'desc'}"></i>
              </th>
              <th ng-if="isColumnEnabled('usergroups')" width="250">{t}Group{/t}</th>
              <th class="text-center pointer" width="10" ng-click="sort('enabled')" ng-if="isColumnEnabled('enabled')">{t}Enabled{/t}</th>
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
              <td ng-if="isColumnEnabled('name')">
                [% item.name %]
                <div class="listing-inline-actions">
                  <a class="btn btn-default btn-small" ng-href="[% routing.ngGenerate('manager_user_show', { id: item.id }); %]" ng-if="security.hasPermission('USER_UPDATE')">
                    <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                  </a>
                  <button class="btn btn-danger btn-small" ng-click="delete(item.id)" ng-if="security.hasPermission('USER_DELETE')" type="button">
                    <i class="fa fa-trash m-r-5"></i>{t}Delete{/t}
                  </button>
                </div>
              </td>
              <td ng-if="isColumnEnabled('username')">
                [% item.username %]
              </td>
              <td ng-if="isColumnEnabled('usergroups')">
                <ul class="no-style">
                  <li class="m-b-5 m-r-5 pull-left" ng-repeat="(id, user_group) in item.user_groups" ng-if="extra.user_groups[id] && user_group.status !== 0" uib-tooltip="{t}User group disabled{/t}" tooltip-enable="extra.user_groups[id].enabled === 0">
                    <a class="label text-uppercase" ng-class="{ 'label-danger': !extra.user_groups[id].enabled, 'label-default': extra.user_groups[id].enabled }" href="[% routing.generate('backend_user_group_show', { id: id }) %]">
                      <strong>[% extra.user_groups[id].name %]</strong>
                    </span>
                    </a>
                  </li>
                </ul>
              </td>
              <td class="text-center" ng-if="isColumnEnabled('enabled')">
                <button class="btn btn-white" ng-click="patch(item, 'activated', item.activated == '1' ? '0' : '1')" ng-if="security.hasPermission('USER_UPDATE')">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.activatedLoading, 'fa-check text-success' : !item.activatedLoading && item.activated== '1', 'fa-times text-error': !item.activatedLoading && item.activated== '0' }"></i>
                </button>
                <span ng-if="!security.hasPermission('USER_UPDATE')">
                  <i class="fa" ng-class="{ 'fa-check text-success' : item.activated== '1', 'fa-times text-error': item.activated== '0' }"></i>
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-footer clearfix">
      <div class="pull-right">
        <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
      </div>
    </div>
  </div>
</div>
