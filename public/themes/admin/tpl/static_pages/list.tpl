{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_staticpages}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentListController" ng-init="init('static_page', { title_like: '', content_status: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-file-o"></i>
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

<div class="page-navbar selected-navbar collapsed" class="hidden" ng-class="{ 'collapsed': selected.contents.length == 0 }">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section pull-left">
                <li class="quicklinks">
                  <button class="btn btn-link" ng-click="selected.contents = []; selected.all = 0" tooltip="Clear selection" tooltip-placement="right"type="button">
                    <i class="fa fa-check fa-lg"></i>
                  </button>
                </li>
                 <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                    <h4>
                        [% selected.contents.length %] {t}items selected{/t}
                    </h4>
                </li>
            </ul>
            <ul class="nav quick-section pull-right">
                <li class="quicklinks">
                    <button class="btn btn-link" ng-click="deselectAll()" tooltip="{t}Clear selection{/t}" tooltip-placement="bottom" type="button">
                      {t}Deselect{/t}
                    </button>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
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
                        <a class="btn btn-link" href="#" id="batch-delete" ng-click="sendToTrashSelected()">
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
                    <input class="no-boarder" name="title" ng-model="criteria.title_like" placeholder="{t}Search by title{/t}" type="text"/>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks dropdown">
                    <select name="status" ng-model="criteria.content_status" data-label="{t}Status{/t}">
                        <option value="-1"> {t}-- All --{/t} </option>
                        <option value="1"> {t}Published{/t} </option>
                        <option value="0"> {t}No published{/t} </option>
                    </select>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <span class="info">
                    {t}Results{/t}: [% pagination.total %]
                    </span>
                </li>
            </ul>
            <ul class="nav quick-section pull-right">
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks form-inline pagination-links">
                    <div class="btn-group">
                        <button class="btn btn-white" ng-click="goToPrevPage()" ng-disabled="isFirstpage()" type="button">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-white" ng-click="goToNextPage()" ng-disabled="isLastPage()" type="button">
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
                <div class="loading-spinner"></div>
                <div class="spinner-text">{t}Loading{/t}...</div>
            </div>
            <div class="table-wrapper ng-cloak">
                <table class="table table-hover no-margin" ng-if="!loading">
                    <thead>
                        <tr ng-if="contents.length > 0">
                            <th style="width:15px;">
                                <div class="checkbox checkbox-default">
                                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                                    <label for="select-all"></label>
                                </div>
                            </th>
                            <th>{t}Title{/t}</th>
                            <th>{t}URL{/t}</th>
                            <!-- <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th> -->
                            <th class="center" style="width:20px;">{t}Published{/t}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-if="contents.length == 0">
                            <td class="empty" colspan="10">{t}No available static pages.{/t}</td>
                        </tr>
                        <tr ng-if="contents.length >= 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                            <td>
                                <div class="checkbox check-default">
                                        <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                                        <label for="checkbox[%$index%]"></label>
                                    </div>
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
                                    <button class="del link link-danger" ng-click="sendToTrash(content)" type="button">
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
                                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading && content.content_status == 1, 'fa-times text-danger': !content.loading && content.content_status == 0 }" ></i>
                                    </button>
                                {/acl}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div ng-include="'static_pages'"></div>
        </div>
        <div class="grid-footer clearfix ng-cloak" ng-if="!loading">
            <div class="pagination-info pull-left" ng-if="contents.length > 0">
                {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
            </div>
            <div class="pull-right" ng-if="contents.length > 0">
                <pagination class="no-margin" max-size="5" direction-links="true"  on-select-page="selectPage(page, 'backend_ws_contents_list')" ng-model="pagination.page" total-items="pagination.total" num-pages="pages"></pagination>
            </div>
        </div>
    </div>

        <script  type="text/ng-template" id="static_pages">


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
