{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    .submitted-on {
        color: #777;
    }
</style>
{/block}


{block name="content"}
<form action="{url name=admin_comments_list}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('comment', { status: 'pending', body_like: '' }, 'date', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

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
                                <i class="fa fa-gear"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="page-navbar selected-navbar" ng-class="{ 'collapsed': shvs.selected.length == 0 }">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section pull-left">
                    <li class="quicklinks">
                      <button class="btn btn-link" ng-click="shvs.selected = []; selected.all = 0" tooltip="Clear selection" tooltip-placement="right"type="button">
                        <i class="fa fa-check fa-lg"></i>
                      </button>
                    </li>
                     <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h4>
                            [% shvs.selected.length %] {t}items selected{/t}
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
                    <li class="m-r-10 input-prepend inside search-input no-boarder">
                        <span class="add-on">
                            <span class="fa fa-search fa-lg"></span>
                        </span>
                        <input autofocus class="no-boarder" ng-model="shvs.search.body_like" placeholder="{t}Search by body{/t}" type="text">
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <select class="select2" name="status" ng-model="shvs.search.status" data-label="{t}Status{/t}">
                            <option value="-1">-- All --</option>
                            {html_options options=$statuses selected=$filter_status}
                        </select>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <span class="info">
                        {t}Results{/t}: [% shvs.total %]
                        </span>
                    </li>
                </ul>
                <ul class="nav quick-section pull-right">
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks form-inline pagination-links">
                        <div class="btn-group">
                            <button class="btn btn-white" ng-click="pagination.page = pagination.page - 1" ng-disabled="pagination.page - 1 < 1" type="button">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button class="btn btn-white" ng-click="pagination.page = pagination.page + 1" ng-disabled="pagination.page == pagination.pages" type="button">
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
                    <div class="spinner"></div>
                    <div class="spinner-text">{t}Loading{/t}...</div>
                </div>
                <div class="table-wrapper" ng-if="no-loading">
                    <table class="table table-hover table-condensed" ng-if="!loading">
                        <thead>
                            <tr>
                                <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                                <th>{t}Author{/t}</th>
                                <th>{t}Comment{/t}</th>
                                <th class="wrap">{t}In response to{/t}</th>
                                <th style='width:20px;' class="center">{t}Published{/t}</th>
                                <th style='width:10px;'></th>
                           </tr>
                        </thead>
                        <tbody>
                            <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                                <td>
                                    <checkbox index="[% content.id %]">
                                </td>
                                <td>
                                    <strong>[% content.author %]</strong><br>
                                    <small ng-if="content.author_email">
                                        [% content.author_email %]
                                    </small>
                                    <br>
                                    <small>[% content.author_ip %]</small>
                                </td>
                                <td class="left">
                                    <div class="submitted-on">{t}Submitted on:{/t} [% content.date | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]</div>
                                    <p>
                                        [% content.body | limitTo : 250 %]<span ng-if="content.body.length > 250">...</span>
                                    </p>
                                </td>
                                <td >
                                    [% shvs.extra.contents[content.content_id].title | limitTo : 100 %]<span ng-if="shvs.extra.contents[content.content_id].title.length > 250">...</span>
                                </td>
                                <td class="center">
                                    {acl isAllowed="COMMENT_AVAILABLE"}
                                        <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.status == 'accepted', unpublished: (content.status == 'rejected' || content.status == 'pending') }" ng-click="updateItem($index, content.id, 'backend_ws_comment_toggle_status', 'status', content.status != 'accepted' ? 'accepted' : 'rejected', 'loading')" type="button"></button>
                                    {/acl}
                                </td>
                                <td class="right">

                                    <div class="btn-group">
                                        {acl isAllowed="COMMENT_UPDATE"}
                                            <a class="btn" href="[% edit(content.id, 'admin_comment_show') %]" title="{t}Edit{/t}">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                        {/acl}
                                        {acl isAllowed="COMMENT_DELETE"}
                                            <button class="btn btn-danger" ng-click="open('modal-remove-permanently', 'backend_ws_comment_delete', $index)" type="button">
                                               <i class="fa fa-trash-o"></i>
                                            </button>
                                        {/acl}
                                    </div>
                                </td>
                            </tr>
                            <tr ng-if="shvs.contents.length == 0">
                                <td class="empty" colspan="6">
                                    {t}No comments matched your criteria.{/t}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="grid-footer" ng-if="no-loading">
                <div class="pagination-info pull-left" ng-if="shvs.contents.length > 0">
                    {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                </div>
                <div class="pull-right" ng-if="shvs.contents.length > 0">
                    <pagination class="no-margin" max-size="5" direction-links="true"  on-select-page="selectPage(page, 'backend_ws_contents_list')" ng-model="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
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
</form>
{/block}
