{extends file="base/admin.tpl"}

{block name="content"}
<div action="{url name=admin_letters}" method="GET" name="formulario" id="formulario"  ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init('letter', { content_status: -1, in_home: {if $category == 'widget'}1{else}-1{/if}, title_like: '', in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-envelope"></i>
              <span class="hidden-xs">{t}Letters to the Editor{/t}</span> <span class="visible-xs-inline">{t}Letters{/t}</span>
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="LETTER_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=admin_letter_create}" class="admin_add" accesskey="N" tabindex="1">
                <span class="fa fa-plus"></span> {t}Create{/t}
              </a>
            </li>
            {/acl}
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="page-navbar selected-navbar collapsed" class="hidden" ng-class="{ 'collapsed': selected.contents.length == 0 }">
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
          {acl isAllowed="LETTER_AVAILABLE"}
          <li class="quicklinks">
            <button class="btn btn-link" id="batch-publish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" tooltip="{t}Publish{/t}" tooltip-placement="bottom">
              <i class="fa fa-check fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" id="batch-unpublish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" tooltip="{t}Unpublish{/t}" tooltip-placement="bottom">
              <i class="fa fa-times fa-lg"></i>
            </button>
          </li>
          {/acl}
          {acl isAllowed="LETTER_DELETE"}
          <li class="quicklinks"><span class="h-seperate"></span></li>
          <li class="quicklinks">
            <button class="btn btn-link" id="batch-delete" ng-click="sendToTrashSelected()" tooltip="{t}Delete{/t}" tooltip-placement="bottom">
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
            <input class="no-boarder" name="title" ng-model="criteria.title_like" placeholder="{t}Search by title{/t}" type="text"/>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs ng-cloak" ng-init="status = [ { name: '{t}All{/t}', value: -1 }, { name: '{t}Published{/t}', value: 1 }, { name: '{t}No published{/t}', value: 0 } ]">
            <ui-select name="status" theme="select2" ng-model="criteria.content_status">
              <ui-select-match>
                <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.value as item in status | filter: { name: $select.search }">
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
            <h4>{t}Unable to find any letter that matches your search.{/t}</h4>
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
                <th style="width: 100px;" class="text-center hidden-xs">{t}Image{/t}</th>
                <th>{t}Title{/t}</th>
                <th style="width: 110px;" class="left hidden-xs">{t}Author{/t}</th>
                {acl isAllowed="LETTER_AVAILABLE"}
                <th class="center" style="width:10px;">{t}Published{/t}</th>
                {/acl}
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
                <td class="center hidden-xs">
                  <span ng-if="content.image">
                    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="content.photo.path_img" transform="thumbnail,120,120"></dynamic-image>
                  </span>
                </td>
                <td>
                  <span class="center visible-xs" ng-if="content.image" style="max-width:80px">
                    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="content.photo.path_img" transform="thumbnail,120,120"></dynamic-image>
                  </span>
                  <span tooltip="[% content.body | striptags | limitTo: 140 %]...">[% content.title %]</span>
                  <div class="small-text">
                    <strong>{t}Date{/t}:</strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                  </div>
                  <div class="listing-inline-actions">
                    {acl isAllowed="LETTER_UPDATE"}
                    <a class="link" href="[% edit(content.id, 'admin_letter_show') %]">
                      <i class="fa fa-pencil"></i>
                      {t}Edit{/t}
                    </a>
                    {/acl}

                    {acl isAllowed="LETTER_AVAILABLE"}
                    <a class="link pointer" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', 2, 'loading')" ng-if="content.content_status != 2" type="button" title="{t}Reject{/t}">
                      <i class="fa fa-ban"></i>
                      {t}Reject{/t}
                    </a>
                    {/acl}

                    {acl isAllowed="LETTER_DELETE"}
                    <button class="del link link-danger" ng-click="sendToTrash(content)" type="button">
                      <i class="fa fa-trash-o"></i>
                      {t}Delete{/t}
                    </button>
                    {/acl}
                  </div>
                </td>
                <td class="center nowrap hidden-xs">
                  [% content.author %] ([% content.email %])
                </td>
                <td class="right">
                  {acl isAllowed="LETTER_AVAILABLE"}
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" title="{t}Publish/Unpublish{/t}" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading && content.content_status == 1, 'fa-times text-danger': !content.loading && content.content_status != 1 }"></i>
                  </button>
                  {/acl}
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

  <script type="text/ng-template" id="modal-update-selected">
    {include file="common/modals/_modalBatchUpdate.tpl"}
  </script>

</div>
{/block}
