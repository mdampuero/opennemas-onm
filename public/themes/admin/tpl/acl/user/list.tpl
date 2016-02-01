{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-app="BackendApp" ng-controller="UserListCtrl" ng-init="init(null, { name_like: '', fk_user_group: -1, type: -1, activated: -1 }, 'name', 'asc', 'backend_ws_users_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-user fa-lg"></i>
                {t}Users{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="quick-section">
              <li class="quicklinks">
                <a class="btn btn-white" href="{url name=backend_ws_users_download_list}">
                  <span class="fa fa-download"></span>
                  {t}Download{/t}
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <a class="btn btn-primary" href="{url name=admin_acl_user_create}">
                  <i class="fa fa-plus"></i>
                  {t}Create{/t}
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.contents.length == 0 }">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section pull-left">
            <li class="quicklinks">
              <button class="btn btn-link" ng-click="deselectAll()" tooltip="{t}Clear selection{/t}" tooltip-placement="right"type="button">
                <i class="fa fa-arrow-left fa-lg"></i>
              </button>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <h4>
                [% selected.contents.length %] <span class="hidden-xs">{t}items selected{/t}</span>
              </h4>
            </li>
          </ul>
          <ul class="nav quick-section pull-right">
            {acl isAllowed="USER_AVAILABLE"}
            <li class="quicklinks">
              <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_users_batch_set_enabled', 'activated', 0, 'loading')" tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
                <i class="fa fa-times fa-lg"></i>
              </button>
            </li>
            <li class="quicklinks">
              <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_users_batch_set_enabled', 'activated', 1, 'loading')" tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
                <i class="fa fa-check fa-lg"></i>
              </button>
            </li>
            {/acl}
            {acl isAllowed="USER_DELETE"}
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <button class="btn btn-link" ng-click="deleteSelected('backend_ws_users_batch_delete')" tooltip="{t}Delete{/t}" tooltip-placement="bottom">
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
              <input class="no-boarder" name="title" ng-model="criteria.name_like" placeholder="{t}Search by title{/t}" type="text"/>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-init="type = [ { name: '{t}All{/t}', value: -1}, { name: '{t}Backend{/t}', value: 0}, { name: '{t}Frontend{/t}', value: 1 } ]">
              <ui-select name="type" theme="select2" ng-model="criteria.type">
                <ui-select-match>
                  <strong>{t}Type{/t}:</strong> [% $select.selected.name %]
                </ui-select-match>
                <ui-select-choices repeat="item.value as item in type  | filter: $select.search">
                  <div ng-bind-html="item.name | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-init="groups = {json_encode($groups)|clear_json}">
              <ui-select name="group" theme="select2" ng-model="criteria.fk_user_group">
                <ui-select-match>
                  <strong>{t}Group{/t}:</strong> [% $select.selected.name %]
                </ui-select-match>
                <ui-select-choices repeat="item.value as item in groups  | filter: $select.search">
                  <div ng-bind-html="item.name | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-init="activated = [ { name: '{t}All{/t}', value: -1}, { name: '{t}Activated{/t}', value: 1}, { name: '{t}Deactivated{/t}', value: 0 } ]">
              <ui-select name="activated" theme="select2" ng-model="criteria.activated">
                <ui-select-match>
                  <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
                </ui-select-match>
                <ui-select-choices repeat="item.value as item in activated  | filter: $select.search">
                  <div ng-bind-html="item.name | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
            <li class="quicklinks hidden-xs ng-cloak">
              <ui-select name="view" theme="select2" ng-model="pagination.epp">
                <ui-select-match>
                  <strong>{t}View{/t}:</strong> [% $select.selected %]
                </ui-select-match>
                <ui-select-choices repeat="item in views  | filter: $select.search">
                  <div ng-bind-html="item | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
          </ul>
          <ul class="nav quick-section pull-right ng-cloak" ng-if="contents.length > 0">
            <li class="quicklinks hidden-xs">
              <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="grid simple">
        <div class="grid-body no-padding">
          <div class="spinner-wrapper" ng-if="loading">
            <div class="loading-spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
          </div>
          <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
            <div class="center">
              <h4>{t}Unable to find any user that matches your search.{/t}</h4>
              <h6>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h6>
            </div>
          </div>
          <div class="table-wrapper ng-cloak" ng-if="!loading && contents.length > 0">
            <table class="table table-hover no-margin">
              <thead>
                <tr>
                  <th class="checkbox-cell">
                    <div class="checkbox checkbox-default">
                      <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                      <label for="select-all"></label>
                    </div>
                  </th>
                  <th class=" hidden-xs" style="width:20px;">{t}Avatar{/t}</th>
                  <th class="left">{t}Full name{/t}</th>
                  <th class="center nowrap hidden-xs" style="width:110px">{t}Username{/t}</th>
                  <th class="center hidden-xs hidden-sm" >{t}E-mail{/t}</th>
                  <th class="center hidden-xs hidden-sm" >{t}Backend access{/t}</th>
                  <th class="center hidden-xs" >{t}Group{/t}</th>
                  <th class="center hidden-xs" >{t}Activated{/t}</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-if="contents.length > 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                  <td class="checkbox-cell">
                    <div class="checkbox check-default">
                      <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                      <label for="checkbox[%$index%]"></label>
                    </div>
                  </td>
                  <td class="center hidden-xs">
                    <dynamic-image instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="extra.photos[content.avatar_img_id].path_img" transform="thumbnail,50,50" ng-if="content.avatar_img_id != 0"></dynamic-image>
                    <gravatar class="gravatar" ng-model="content.email" size="40" ng-if="!content.avatar_img_id || content.avatar_img_id == 0"></gravatar>
                  </td>
                  <td class="left">
                    <strong>[% content.name %]</strong>
                    <span class="visible-xs visible-sm">([% content.email %])</span>

                    <div class="visible-xs">[% content.username %]</div>

                    <span ng-repeat="group in content.id_user_group" class="visible-xs">{t}Group{/t}: [% extra.groups[group].name %][% $last ? '' : ', ' %]</span>

                    <div class="listing-inline-actions">
                      <a class="link" href="[% edit(content.id, 'admin_acl_user_show') %]" title="{t}Edit user{/t}">
                        <i class="fa fa-pencil"></i> {t}Edit{/t}
                      </a>
                      <button class="link link-danger" ng-click="delete(content, 'backend_ws_user_delete')" type="button">
                        <i class="fa fa-trash-o"></i>
                        {t}Delete{/t}
                      </button>
                    </div>
                  </td>
                  <td class="center nowrap hidden-xs">
                    [% content.username %]
                  </td>

                  <td class="center hidden-xs hidden-sm">
                    [% content.email %]
                  </td>
                  <td class="center hidden-xs hidden-sm">
                    <span ng-if="content.type == 0">{t}Yes{/t}</span>
                    <span ng-if="content.type == 1">{t}No{/t}</span>
                  </td>
                  <td class="center hidden-xs">
                    <span ng-if="content.id_user_group == ''">{t}Not assigned{/t}</span>
                    <span ng-repeat="group in content.id_user_group">
                      [% extra.groups[group].name %][% $last ? '' : ', ' %]
                    </span>
                  </td>
                  <td class="center">
                    <button class="btn btn-white" ng-click="selected.contents = [ content.id ]; updateSelectedItems('backend_ws_users_batch_set_enabled', 'activated', content.activated != 1 ? 1 : 0, 'loading')" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading, 'fa-check text-success' : !content.loading && content.activated == '1', 'fa-times text-error': !content.loading && content.activated == '0' }"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="grid-footer clearfix ng-cloak" ng-if="!loading && contents.length > 0">
          <div class="pull-right">
            <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
          </div>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="modal-delete">
      {include file="acl/user/modals/_modalDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
      {include file="common/modals/_modalBatchDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-update-selected">
      {include file="acl/user/modals/_modalBatchUpdate.tpl"}
    </script>
  </form>
{/block}
