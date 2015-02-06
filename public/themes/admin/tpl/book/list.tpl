{extends file="base/admin.tpl"}
{block name="content"}
<form action="{url name="admin_books"}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentListController" ng-init="init('book', { content_status: -1, title_like: '', category_name: -1, in_litter: 0 {if $category == 'widget'},'in_home': 1{/if}}, {if $category == 'widget'}'position'{else}'created'{/if}, {if $category == 'widget'}'asc'{else}'desc'{/if}, 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-book"></i>
                        {t}Books{/t}
                    </h4>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks dropdown">
                    <div data-toggle="dropdown">
                        {if $category == 'widget'}
                            {t}Widget Home{/t}
                        {else}
                            {t}Listing{/t}
                        {/if}
                        <span class="caret"></span>
                    </div>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{url name=admin_books_widget}" {if $category=='widget'}class="active"{/if}>
                                {t}Widget Home{/t}
                            </a>
                        </li>
                        <li>
                             <a href="{url name=admin_books}" {if $category != 'widget'}class="active"{/if}>
                                {t}Listing{/t}
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    {if $category == 'widget' && $page <= 1}
                    <li class="quicklinks">
                        <a class="btn btn-white" href="#" ng-click="savePositions('backend_ws_contents_save_positions')" title="{t}Save positions{/t}">
                            <span class="fa fa-save"></span>
                            {t}Save positions{/t}
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    {/if}
                    {acl isAllowed="BOOK_CREATE"}
                    <li>
                        <a class="btn btn-primary" href="{url name=admin_books_create category=$category}" title="{t}New book{/t}">
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
                {acl isAllowed="BOOK_AVAILABLE"}
                <li class="quicklinks">
                    <a class="btn btn-link" href="#" id="batch-publish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" tooltip="{t}Publish{/t}" tooltip-placement="bottom">
                        <i class="fa fa-check"></i>
                    </a>
                </li>
                <li class="quicklinks">
                    <a class="btn btn-link" href="#" id="batch-unpublish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" tooltip="{t}Unpublish{/t}" tooltip-placement="bottom">
                        <i class="fa fa-times"></i>
                    </a>
                </li>
                {/acl}
                {acl isAllowed="BOOK_DELETE"}
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <a class="btn btn-link" href="#" id="batch-delete" ng-click="sendToTrashSelected()" tooltip="{t}Delete{/t}" tooltip-placement="bottom">
                        <i class="fa fa-trash-o"></i>
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
                    <select id="category" ng-model="criteria.category_name" data-label="{t}Category{/t}">
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
                    <select name="status" ng-model="criteria.content_status" data-label="{t}Status{/t}">
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
            <div class="spinner-wrapper" ng-if="loading">
                <div class="loading-spinner"></div>
                <div class="spinner-text">{t}Loading{/t}...</div>
            </div>
            <div class="table-wrapper ng-cloak">
                <table class="table table-hover no-margin" ng-if="!loading">
                <thead>
                    <tr>
                        <th style="width:15px;">
                            <div class="checkbox checkbox-default">
                                <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                                <label for="select-all"></label>
                            </div>
                        </th>
                        <th class="title">{t}Title{/t}</th>
                        <th style="width:65px;" class="center">{t}Section{/t}</th>
                        <th class="center" style="width:100px;">{t}Created on{/t}</th>
                        {acl isAllowed="BOOK_AVAILABLE"}
                        <th class="center" style="width:35px;">{t}Published{/t}</th>
                        {/acl}
                        {acl isAllowed="BOOK_AVAILABLE"}
                        <th class="center" style="width:35px;">{t}Home{/t}</th>
                        {/acl}
                    </tr>
                </thead>
                <tbody {if $category == 'widget'}ui-sortable ng-model="contents"{/if}>
                    <tr ng-if="contents.length == 0">
                        <td class="empty" colspan="6">{t}No available books.{/t}</td>
                    </tr>

                    <tr ng-if="contents.length > 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }" data-id="[% content.id %]">
                        <td>
                            <div class="checkbox check-default">
                                        <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                                        <label for="checkbox[%$index%]"></label>
                                    </div>
                        </td>
                        <td>
                            [% content.title %]
                            <div class="listing-inline-actions">
                               {acl isAllowed="BOOK_UPDATE"}
                                <a class="link" href="[% edit(content.id, 'admin_book_show') %]">
                                    <i class="fa fa-pencil"></i>
                                    {t}Edit{/t}
                                </a>
                                {/acl}
                                {acl isAllowed="BOOK_DELETE"}
                                <button class="del link link-danger" ng-click="sendToTrash(content)" type="button">
                                    <i class="fa fa-trash-o"></i>
                                    {t}Delete{/t}
                                </button>
                                {/acl}
                            </div>
                        </td>
                        <td class="center">
                            <span ng-if="content.category_name">
                                [% content.category_name %]
                            </span>
                            <span ng-if="!content.category_name">
                                {t}Unassigned{/t}
                            </span>
                        </td>
                        <td class="center nowrap">
                            [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                        </td>
                        {acl isAllowed="BOOK_AVAILABLE"}
                        <td class="center">
                            <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                                <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading && content.content_status == 1, 'fa-times text-danger': !content.loading && content.content_status == 0 }"></i>
                            </button>
                        </td>
                        {/acl}
                        {acl isAllowed="BOOK_HOME"}
                        <td class="center">
                            <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                                <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.home_loading == 1, 'fa-home text-info': !content.home_loading && content.in_home == 1, 'fa-home': !content.home_loading && content.in_home != 1 }"></i>
                                <i class="fa fa-times fa-sub text-danger" ng-if="!content.home_loading && content.in_home != 1"></i>
                            </button>
                        </td>
                        {/acl}
                    </tr>
                </tbody>
                </table>
            </div>
        </div>
        <div class="grid-footer clearfix ng-cloak" ng-if="contents.length > 0">
            <div class="pagination-info pull-left">
                {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
            </div>
            <div class="pull-right">
                <pagination class="no-margin" max-size="5" direction-links="true"  on-select-page="selectPage(page, 'backend_ws_contents_list')" ng-model="pagination.page" total-items="pagination.total" num-pages="pages"></pagination>
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
