{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-app="BackendApp" ng-controller="UserGroupListCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-users m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                {t}User groups{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              {acl isAllowed="GROUP_CREATE"}
                <li>
                  <a class="btn btn-success text-uppercase" href="[% routing.generate('backend_user_group_create') %]">
                    <i class="fa fa-plus"></i>
                    {t}Create{/t}
                  </a>
                </li>
              {/acl}
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
            {acl isAllowed="GROUP_AVAILABLE"}
              <li class="quicklinks">
                <button class="btn btn-link" ng-click="patchSelected('enabled', 0)" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
                  <i class="fa fa-times fa-lg"></i>
                </button>
              </li>
              <li class="quicklinks">
                <button class="btn btn-link" ng-click="patchSelected('enabled', 1)" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
                  <i class="fa fa-check fa-lg"></i>
                </button>
              </li>
            {/acl}
            {acl isAllowed="GROUP_DELETE"}
              <li class="quicklinks hidden-xs">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-link" href="#" ng-click="deleteSelected('backend_ws_user_groups_delete')">
                  <i class="fa fa-trash-o fa-lg"></i>
                </button>
              </li>
            {/acl}
          </ul>
        </div>
      </div>
    </div>
    <div class="page-navbar filters-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="m-r-10 input-prepend inside search-input no-boarder">
              <span class="add-on">
                <span class="fa fa-search fa-lg"></span>
              </span>
              <input class="no-boarder" name="name" ng-model="criteria.name" placeholder="Search by name" type="text">
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-init="enabled = [ { name: '{t}Any{/t}', value: null}, { name: '{t}Enabled{/t}', value: 1}, { name: '{t}Disabled{/t}', value: 0 } ]">
              <ui-select name="enabled" theme="select2" ng-model="criteria.enabled">
                <ui-select-match>
                  <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
                </ui-select-match>
                <ui-select-choices repeat="item.value as item in enabled | filter: $select.search">
                  <div ng-bind-html="item.name | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-init="private = [ { name: '{t}Any{/t}', value: null}, { name: '{t}Private{/t}', value: 1}, { name: '{t}Public{/t}', value: 0 } ]">
              <ui-select name="private" theme="select2" ng-model="criteria.private">
                <ui-select-match>
                  <strong>{t}Visibility{/t}:</strong> [% $select.selected.name %]
                </ui-select-match>
                <ui-select-choices repeat="item.value as item in private | filter: $select.search">
                  <div ng-bind-html="item.name | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
            <li class="quicklinks hidden-xs ng-cloak">
              <ui-select name="view" theme="select2" ng-model="criteria.epp">
                <ui-select-match>
                  <strong>{t}View{/t}:</strong> [% $select.selected %]
                </ui-select-match>
                <ui-select-choices repeat="item in views | filter: $select.search">
                  <div ng-bind-html="item | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
          </ul>
          <ul class="nav quick-section pull-right ng-cloak" ng-if="items.length > 0">
            <li class="quicklinks hidden-xs">
              <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="listing-no-contents" ng-hide="!flags.http.loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && items.length == 0">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-warning text-warning"></i>
          <h3>{t}Unable to find any item that matches your search.{/t}</h3>
          <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
        </div>
      </div>
      <div class="grid simple ng-cloak" ng-if="!flags.http.loading && items.length > 0">
        <div class="grid-body no-padding">
          <div class="table-wrapper">
            <table class="table table-hover no-margin">
              <thead>
                <tr>
                  <th class="checkbox-cell" width="50">
                    <div class="checkbox checkbox-default">
                      <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                      <label for="select-all"></label>
                    </div>
                  </th>
                  <th>{t}Name{/t}</th>
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
                  <td class="checkbox-cell">
                    <div class="checkbox check-default">
                      <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.pk_user_group" type="checkbox">
                      <label for="checkbox[%$index%]"></label>
                    </div>
                  </td>
                  <td>
                    [% item.name %]
                    <div class="listing-inline-actions">
                      <a class="btn btn-default btn-small" href="[% routing.generate('backend_user_group_show', { id: item.pk_user_group }) %]">
                        <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                      </a>
                      <button class="btn btn-danger btn-small" ng-click="delete(item.pk_user_group)" type="button">
                        <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}</button>
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
        <div class="grid-footer clearfix ng-cloak">
          <div class="pull-right">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
          </div>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="modal-delete">
      {include file="base/modal/modal.delete.tpl"}
    </script>
  </div>
{/block}
