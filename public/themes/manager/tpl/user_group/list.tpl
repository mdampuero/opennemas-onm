<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_user_groups_list') %]">
              <i class="fa fa-users"></i>
              {t}User groups{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks" ng-if="security.hasPermission('GROUP_CREATE')">
            <a class="btn btn-success text-uppercase" ng-href="[% routing.ngGenerate('manager_user_group_create') %]">
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
        <li class="quicklinks" ng-if="security.hasPermission('GROUP_UPDATE')">
          <button class="btn btn-link" ng-click="patchSelected('enabled', 0)" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-times fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks" ng-if="security.hasPermission('GROUP_UPDATE')">
          <button class="btn btn-link" ng-click="patchSelected('enabled', 1)" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-check fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks" ng-if="security.hasPermission('GROUP_DELETE')">
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
            <input class="input-min-45 input-250" ng-class="{ 'dirty': criteria.name }" ng-keyup="searchByKeypress($event)" ng-model="criteria.name" placeholder="{t}Search by name{/t}" type="text">
            <span class="input-group-addon input-group-addon-inside pointer no-animate" ng-click="clear('name')" ng-show="criteria.name">
              <i class="fa fa-times"></i>
            </span>
          </div>
        </li>
        <li class="quicklins">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks hidden-xs">
          <ui-select name="view" theme="select2" ng-model="criteria.epp">
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
              <th class="pointer" ng-click="sort('pk_user_group')" width="50">
                #
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('pk_user_group') == 'asc', 'fa fa-caret-down': isOrderedBy('pk_user_group') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('name')" ng-if="isColumnEnabled('name')">
                {t}Group name{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
              </th>
              <th class="text-center" width="150">
                <i class="fa fa-eye" uib-tooltip="{t}Visibility{/t}" tooltip-placement="left"></i>
                <span ng-if="isHelpEnabled()">{t}Visibility{/t}</span>
              </th>
              <th class="text-center" width="150">
                <i class="fa fa-check" uib-tooltip="{t}Enabled{/t}" tooltip-placement="left"></i>
                <span ng-if="isHelpEnabled()">{t}Enabled{/t}</span>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.pk_user_group) }">
              <td>
                <div class="checkbox check-default">
                  <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.pk_user_group" type="checkbox">
                  <label for="checkbox[%$index%]"></label>
                </div>
              </td>
              <td>
                [% item.pk_user_group %]
              </td>
              <td ng-if="isColumnEnabled('name')">
                [% item.name %]
                <div class="listing-inline-actions">
                  <a class="btn btn-default btn-small" ng-href="[% routing.ngGenerate('manager_user_group_show', { id: item.pk_user_group }); %]" ng-if="security.hasPermission('GROUP_UPDATE')">
                    <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                  </a>
                  <button class="btn btn-danger btn-small" ng-click="delete(item.pk_user_group)" ng-if="security.hasPermission('GROUP_DELETE')" type="button">
                    <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                  </button>
                </div>
              </td>
              <td class="text-center">
                <button class="btn btn-white" ng-click="patch(item, 'private', item.private != 1 ? 1 : 0)" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.privateLoading, 'fa-eye-slash text-error' : !item.privateLoading && item.private == 1, 'fa-eye text-success': !item.privateLoading && item.private == 0 }"></i>
                </button>
              </td>
              <td class="text-center">
                <button class="btn btn-white" ng-click="patch(item, 'enabled', item.enabled != 1 ? 1 : 0)" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.enabledLoading, 'fa-check text-success' : !item.enabledLoading && item.enabled == 1, 'fa-times text-error': !item.enabledLoading && item.enabled == 0 }"></i>
                </button>
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
