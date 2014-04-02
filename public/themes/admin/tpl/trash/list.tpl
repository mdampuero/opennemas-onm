{extends file="base/admin.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
<form action="{url name=admin_trash}" method="post" id="trashform"  ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('content', { in_litter: 1, title_like: '', content_type_name: -1 }, 'created', 'desc', 'backend_ws_contents_list')">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Trash{/t}</h2></div>
            <ul class="old-button">
                <li ng-if="shvs.selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="TRASH_ADMIN"}
                        <li>
                            <a href="#" ng-click="open('modal-batch-restore', 'backend_ws_contents_batch_restore_from_trash')">
                                <i class="icon-retweet"></i>
                                {t}Restore{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" ng-click="open('modal-batch-remove-permanently', 'backend_ws_contents_batch_remove_permanently')">
                                <i class="icon-trash"></i>
                                {t}Delete{/t}
                            </a>
                        </li>
                        {/acl}
                    </ul>
                </li>
            </ul>
        </div>
    </div>

	<div class="wrapper-content">
        {render_messages}
        <div class="table-info clearfix">
            <div class="pull-left">
                <div class="form-inline">
                    <strong>{t}FILTER:{/t}</strong>
                    &nbsp;&nbsp;
                    <input type="text" autofocus placeholder="{t}Search by title{/t}" name="title" ng-model="shvs.search.title_like"/>
                    &nbsp;&nbsp;
                    {include file="trash/partials/_pills.tpl"}
                </div>
            </div>
        </div>
        <div ng-include="'trash_list'"></div>
        <script type="text/ng-template" id="trash_list">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
               <tr>
                    <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                    <th class="left">{t}Content type{/t}</th>
                    <th class='left'>{t}Title{/t}</th>
                    <th style="width:40px">{t}Section{/t}</th>
                    <th class="left" style="width:110px;">{t}Date{/t}</th>
                    <th class="nowrap center" style="width:40px;">{t}Actions{/t}</th>
               </tr>
            </thead>
            <tbody>
                <tr ng-if="shvs.contents.length == 0">
                    <td class="empty"colspan=6>
                        {t}There is no elements in the trash{/t}
                    </td>
                </tr>
                <tr ng-if="shvs.contents.length >= 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected($index) }">
                    <td>
                        <checkbox index="[% $index %]">
                    </td>
                    <td><strong>[% content.content_type_l10n_name %]</strong> </td>
                    <td>[% content.title %]</td>
                    <td class="left">[% content.category_name %]</td>
                    <td class="center">[% content.created %]</td>
                    <td class="nowrap right">
                        <div class="btn-group">

                            <button class="del btn" ng-click="open('modal-restore-from-trash', 'backend_ws_content_restore_from_trash', $index)" type="button" title="{t}Restore{/t}">
                                <i class="icon-retweet"></i> {t}Restore{/t}
                            </button>

                            <button class="btn btn-danger" ng-click="open('modal-remove-permanently', 'backend_ws_content_remove_permanently', $index)" type="button" title="{t}Restore{/t}">
                                <i class="icon-trash icon-white"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="center">
                        <div class="pull-left">
                            {t}Showing{/t} [% (shvs.page - 1) * 10 %]-[% (shvs.page * 10) < shvs.total ? shvs.page * 10 : shvs.total %] {t}of{/t} [% shvs.total %]
                        </div>
                        <div class="pull-right">
                            <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_contents_list')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
        </script>

        <script type="text/ng-template" id="modal-restore-from-trash">
            {include file="common/modals/_modalRestoreFromTrash.tpl"}
        </script>

        <script type="text/ng-template" id="modal-remove-permanently">
            {include file="common/modals/_modalRemovePermanently.tpl"}
        </script>

        <script type="text/ng-template" id="modal-batch-restore">
            {include file="common/modals/_modalBatchDelete.tpl"}
        </script>

        <script type="text/ng-template" id="modal-batch-remove-permanently">
            {include file="common/modals/_modalBatchRemovePermanently.tpl"}
        </script>

    </div>
</form>
{/block}
