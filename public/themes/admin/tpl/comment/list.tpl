{extends file="base/admin.tpl"}

{block name="content"}
<style>
  .table-wrapper {
    white-space: normal !important;
  }
  .table td {
    white-space: normal;
  }

  .comment-author-info, .comment-body-block {
    display:block;
    width:100%;
    clear:both;
  }
  .gravatar.img-thumbnail {
    min-width: 40px;
  }
</style>
  <div action="{url name=admin_comments_list}" ng-app="BackendApp" ng-controller="CommentListCtrl" ng-init="init(null, 'backend_ws_contents_list')">
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
              <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right"type="button">
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
              <button class="btn btn-link" ng-click="patchSelected('status', 'rejected')" uib-tooltip="{t}Reject{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-times fa-lg"></i>
              </button>
            </li>
            <li class="quicklinks">
              <button class="btn btn-link" ng-click="patchSelected('status', 'accepted')" uib-tooltip="{t}Accept{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-check fa-lg"></i>
              </button>
            </li>
            {/acl}
            {acl isAllowed="COMMENT_DELETE"}
            <li class="quicklinks">
              <button class="btn btn-link" ng-click="deleteSelected('backend_ws_comments_batch_delete')" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
              <input class="no-boarder" ng-model="criteria.body" placeholder="{t}Search by body{/t}" type="text">
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks ng-cloak" ng-init="statuses = {json_encode($statuses)|clear_json}">
              <ui-select name="status" theme="select2" ng-model="criteria.status" data-label="{t}Status{/t}">
                <ui-select-match>
                  <strong>{t}Status{/t}:</strong> [% $select.selected.title %]
                </ui-select-match>
                <ui-select-choices repeat="item.value as item in statuses | filter: $select.search">
                  <div ng-bind-html="item.title | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
            <li class="quicklinks ng-cloak hidden-xs">
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
          <ul class="nav quick-section pull-right ng-cloak hidden-xs" ng-if="contents.length > 0">
            <li class="quicklinks">
              <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
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
            <div class="text-center p-b-15 p-t-15">
              <i class="fa fa-4x fa-warning text-warning"></i>
              <h4>{t}Unable to find any comment that matches your search.{/t}</h4>
              <h6>{t}Maybe changing any filter could help.{/t}</h6>
            </div>
          </div>
          <div class="table-wrapper ng-cloak" ng-if="!loading">
            <table class="table table-hover no-margin" ng-if="contents.length > 0">
              <thead>
                <tr>
                  <th class="checkbox-cell" width="10px">
                    <div class="checkbox checkbox-default">
                      <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                      <label for="select-all"></label>
                    </div>
                  </th>
                  <th class="hidden-xs text-center" width="40"><i class="fa fa-picture-o"></i></th>
                  <th>{t}Comment{/t}</th>
                  <th class="text-right" width="10px">{t}Published{/t}</th>
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
                  <td class="text-center hidden-xs">
                    <gravatar class="gravatar img-thumbnail img-thumbnail-circle" ng-model="content.author_email" size="40"></gravatar>
                  </td>
                  <td>
                    <div class="comment-author-info row">
                      <small class="gravatar">
                        <div class="submitted-on">
                          <strong>{t}Author:{/t}</strong> [% content.author %] <span ng-if="content.author_email">([% content.author_email %])</span>
                          - <span class="hidden-xs">[% content.author_ip %]</span>
                        </div>
                        <div class="submitted-on"><strong>{t}Submitted on:{/t}</strong> [% content.date.date | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]</div>
                        <div class="on-response-to"><strong>{t}In response to{/t}:</strong> <a ng-href="/[% extra.contents[content.content_id].uri %]" target="_blank">[% extra.contents[content.content_id].title | limitTo : 100 %]<span ng-if="extra.contents[content.content_id].title.length > 100">...</span></a></div>
                      </small>
                    </div>
                    <div class="comment-body-block row">
                      <div ng-bind-html="content.body"></div>
                      <div class="listing-inline-actions">
                        {acl isAllowed="COMMENT_UPDATE"}
                        <a class="btn btn-defauilt btn-small" href="[% edit(content.id, 'admin_comment_show') %]" title="{t}Edit{/t}">
                          <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                        </a>
                        {/acl}
                        {acl isAllowed="COMMENT_DELETE"}
                        <button class="btn btn-danger btn-small" ng-click="delete(content, 'backend_ws_comment_delete')" type="button">
                        <i class="fa fa-trash-o m-r-5"></i>{t}Remove{/t}
                        </button>
                        {/acl}
                      </div>
                    </div>
                  </td>
                  <td class="text-center">
                    {acl isAllowed="COMMENT_AVAILABLE"}
                      <button class="btn btn-white" ng-class="{ statusLoading: content.statusLoading == 1, published: content.status == 'accepted', unpublished: (content.status == 'rejected' || content.status == 'pending') }" ng-click="patch(content, 'status', content.status != 'accepted' ? 'accepted' : 'rejected')" type="button">
                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.statusLoading, 'fa-check text-success' : !content.statusLoading && content.status == 'accepted', 'fa-times text-error': !content.statusLoading && (content.status == 'pending' || content.status == 'rejected') }"></i>
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
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
          </div>
        </div>
      </div>
    </div>
  </div>
    <script type="text/ng-template" id="modal-delete">
      {include file="comment/modals/_modalDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
      {include file="common/modals/_modalBatchDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-update-selected">
      {include file="common/modals/_modalBatchUpdate.tpl"}
    </script>
  </div>
{/block}
