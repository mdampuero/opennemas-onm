{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="router.js" language="javascript" bundle="fosjsrouting" basepath="js"}
    {script_tag src="routes.js" language="javascript" common=1 basepath="js"}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}
    {script_tag src="angular.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="ui-bootstrap-tpls-0.10.0.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="app.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="services.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="controllers.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="content-modal.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="content.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="fos-js-routing.js" language="javascript" bundle="backend" basepath="js/services"}
{/block}

{block name="header-css" append}
<style type="text/css">
    .submitted-on {
        color: #777;
    }
</style>
{/block}


{block name="content"}
<form action="{url name=admin_comments_list}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('comment', { status: -1, body_like: '' }, 'date', 'backend_ws_contents_list')">
    <div class="top-action-bar clearfix" class="clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>
                    {t}Comments{/t}
                </h2>
            </div>
            <ul class="old-button">
                <li ng-if="selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="ARTICLE_AVAILABLE"}
                        <li>
                            <a href="#" id="batch-publish" ng-click="batchToggleAvailable(1, 'backend_ws_contents_batch_toggle_available')">
                                <i class="icon-eye-open"></i>
                                {t}Publish{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="batch-unpublish" ng-click="batchToggleAvailable(0, 'backend_ws_contents_batch_toggle_available')">
                                <i class="icon-eye-close"></i>
                                {t}Unpublish{/t}
                            </a>
                        </li>
                        {/acl}
                        {acl isAllowed="ARTICLE_DELETE"}
                            <li class="divider"></li>
                            <li>
                                <a href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_contents_batch_delete')">
                                    <i class="icon-trash"></i>
                                    {t}Delete{/t}
                                </a>
                            </li>
                        {/acl}
                    </ul>
                </li>
                <li class="separator" ng-if="selected.length > 0"></li>
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
            <div class="pull-right form-inline">
                <input placeholder="{t}Search{/t}" ng-model="filters.search.body_like" type="text">
                <label for="status">Status:</label>
                <select class="form-filters" name="status" ng-model="filters.search.status">
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
                    <th style="width:15px;"><input type="checkbox" ng-checked="areSelected()" ng-click="selectAll($event)"></th>
                    <th>{t}Author{/t}</th>
                    <th>{t}Comment{/t}</th>
                    <th class="wrap">{t}In response to{/t}</th>
                    <th style='width:20px;' class="center">{t}Published{/t}</th>
                    <th style='width:80px;' class="right">{t}Actions{/t}</th>
               </tr>
            </thead>
            <tbody>
                <tr ng-if="contents.length > 0" ng-repeat="content in contents">
                    <td>
                        <input type="checkbox" class="minput" ng-checked="isSelected(content.id)" ng-click="updateSelection($event, content.id)" value="[% content.id %]">
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
                        <div class="submitted-on">{t}Submitted on:{/t} [% content.date.date %]</div>
                        <p>
                            [% content.body %]
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
                <tr ng-if="contents.length == 0">
                    <td class="empty" colspan="6">
                        {t}No comments matched your criteria.{/t}
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="center">
                        <div class="pull-left">
                            [% (page - 1) * 10 %]-[% (page * 10) < total ? page * 10 : total %] of [% total %]
                        </div>
                        <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_contents_list')" page="page" total-items="total" num-pages="pages"></pagination>
                        <div class="pull-right">
                            [% page %] / [% pages %]
                        </div>
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
    <script>
        jQuery('#buton-batchReject').on('click', function(){
            jQuery('#formulario').attr('action', "{url name=admin_comments_batch_status category=$category page=$page}");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
        jQuery('#buton-batchFrontpage').on('click', function(){
            jQuery('#formulario').attr('action', "{url name=admin_comments_batch_status category=$category page=$page}");
            jQuery('#formulario').submit();
            e.preventDefault();
        });

        var comments_manager_urls = {
            batchDelete: '{url name=admin_comments_batch_delete category=$category page=$page}',
        }

    </script>
    {include file="comment/modals/_modalDelete.tpl"}
    {include file="comment/modals/_modalBatchDelete.tpl"}
    {include file="comment/modals/_modalAccept.tpl"}
    {include file="comment/modals/_modalChange.tpl"}
{/block}
