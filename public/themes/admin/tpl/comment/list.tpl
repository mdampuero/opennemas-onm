{extends file="base/admin.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="header-css" append}
<style type="text/css">
    .submitted-on {
        color: #777;
    }
</style>
{/block}


{block name="content"}
<form action="{url name=admin_comments_list}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('comment', { status: -1, body_like: '' }, 'date', 'desc', 'backend_ws_contents_list')">
    <div class="top-action-bar clearfix" class="clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>
                    {t}Comments{/t}
                </h2>
            </div>
            <ul class="old-button">
                <li ng-if="shvs.selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="ARTICLE_AVAILABLE"}
                        <li>
                            <a href="#" id="batch-publish" ng-click="batchToggleStatus('accepted', 'backend_ws_comments_batch_toggle_status')">
                                <i class="icon-check"></i>
                                {t}Accept{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="batch-unpublish" ng-click="batchToggleStatus('rejected', 'backend_ws_comments_batch_toggle_status')">
                                <i class="icon-remove"></i>
                                {t}Reject{/t}
                            </a>
                        </li>
                        {/acl}
                        {acl isAllowed="ARTICLE_DELETE"}
                            <li class="divider"></li>
                            <li>
                                <a href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_comments_batch_delete')">
                                    <i class="icon-trash"></i>
                                    {t}Delete{/t}
                                </a>
                            </li>
                        {/acl}
                    </ul>
                </li>
                <li class="separator" ng-if="shvs.selected.length > 0"></li>
                <li>
                    <a href="{url name=admin_comments_config}" title="{t}Config comments module{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}/template_manager/configure48x48.png" alt="{t}Settings{/t}"><br>
                        {t}Settings{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div class="table-info clearfix">
            <div class="pull-left form-inline">
                <strong>{t}FILTER:{/t}</strong>
                &nbsp;&nbsp;
                <input type="text" autofocus placeholder="{t}Search by body{/t}" ng-model="shvs.search.body_like">
                &nbsp;&nbsp;
                <select class="select2" name="status" ng-model="shvs.search.status" data-label="{t}Status{/t}">
                    <option value="-1">-- All --</option>
                    {html_options options=$statuses selected=$filter_status}
                </select>
            </div>
        </div>
        <div ng-include="'comments'"></div>

    </div>
    <script type="text/ng-template" id="comments">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
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
                <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected($index) }">
                    <td>
                        <checkbox index="[% $index %]">
                    </td>
                    <td>
                        <strong>[% content.author %]</strong><br>
                        <a href="mailto:[% content.author_email%]" ng-if="content.author_email">
                            [% content.author_email %]
                        </a>
                        <br>
                        [% content.author_ip %]
                    </td>
                    <td class="left">
                        <div class="submitted-on">{t}Submitted on:{/t} [% content.date.date | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]</div>
                        <p>
                            [% content.body | limitTo : 250 %]<span ng-if="content.body.length > 250">...</span>
                            {*$comment->body|strip_tags|clearslash|truncate:250:"..."*}
                        </p>
                    </td>
                    <td >
                        [% content.content.title %]
                    </td>
                    <td class="center">
                        {acl isAllowed="COMMENT_AVAILABLE"}
                            <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.status == 'accepted', unpublished: (content.status == 'rejected' || content.status == 'pending') }" ng-click="toggleStatus(content.id, $index, 'backend_ws_comment_toggle_status')" type="button"></button>
                        {/acl}
                    </td>
                    <td class="right">

                        <div class="btn-group">
                            {acl isAllowed="COMMENT_UPDATE"}
                                <button class="btn" ng-click="edit(content.id, 'admin_comment_show')" title="{t}Edit{/t}" type="button">
                                    <i class="icon-pencil"></i>
                                </button>
                            {/acl}
                            {acl isAllowed="COMMENT_DELETE"}
                                <button class="btn btn-danger" ng-click="open('modal-delete', 'backend_ws_comment_delete', $index)" type="button">
                                   <i class="icon-trash icon-white"></i>
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
            <tfoot>
                <tr>
                    <td colspan="6" class="center">
                        <div class="pull-left" ng-if="shvs.contents.length > 0">
                            {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                        </div>
                        <div class="pull-right" ng-if="shvs.contents.length > 0">
                            <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_contents_list')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
                        </div>
                        <span ng-if="shvs.contents.length == 0">&nbsp;</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </script>
    <script type="text/ng-template" id="modal-delete">
        {include file="common/modals/_modalDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
        {include file="common/modals/_modalBatchDelete.tpl"}
    </script>
</form>
{/block}
