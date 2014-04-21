{extends file="base/admin.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
<form action="{url name=admin_letters}" method="GET" name="formulario" id="formulario"  ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('letter', { content_status: -1, in_home: {if $category == 'widget'}1{else}-1{/if}, title_like: '', in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
    <div class="top-action-bar clearfix" class="clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2> {t}Letters to the Editor{/t}</h2>
            </div>
            <ul class="old-button">
                <li ng-if="shvs.selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="LETTER_AVAILABLE"}
                        <li>
                            <a href="#" id="batch-publish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')">
                                <i class="icon-eye-open"></i>
                                {t}Publish{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="batch-unpublish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')">
                                <i class="icon-eye-close"></i>
                                {t}Unpublish{/t}
                            </a>
                        </li>
                        {/acl}
                        {acl isAllowed="LETTER_DELETE"}
                            <li class="divider"></li>
                            <li>
                                <a href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_contents_batch_send_to_trash')">
                                    <i class="icon-trash"></i>
                                    {t}Delete{/t}
                                </a>
                            </li>
                        {/acl}
                    </ul>
                </li>
                <li class="separator" ng-if="shvs.selected.length > 0"></li>
                {acl isAllowed="LETTER_CREATE"}
                <li>
                    <a href="{url name=admin_letter_create}" class="admin_add" accesskey="N" tabindex="1">
                        <img src="{$params.IMAGE_DIR}list-add.png" title="Nueva" alt="Nueva"><br />{t}New letter{/t}
                    </a>
                </li>
                {/acl}

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
                    <input type="text" placeholder="{t}Search by title{/t}" name="title" ng-model="shvs.search.title_like"/>
                    &nbsp;&nbsp;
                    <select class="select2" id="content_status" ng-model="shvs.search.content_status" data-label="{t}Status{/t}">
                        <option value="-1">-- All --</option>
                        <option value="1">{t}Published{/t}</option>
                        <option value="0">{t}No published{/t}</option>
                        <option value="2">{t}Rejected{/t}</option>
                    </select>
                </div>
            </div>
        </div>
        <div ng-include="'letters'"></div>

        <script type="text/ng-template" id="letters">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>

        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
                <tr>
                    <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                    <th>{t}Author{/t} - {t}Title{/t}</th>
                    <th style='width:110px;' class="left">{t}Date{/t}</th>
                    <th style='width:10px;'>{t}Image{/t}</th>
                    {acl isAllowed="LETTER_AVAILABLE"}
                    <th class="center" style='width:10px;'>{t}Available{/t}</th>
                    {/acl}
                    <th style='width:10px;' class="right"></th>
               </tr>
            </thead>
            <tbody>
                <tr ng-if="shvs.contents.length == 0">
                    <td class="empty" colspan="10">{t}No available letters.{/t}</td>
                </tr>
                <tr ng-if="shvs.contents.length >= 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                    <td>
                        <checkbox index="[% content.id %]">
                    </td>
                    <td>
                        <div>
                            <small>[% content.author %]: [% content.email %]</small>
                        </div>
                        <span tooltip="[% content.body | striptags | limitTo: 140 %]...">[% content.title %]</span>
                    </td>
                    <td class="center nowrap">
                        [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                    </td>
                    <td >
                        <img ng-if="content.image" ng-src="{$params.IMAGE_DIR}iconos/picture.png" alt="{t}Media{/t}" title="{t}Media element (jpg, image, gif){/t}" />
                    </td>
                    <td class="center">
                        {acl isAllowed="LETTER_AVAILABLE"}
                        <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.content_status == 1, unpublished: content.content_status != 1 }" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" title="{t}Publish/Unpublish{/t}" type="button"></button>
                        <button class="btn-link" ng-class="{ loading: content.loading == 1, unpublished: content.loading != 1 }" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', 2, 'loading')" ng-if="content.content_status != 2" type="button" title="{t}Reject{/t}">
                        </button>
                        {/acl}
                    </td>
                    <td class="right">
                        <div class="btn-group">
                            {acl isAllowed="LETTER_UPDATE"}
                            <a class="btn" href="[% edit(content.id, 'admin_letter_show') %]">
                                <i class="icon-pencil"></i>
                            </a>
                            {/acl}
                            {acl isAllowed="LETTER_DELETE"}
                            <button class="del btn btn-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                <i class="icon-trash icon-white"></i>
                            </button>
                            {/acl}
                       </ul>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="center">
                        <div class="pull-left" ng-if="shvs.contents.length > 0">
                            {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                        </div>
                        <div class="pull-right" ng-if="shvs.contents.length > 0">
                            <pagination max-size="0" direction-links="true"  on-select-page="selectPage(page, 'backend_ws_contents_list')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
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
    </div>
</form>
{/block}
