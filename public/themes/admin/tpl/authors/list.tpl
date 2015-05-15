{extends file="base/admin.tpl"}
{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init(null, { fk_user_group: 3 }, 'name', 'asc', 'backend_ws_users_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-user"></i>
              {t}Authors{/t}
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=admin_author_create}" title="{t}Create new author{/t}" accesskey="c">
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
          {acl isAllowed="AUTHOR_DELETE"}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deleteSelected('backend_ws_users_batch_delete')" tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
          <li class="quicklinks">
            <span class="h-seperate"></span>
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

    {render_messages}

    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="spinner-wrapper" ng-if="loading">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>

        <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
          <div class="center">
          <h4>{t}Unable to find any author that matches your search.{/t}</h4>
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
                <th class="center" style="width:20px;">{t}Avatar{/t}</th>
                <th class="left">{t}Full name{/t}</th>
                <th class="left hidden-xs" >{t}Biography{/t}</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                <td class="checkbox-cell">
                  <div class="checkbox check-default">
                    <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td class="center">
                  <dynamic-image instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="extra.photos[content.avatar_img_id].path_img" transform="thumbnail,50,50" ng-if="content.avatar_img_id != 0"></dynamic-image>
                  <gravatar ng-model="content.email" size="40" ng-if="!content.avatar_img_id || content.avatar_img_id == 0"></gravatar>
                </td>

                <td class="left">
                  [% content.name %]
                  <div class="listing-inline-actions">
                    {acl isAllowed="AUTHOR_UPDATE"}
                    <a class="link" href="[% edit(content.id, 'admin_author_show') %]" title="{t}Edit user{/t}">
                      <i class="fa fa-pencil"></i> {t}Edit{/t}
                    </a>
                    {/acl}
                    {acl isAllowed="AUTHOR_DELETE"}
                    <button class="link link-danger" ng-click="delete(content, 'backend_ws_user_delete')" type="button">
                      <i class="fa fa-trash-o"></i>
                      {t}Delete{/t}
                    </button>
                    {/acl}
                  </div>
                </td>
                <td class="left hidden-xs">
                  <span ng-if="content.is_blog == 1">
                    <strong>Blog </strong>:
                  </span>
                  <span ng-if="content.bio != ''">[% content.bio %]</span>
                  <span ng-if="content.bio == ''">{t}No biography set{/t}</span>
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
    {include file="common/modals/_modalDelete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-delete-selected">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>
</div>
{/block}
