{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_staticpages}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('static_page', { title_like: '', content_status: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}Static Pages{/t}
                    </h4>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    {acl isAllowed="STATIC_PAGE_CREATE"}
                    <li class="quicklinks">
                        <a class="btn btn-primary" href="{url name=admin_staticpages_create}" title="{t}Create new page{/t}">
                            <span class="fa fa-plus"></span>
                            {t}Create{/t}
                        </a>
                    </li>
                    {/acl}
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="page-navbar selected-navbar" class="hidden" ng-class="{ 'collapsed': shvs.selected.length == 0 }">
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
                {acl isAllowed="ARTICLE_AVAILABLE"}
                <li class="quicklinks">
                    <a class="btn btn-link" href="#" id="batch-publish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')">
                        {t}Publish{/t}
                    </a>
                </li>
                <li class="quicklinks">
                    <a class="btn btn-link" href="#" id="batch-unpublish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')">
                        {t}Unpublish{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ARTICLE_DELETE"}
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks">
                        <a class="btn btn-link" href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_contents_batch_send_to_trash')">
                            {t}Delete{/t}
                        </a>
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
                    <select name="status" ng-model="shvs.search.content_status" data-label="{t}Status{/t}">
                        <option value="-1"> {t}-- All --{/t} </option>
                        <option value="1"> {t}Published{/t} </option>
                        <option value="0"> {t}No published{/t} </option>
                    </select>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
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
            <div ng-include="'static_pages'"></div>
        </div>
    </div>

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
                    <td>
                        [% content.title %]
                        <div class="listing-inline-actions">
                            {acl isAllowed="STATIC_PAGE_UPDATE"}
                            <a class="link" href="[% edit(content.id, 'admin_staticpage_show') %]">
                                <i class="fa fa-pencil"></i> {t}Edit{/t}
                            </a>
                            {/acl}
                            {acl isAllowed="STATIC_PAGE_DELETE"}
                            <button class="del link link-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                <i class="fa fa-trash-o"></i> {t}Delete{/t}
                            </button>
                            {/acl}
                        </div>
                    </td>
                    <td>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}/[% content.slug %]/" target="_blank" title="{t}Open in a new window{/t}">
                            {$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}/[% content.slug %]
                        </a>
                    </td>
                    <!-- <td class="center">
                        {$page->views}
                    </td> -->
                    <td class="center">
                        {acl isAllowed="STATIC_PAGE_AVAILABLE"}
                            <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.content_status == 1, unpublished: content.content_status == 0 }" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button"></button>
                        {/acl}
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
    </form>
</div>
{/block}
