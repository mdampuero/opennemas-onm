{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
  .submitted-on {
    color: #777;
  }
</style>
{/block}


{block name="content"}
<div action="{url name=admin_comments_list}" ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init('comment', { status: 'pending', body_like: '' }, 'date', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-comment"></i>
              {t}Comments{/t}
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_comments_config}" title="{t}Config comments module{/t}">
                <i class="fa fa-gear fa-lg"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="page-navbar selected-navbar collapsed ng-cloak" ng-class="{ 'collapsed': selected.contents.length == 0 }">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section pull-left">
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deselectAll()" tooltip="Clear selection" tooltip-placement="right"type="button">
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
          {acl isAllowed="COMMENT_AVAILABLE"}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_comments_batch_toggle_status', 'status', 'rejected', 'loading')" tooltip="{t}Reject{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-times fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_comments_batch_toggle_status', 'status', 'accepted', 'loading')" tooltip="{t}Accept{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-check fa-lg"></i>
            </button>
          </li>
          {/acl}
          {acl isAllowed="COMMENT_DELETE"}
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="open('modal-delete-selected', 'backend_ws_comments_batch_delete')" tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
          <li class="m-r-10 input-prepend inside search-input no-boarder hidden-xs">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input autofocus class="no-boarder" ng-model="criteria.body_like" placeholder="{t}Search by body{/t}" type="text">
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <select class="select2" name="status" ng-model="criteria.status" data-label="{t}Status{/t}">
              <option value="-1">-- All --</option>
              {html_options options=$statuses selected=pending}
            </select>
          </li>
          <li class="quicklinks hidden-xs">
            <select class="select2 input-medium" name="status" ng-model="pagination.epp" data-label="{t}View{/t}">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </li>
        </ul>
        <ul class="nav quick-section pull-right simple-pagination ng-cloak" ng-if="contents.length > 0">
          <li class="quicklinks hidden-xs">
            <span class="info">
              [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
            </span>
          </li>
          <li class="quicklinks form-inline pagination-links">
            <div class="btn-group">
              <button class="btn btn-white" ng-click="goToPrevPage()" ng-disabled="isFirstPage()" type="button">
                <i class="fa fa-chevron-left"></i>
              </button>
              <button class="btn btn-white" ng-click="goToNextPage()" ng-disabled="isLastPage()" type="button">
                <i class="fa fa-chevron-right"></i>
              </button>
            </div>
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
            <h4>{t}Unable to find any comment that matches your search.{/t}</h4>
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
                <th>{t}Comment{/t}</th>
                <th class="wrap hidden-xs">{t}In response to{/t}</th>
                <th style='width:10px;' class="center">{t}Published{/t}</th>
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
                <td>
                  <div class="submitted-on">{t}Author: {/t}<strong>[% content.author %]</strong> (<span ng-if="content.author_email">[% content.author_email %]</span>) - <span class="hidden-xs">[% content.author_ip %]</span></div>
                  <div class="submitted-on">{t}Submitted on:{/t} [% content.date | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]</div>
                  <p>
                    [% content.body | limitTo : 250 %]<span ng-if="content.body.length > 250">...</span>
                  </p>
                  <div class="listing-inline-actions">
                    {acl isAllowed="COMMENT_UPDATE"}
                    <a class="link" href="[% edit(content.id, 'admin_comment_show') %]" title="{t}Edit{/t}">
                      <i class="fa fa-pencil"></i> {t}Edit{/t}
                    </a>
                    {/acl}
                    {acl isAllowed="COMMENT_DELETE"}
                    <button class="link link-danger" ng-click="open('modal-remove-permanently', 'backend_ws_comment_delete', $index)" type="button">
                     <i class="fa fa-trash-o"></i> {t}Remove{/t}
                   </button>
                   {/acl}
                 </div>
               </td>
               <td class="hidden-xs">
                [% extra.contents[content.content_id].title | limitTo : 100 %]<span ng-if="extra.contents[content.content_id].title.length > 250">...</span>
              </td>
              <td class="right">
                {acl isAllowed="COMMENT_AVAILABLE"}
                <button class="btn btn-white" ng-class="{ loading: content.loading == 1, published: content.status == 'accepted', unpublished: (content.status == 'rejected' || content.status == 'pending') }" ng-click="updateItem($index, content.id, 'backend_ws_comment_toggle_status', 'status', content.status != 'accepted' ? 'accepted' : 'rejected', 'loading')" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading, 'fa-check text-success' : !content.loading && content.status == 'accepted', 'fa-times text-error': !content.loading && (content.status == 'pending' || content.status == 'rejected') }"></i>
                </button>
                {/acl}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-footer clearfix ng-cloak" ng-if="!loading && contents.length > 0">
      <div class="pagination-info pull-left">
        {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
      </div>
      <div class="pull-right pagination-wrapper">
        <pagination class="no-margin" max-size="5" direction-links="true" ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total" num-pages="pagination.pages"></pagination>
      </div>
    </div>
  </div>
</div>

<script type="text/ng-template" id="modal-remove-permanently">
  {include file="common/modals/_modalRemovePermanently.tpl"}
</script>
<script type="text/ng-template" id="modal-delete-selected">
  {include file="common/modals/_modalBatchDelete.tpl"}
</script>
<script type="text/ng-template" id="modal-update-selected">
  {include file="common/modals/_modalBatchUpdate.tpl"}
</script>
</div>
{/block}
