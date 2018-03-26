{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-app="BackendApp" ng-controller="UserListCtrl" ng-init="init();master = {if $app.user->isMaster()}true{else} false{/if}">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-user"></i>
                {t}Users{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-success text-uppercase" href="[% routing.generate('backend_user_create') %]">
                  <i class="fa fa-plus"></i>
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
              <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right"type="button">
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
            {acl isAllowed="USER_AVAILABLE"}
              <li class="quicklinks">
                <button class="btn btn-link" ng-click="confirm('activated', 0)" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
                  <i class="fa fa-times fa-lg"></i>
                </button>
              </li>
              <li class="quicklinks">
                <button class="btn btn-link" ng-click="confirm('activated', 1)" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
                  <i class="fa fa-check fa-lg"></i>
                </button>
              </li>
            {/acl}
            {acl isAllowed="USER_DELETE"}
              <li class="quicklinks hidden-xs">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-link" ng-click="deleteSelected('backend_ws_users_batch_delete')" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom">
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
              <input class="no-boarder" name="title" ng-model="criteria.name" placeholder="{t}Search by title{/t}" type="text"/>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs ng-cloak">
              <ui-select name="group" theme="select2" ng-model="criteria.user_group_id">
                <ui-select-match>
                  <strong>{t}User Group{/t}:</strong> [% $select.selected.name %]
                </ui-select-match>
                <ui-select-choices repeat="item.pk_user_group as item in toArray(addEmptyValue(data.extra.user_groups, 'pk_user_group'))  | filter: $select.search">
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
            <li class="quicklinks hidden-xs ng-cloak">
              <ui-select name="view" theme="select2" ng-model="criteria.epp">
                <ui-select-match>
                  <strong>{t}View{/t}:</strong> [% $select.selected %]
                </ui-select-match>
                <ui-select-choices repeat="item in views  | filter: $select.search">
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
          <h3>{t}Unable to find any user that matches your search.{/t}</h3>
          <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
        </div>
      </div>
      <div class="grid simple ng-cloak" ng-if="!flags.http.loading && items.length > 0">
        <div class="grid-body no-padding">
          <div class="table-wrapper">
            <table class="table table-hover no-margin">
              <thead>
                <tr>
                  <th class="checkbox-cell">
                    <div class="checkbox checkbox-default">
                      <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                      <label for="select-all"></label>
                    </div>
                  </th>
                  <th class="hidden-xs text-center" width="80"><i class="fa fa-picture-o"></i></th>
                  <th width="400">{t}Name{/t}</th>
                  <th class="hidden-xs" width="400">{t}Email{/t}</th>
                  <th class="hidden-sm hidden-xs" width="240">{t}Username{/t}</th>
                  <th class="hidden-sm hidden-xs">{t}User groups{/t}</th>
                  <th class="hidden-sm hidden-xs text-center" width="100">{t}Social{/t}</th>
                  <th class="text-center" width="50">{t}Enabled{/t}</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(items.id) }">
                  <td class="checkbox-cell">
                    <div class="checkbox check-default">
                      <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                      <label for="checkbox[%$index%]"></label>
                    </div>
                  </td>
                  <td class="text-center hidden-xs">
                    <dynamic-image class="img-thumbnail img-thumbnail-circle" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="data.extra.photos[item.avatar_img_id].path_img" ng-if="item.avatar_img_id"></dynamic-image>
                    <gravatar class="gravatar img-thumbnail img-thumbnail-circle" ng-model="item.email" size="60" ng-if="!item.avatar_img_id || item.avatar_img_id == 0"></gravatar>
                  </td>
                  <td>
                    <strong class="hidden-xs" ng-if="item.name">
                      [% item.name %]
                    </strong>
                    <i ng-if="!item.name">{t}Unknown{/t}</i>
                    <span class="visible-xs" ng-if="item.name">
                      <strong>{t}Name{/t}:</strong>
                      [% item.name%]
                    </span>
                    <span class="visible-xs">
                      <strong>{t}Email{/t}:</strong>
                      [% item.email%]
                    </span>
                    <div class="listing-inline-actions">
                      <a class="link" href="[% routing.generate('backend_user_show', { id: item.id }) %]">
                        <i class="fa fa-pencil"></i>{t}Edit{/t}
                      </a>
                      <button class="link link-danger" ng-click="delete(item.id)" type="button">
                        <i class="fa fa-trash-o"></i>{t}Delete{/t}
                      </button>
                    </div>
                  </td>
                  <td class="hidden-xs">
                    [% item.email %]
                  </td>
                  <td class="hidden-sm hidden-xs">
                    [% item.username %]
                  </td>
                  <td class="hidden-sm hidden-xs">
                    <ul class="no-style">
                      <li class="m-b-5 m-r-5 pull-left" ng-repeat="(id, user_group) in item.user_groups" ng-if="data.extra.user_groups[id] && user_group.status !== 0" uib-tooltip="{t}User group disabled{/t}" tooltip-enable="data.extra.user_groups[id].enabled === 0">
                        <a class="label text-uppercase" ng-class="{ 'label-danger': !data.extra.user_groups[id].enabled, 'label-default': data.extra.user_groups[id].enabled }" href="[% routing.generate('backend_user_group_show', { id: id }) %]">
                          <strong>[% data.extra.user_groups[id].name %]</strong>
                        </span>
                        </a>
                      </li>
                    </ul>
                  </td>
                  <td class="hidden-sm hidden-xs text-center">
                    <ul class="no-style">
                      <li ng-show="item.facebook_id">
                        <i class="fa fa-facebook-official fa-lg m-b-10 text-facebook"></i>
                      </li>
                      <li ng-show="item.google_id">
                        <i class="fa fa-google-plus-official fa-lg m-b-10 text-google"></i>
                      </li>
                      <li ng-show="item.twitter_id">
                        <i class="fa fa-twitter fa-lg m-b-5 text-twitter"></i>
                      </li>
                    </ul>
                  </td>
                  <td class="text-center">
                    <button class="btn btn-white" ng-click="confirm('activated', item.activated != 1 ? 1 : 0, item)" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.activatedLoading, 'fa-check text-success' : !item.activatedLoading && item.activated == '1', 'fa-times text-error': !item.activatedLoading && item.activated == '0' }"></i>
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
      {include file="user/modal.delete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-confirm">
      {include file="user/modal.confirm.tpl"}
    </script>
  </div>
{/block}
