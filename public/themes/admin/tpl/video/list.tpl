{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('video', { content_status: -1, title_like: '', category_name: -1, in_litter: 0{if $category == 'widget'},in_home: 1{/if} }, {if $category == 'widget'}'position'{else}'created'{/if}, {if $category == 'widget'}'asc'{else}'desc'{/if}, 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-film"></i>
                            {t}Videos{/t}
                        </h4>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks dropdown">
                        <div data-toggle="dropdown">
                            {if $category == 'widget'}
                                {t}Widget home{/t}
                            {elseif $category == 'all'}
                                {t}Listing{/t}
                            {else}
                                {$datos_cat[0]->title}
                            {/if}
                            <span class="caret"></span>
                        </div>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{url name=admin_videos_widget}" {if $category=='widget'}class="active"{/if}>
                                    {t}Widget home{/t}
                                </a>
                            </li>
                            <li>
                                <a href="{url name=admin_videos}" {if $category != 'widget'}class="active"{/if}>
                                    {t}Listing{/t}
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        {acl isAllowed="VIDEO_SETTINGS"}
                        <li class="quicklinks">
                            <a class="btn btn-link" href="{url name=admin_videos_config}" class="admin_add" title="{t}Config video module{/t}">
                                <span class="fa fa-cog"></span>
                            </a>
                        </li>
                        <li class="quicklinks"><span class="h-seperate"></span></li>
                        {/acl}
                        {acl isAllowed="VIDEO_WIDGET"}
                        {if $category eq 'widget'}
                        <li class="quicklinks">
                            <a class="btn btn-white" href="#" ng-click="savePositions('backend_ws_contents_save_positions')" title="{t}Save positions{/t}">
                                {t}Save positions{/t}
                            </a>
                        </li>
                        <li class="quicklinks"><span class="h-seperate"></span></li>
                        {/if}
                        {/acl}
                        {acl isAllowed="VIDEO_CREATE"}
                        <li class="quicklinks">
                            <a class="btn btn-primary" href="{url name=admin_videos_create category=$category}" accesskey="N" tabindex="1">
                                <span class="fa fa-save"></span>
                                {t}Create{/t}
                            </a>
                        </li>
                        {/acl}
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
                    {acl isAllowed="VIDEO_AVAILABLE"}
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
                                <i class="fa fa-times fa-lg"></i>
                            </button>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
                                <i class="fa fa-check fa-lg"></i>
                            </button>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 1, 'home_loading')" tooltip="{t escape="off"}In home{/t}" tooltip-placement="{t escape="off"}In home{/t}">
                                <i class="fa fa-home"></i>
                            </a>
                        </li>
                        <li class="quicklinks">
                            <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 0, 'home_loading')" tooltip="{t escape="off"}Drop from home{/t}" tooltip-placement="{t escape="off"}Drop from home{/t}">
                                <i class="fa fa-home"></i>
                                <i class="fa fa-times fa-sub text-danger"></i>
                            </a>
                        </li>
                        {acl isAllowed="VIDEO_DELETE"}
                            <li class="quicklinks">
                                <span class="h-seperate"></span>
                            </li>
                        {/acl}
                    {/acl}
                    {acl isAllowed="VIDEO_DELETE"}
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="open('modal-delete-selected', 'backend_ws_contents_batch_send_to_trash')" tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
                        <input class="no-boarder" name="title" ng-model="shvs.search.title_like" placeholder="{t}Search by title{/t}" type="text"/>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks dropdown">
                        <select id="category" ng-model="shvs.search.category_name" data-label="{t}Category{/t}">
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
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks">
                        <select name="status" ng-model="shvs.search.content_status" data-label="{t}Status{/t}">
                            <option value="-1"> {t}-- All --{/t} </option>
                            <option value="1"> {t}Published{/t} </option>
                            <option value="0"> {t}No published{/t} </option>
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
                    <input type="hidden" name="in_home" ng-model="shvs.search.in_home">
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

        {if $category == 'widget'}
            <div class="messages" ng-if="{$total_elements_widget} > 0 && shvs.total != {$total_elements_widget}">
                <div class="alert alert-info">
                    <button class="close" data-dismiss="alert">×</button>
                    {t 1=$total_elements_widget}You must put %1 videos in the HOME{/t}<br>
                </div>
            </div>
        {/if}

        <div class="grid simple">
            <div class="grid-body no-padding">
                <div class="spinner-wrapper" ng-if="loading">
                    <div class="spinner"></div>
                    <div class="spinner-text">{t}Loading{/t}...</div>
                </div>
                <div class="table-wrapper" !ng-if="loading">
                    <table class="table table-hover no-margin" ng-if="!loading">
                       <thead>
                            <tr>
                                <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
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
                                    <div class="listing-inline-actions">
                                        {acl isAllowed="VIDEO_UPDATE"}
                                        <a class="link" href="[% edit(content.id, 'admin_video_show') %]">
                                            <i class="fa fa-pencil"></i> {t}Edit{/t}
                                        </a>
                                        {/acl}

                                        {acl isAllowed="VIDEO_DELETE"}
                                        <button class="del link link-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                            <i class="fa fa-trash-o"></i> {t}Remove{/t}
                                        </button>
                                        {/acl}
                                    </div>
                                </td>
                                {if $category=='widget' || $category=='all'}
                                <td class="center">
                                    [% content.category_name %]
                                </td>
                                {/if}
                                </td>
                                <td class="center nowrap">
                                    [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                                </td>
                                {acl isAllowed="VIDEO_AVAILABLE"}
                                <td class="center">
                                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading == 1 && content.content_status == 1, 'fa-times text-danger': !content.loading == 1 && content.content_status == 0 }"></i>
                                    </button>
                                </td>
                                {/acl}
                                {acl isAllowed="VIDEO_FAVORITE"}
                                <td class="center">
                                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_favorite', 'favorite', content.favorite != 1 ? 1 : 0, 'favorite_loading')" type="button">
                                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.favorite_loading == 1, 'fa-star text-warning': !content.favorite_loading == 1 && content.favorite == 1, 'fa-star-o': !content.favorite_loading == 1 && content.favorite != 1 }"></i>
                                    </button>
                                </td>
                                {/acl}
                                {acl isAllowed="VIDEO_HOME"}
                                <td class="center">
                                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.home_loading == 1, 'fa-home text-success': !content.home_loading == 1 && content.in_home == 1, 'fa-home': !content.home_loading == 1 && content.in_home == 0 }"></i>
                                        <i class="fa fa-times fa-sub text-danger" ng-if="!content.favorite_loading == 1 && content.in_home == 0"></i>
                                    </button>
                                </td>
                                {/acl}
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="grid-footer clearfix" ng-if="shvs.contents.length > 0">
                <div class="pull-left">
                    {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                </div>
                <div class="pull-right">
                    <pagination class="no-margin" max-size="5" direction-links="true" on-select-page="selectPage(page, 'backend_ws_contents_list')" ng-model="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
                </div>
            </div>
        </div>

        <script type="text/ng-template" id="modal-delete">
            {include file="common/modals/_modalDelete.tpl"}
        </script>

        <script type="text/ng-template" id="modal-delete-selected">
            {include file="common/modals/_modalBatchDelete.tpl"}
        </script>
    </div>
</form>
{/block}
