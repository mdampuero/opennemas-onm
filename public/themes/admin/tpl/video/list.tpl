{extends file="base/admin.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('video', { content_status: -1, title_like: '', category_name: -1, in_litter: 0{if $category == 'widget'},in_home: 1{/if} }, {if $category == 'widget'}'position'{else}'created'{/if}, {if $category == 'widget'}'asc'{else}'desc'{/if}, 'backend_ws_contents_list')">
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title">
                <h2>{t}Videos{/t} :: </h2>
                <div class="section-picker">
                    <div class="title-picker btn"><span class="text">{if $category == 'widget'}{t}WIDGET HOME{/t}{elseif $category == 'all'}{t}Listing{/t}{else}{$datos_cat[0]->title}{/if}</span> <span class="caret"></span></div>
                    <div class="options">
                        <a href="{url name=admin_videos_widget}" {if $category=='widget'}class="active"{/if}>{t}WIDGET HOME{/t}</a>
                        <a href="{url name=admin_videos}" {if $category != 'widget'}class="active"{/if}>{t}Listing{/t}</a>
                    </div>
                </div>
            </div>
			<ul class="old-button">
                {acl isAllowed="VIDEO_SETTINGS"}
                    <li>
                        <a href="{url name=admin_videos_config}" class="admin_add" title="{t}Config video module{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                            {t}Settings{/t}
                        </a>
                    </li>
                    <li class="separator"></li>
                {/acl}
                <li ng-if="shvs.selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="VIDEO_AVAILABLE"}
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
                        <li class="divider"></li>
                        <li>
                            <a href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 1, 'home_loading')">
                                <i class="go-home"></i>
                                {t escape="off"}In home{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 0, 'home_loading')">
                                <i class="no-home"></i>
                                {t escape="off"}Drop from home{/t}
                            </a>
                        </li>
                        {/acl}
                        {acl isAllowed="VIDEO_DELETE"}
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
				{acl isAllowed="VIDEO_CREATE"}
				<li>
					<a href="{url name=admin_videos_create category=$category}" accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}/video.png" title="Nuevo Video" alt="Nuevo Video"><br />{t}New video{/t}
					</a>
				</li>
				{/acl}
                {acl isAllowed="VIDEO_WIDGET"}
                    {if $category eq 'widget'}
                        <li class="separator"></li>
                        <li>
                            <a href="#" ng-click="savePositions('backend_ws_contents_save_positions')" title="{t}Save positions{/t}">
                                <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save positions{/t}"><br />{t}Save positions{/t}
                            </a>
                        </li>
                    {/if}
                {/acl}
			</ul>
		</div>
	</div>

    <div class="wrapper-content">
        {render_messages}

        {if $category == 'widget'}
            <div class="messages" ng-if="{$total_elements_widget} > 0 && shvs.total != {$total_elements_widget}">
                <div class="alert alert-info">
                    <button class="close" data-dismiss="alert">×</button>
                    {t 1=$total_elements_widget}You must put %1 videos in the HOME{/t}<br>
                </div>
            </div>
        {/if}

        <div class="table-info clearfix">
            <div class="pull-left">
                <div class="form-inline">
                    <strong>{t}FILTER:{/t}</strong>
                    &nbsp;&nbsp;
                    <input type="text" autofocus placeholder="{t}Search by title{/t}" name="title" ng-model="shvs.search.title_like"/>
                    &nbsp;&nbsp;
                    <select class="select2" id="category" ng-model="shvs.search.category_name" data-label="{t}Category{/t}">
                        <option value="-1">{t}-- All --{/t}</option>
                            {section name=as loop=$allcategorys}
                                {assign var=ca value=$allcategorys[as]->pk_content_category}
                                <option value="{$allcategorys[as]->name}">
                                    {$allcategorys[as]->title}
                                    {if $allcategorys[as]->inmenu eq 0}
                                        <span class="inactive">{t}(inactive){/t}</span>
                                    {/if}
                                </option>
                                {section name=su loop=$subcat[as]}
                                {assign var=subca value=$subcat[as][su]->pk_content_category}
                                {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                                    {assign var=subca value=$subcat[as][su]->pk_content_category}
                                    <option value="{$subcat[as][su]->name}">
                                        &rarr;
                                        {$subcat[as][su]->title}
                                        {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}
                                            <span class="inactive">{t}(inactive){/t}</span>
                                        {/if}
                                    </option>
                                {/acl}
                                {/section}
                            {/section}
                    </select>
                    &nbsp;&nbsp;
                    <select class="select2" name="status" ng-model="shvs.search.content_status" data-label="{t}Status{/t}">
                        <option value="-1"> {t}-- All --{/t} </option>
                        <option value="1"> {t}Published{/t} </option>
                        <option value="0"> {t}No published{/t} </option>
                    </select>
                    &nbsp;&nbsp;
                    <input type="hidden" name="in_home" ng-model="shvs.search.in_home">
                </div>
            </div>
        </div>
        <div ng-include="'files'"></div>

        <script type="text/ng-template" id="files">
            <div class="spinner-wrapper" ng-if="loading">
                <div class="spinner"></div>
                <div class="spinner-text">{t}Loading{/t}...</div>
            </div>

            <table class="table table-hover table-condensed" ng-if="!loading">
               <thead>
                    <tr>
                        <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                        <th></th>
                        <th>{t}Title{/t}</th>
                        <th class="center">{t}Section{/t}</th>
                        <th class="center nowrap">Created</th>
                        {acl isAllowed="VIDEO_AVAILABLE"}
                        <th class="center" style="width:35px;">{t}Published{/t}</th>
                        {/acl}
                        {acl isAllowed="VIDEO_FAVORITE"}
                        <th class="center" style="width:35px;">{t}Favorite{/t}</th>
                        {/acl}
                        {acl isAllowed="VIDEO_HOME"}
                        <th class="center" style="width:35px;">{t}Home{/t}</th>
                        {/acl}
                        <th class="center"></th>
                    </tr>
                </thead>
                <tbody {if $category == 'widget'}ui-sortable ng-model="shvs.contents"{/if}>
                    <tr ng-if="shvs.contents.length == 0">
                        <td class="empty" colspan="10">{t}No available videos.{/t}</td>
                    </tr>

                    <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }" data-id="[% content.id %]">
                        <td>
                            <checkbox index="[% content.id %]">
                        </td>
                        <td style="width:15px;">
                            <img ng-src="[% content.thumb %]" alt="" style="max-width:60px">
                        </td>
                        <td>
                            <strong ng-if="content.author_name != 'internal'">[% content.author_name %]</strong> [% content.title %]
                        </td>
                        {if $category=='widget' || $category=='all'}
                        <td class="center">
                            [% content.category_name %]
                        </td>
                        {/if}
                        </td>
                        <td class="center nowrap">
                            [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                        </td>
                        {acl isAllowed="VIDEO_AVAILABLE"}
                        <td class="center">
                            <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.content_status == 1, unpublished: content.content_status == 0 }" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button"></button>
                        </td>
                        {/acl}
                        {acl isAllowed="VIDEO_FAVORITE"}
                        <td class="center">
                            <button class="btn-link" ng-class="{ loading: content.favorite_loading == 1, 'favorite': content.favorite == 1, 'no-favorite': content.favorite != 1 }" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_favorite', 'favorite', content.favorite != 1 ? 1 : 0, 'favorite_loading')" type="button"></button>
                        </td>
                        {/acl}
                        {acl isAllowed="VIDEO_HOME"}
                        <td class="center">
                            <button class="btn-link" ng-class="{ 'loading': content.home_loading == 1, 'go-home': content.in_home == 1, 'no-home': content.in_home == 0 }" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button"></button>
                        </td>
                        {/acl}

                        <td class="right">
                            <div class="btn-group">
                                {acl isAllowed="VIDEO_UPDATE"}
                                <a class="btn" href="[% edit(content.id, 'admin_video_show') %]">
                                    <i class="icon-pencil"></i>
                                </a>
                                {/acl}
                                {acl isAllowed="VIDEO_DELETE"}
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
