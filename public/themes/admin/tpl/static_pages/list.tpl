{extends file="base/admin.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
<form action="{url name=admin_staticpages}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('static_page', { title_like: '', available: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list')">
    <div class="top-action-bar clearfix">
    	<div class="wrapper-content">
    		<div class="title"><h2>{t}Static pages{/t}</h2></div>
    		<ul class="old-button">
                <li ng-if="shvs.selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="ARTICLE_AVAILABLE"}
                        <li>
                            <a href="#" id="batch-publish" ng-click="batchSetContentStatus(1, 'backend_ws_contents_batch_set_content_status')">
                                <i class="icon-eye-open"></i>
                                {t}Publish{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="batch-unpublish" ng-click="batchSetContentStatus(0, 'backend_ws_contents_batch_set_content_status')">
                                <i class="icon-eye-close"></i>
                                {t}Unpublish{/t}
                            </a>
                        </li>
                        {/acl}
                        {acl isAllowed="ARTICLE_DELETE"}
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
                {acl isAllowed="STATIC_CREATE"}
    			<li>
    				<a href="{url name=admin_staticpages_create}" title="{t}Create new page{/t}">
    					<img border="0" src="{$params.IMAGE_DIR}list-add.png" title="{t}New static page{/t}" alt="" /><br />{t}New page{/t}
    				</a>
    			</li>
                {/acl}
    		</ul>
    	</div>
    </div>
    <div class="wrapper-content">
        {render_messages}
        <div class="table-info clearfix">
            <div>
                <div class="pull-left form-inline">
                    <strong>{t}FILTER:{/t}</strong>
                    <input type="search" autofocus name="title" placeholder="{t}Search by title{/t}" ng-model="shvs.search.title_like"/>
                    &nbsp;&nbsp;
                    <select class="select2" name="status" ng-model="shvs.search.available" data-label="{t}Status{/t}">
                        <option value="-1"> {t}-- All --{/t} </option>
                        <option value="1"> {t}Published{/t} </option>
                        <option value="0"> {t}No published{/t} </option>
                    </select>
                </div>
            </div>
        </div>
        <div ng-include="'static_pages'"></div>

        <script  type="text/ng-template" id="static_pages">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
                <tr ng-if="shvs.contents.length > 0">
                    <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                    <th>{t}Title{/t}</th>
                    <th>{t}URL{/t}</th>
                    <!-- <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th> -->
                    <th class="center" style="width:20px;">{t}Published{/t}</th>
                    <th class="center" style="width:80px;">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-if="shvs.contents.length == 0">
                    <td class="empty" colspan="10">{t}No available static pages.{/t}</td>
                </tr>
                <tr ng-if="shvs.contents.length >= 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                    <td>
                        <checkbox index="[% content.id %]">
                    </td>
                    <td>[% content.title %]</td>
                    <td>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}/[% content.slug %]/" target="_blank" title="{t}Open in a new window{/t}">
                            {$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}/[% content.slug %]
                        </a>
                    </td>
                    <!-- <td class="center">
                        {$page->views}
                    </td> -->
                    <td class="center">
                        {acl isAllowed="STATIC_AVAILABLE"}
                            <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.content_status == 1, unpublished: content.content_status == 0 }" ng-click="setContentStatus($index, 'backend_ws_content_set_content_status', content.content_status != 1 ? 1 : 0)" type="button"></button>
                        {/acl}
                    </td>
                    <td class="right nowrap">
                        <div class="btn-group">
                            {acl isAllowed="STATIC_UPDATE"}
                            <a class="btn" href="[% edit(content.id, 'admin_staticpage_show') %]">
                                <i class="icon-pencil"></i>
                            </a>
                            {/acl}
                            {acl isAllowed="STATIC_DELETE"}
                            <button class="del btn btn-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                <i class="icon-trash icon-white"></i>
                            </button>
                            {/acl}
                        </div>
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
</div>
{/block}
