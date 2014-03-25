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

{block name="content"}
<form action="{url name=admin_staticpages}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('static_page', { title: '' }, 'title', 'backend_ws_contents_list')">
    <div class="top-action-bar clearfix">
    	<div class="wrapper-content">
    		<div class="title"><h2>{t}Static pages{/t}</h2></div>
    		<ul class="old-button">
                <li ng-if="selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="ARTICLE_DELETE"}
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
            {acl hasCategoryAccess=$category}<div class="pull-left"><strong>{t}[% total %] static pages{/t}</strong></div>{/acl}

            <div>
                <div class="right form-inline">
                    <div class="input-append">
                        <input type="search" name="title" placeholder="{t}Filter by title{/t}" ng-model="filters.search.title_like"/>
                    </div>
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
                <tr ng-if="contents.length > 0">
                    <th style="width:15px;"><input type="checkbox" ng-checked="areSelected()" ng-click="selectAll($event)"></th>
                    <th>{t}Title{/t}</th>
                    <th>{t}URL{/t}</th>
                    <!-- <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th> -->
                    <th class="center" style="width:20px;">{t}Published{/t}</th>
                    <th class="center" style="width:80px;">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-if="contents.length == 0">
                    <td class="empty" colspan="10">{t}No available static pages.{/t}</td>
                </tr>
                <tr ng-if="contents.length >= 0" ng-repeat="content in contents">
                    <td>
                        <input type="checkbox" class="minput" ng-checked="isSelected(content.id)" ng-click="updateSelection($event, content.id)" value="[% content.id %]">
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
                            <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.available == 1, unpublished: content.available == 0 }" ng-click="toggleAvailable(content.id, $index, 'backend_ws_content_toggle_available')" type="button"></button>
                        {/acl}
                    </td>
                    <td class="right nowrap">
                        <div class="btn-group">
                            {acl isAllowed="STATIC_UPDATE"}
                            <button class="btn" ng-click="edit(content.id, 'admin_staticpage_show')" type="button">
                                <i class="icon-pencil"></i>
                            </button>
                            {/acl}
                            {acl isAllowed="STATIC_DELETE"}
                            <button class="del btn btn-danger" ng-click="open('modal-delete', 'backend_ws_content_delete', $index)" type="button">
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
</div>
{/block}
