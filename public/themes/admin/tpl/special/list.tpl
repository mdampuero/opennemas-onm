{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_specials}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('special', { content_status: -1, category_name: -1, in_home: {if $category == 'widget'}1{else}-1{/if}, title_like: '', in_litter: 0 }, {if $category == 'widget'}'position', 'asc'{else}'created', 'desc'{/if}, 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}Specials{/t}
                    </h4>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <div class="section-picker">
                        <div class="title-picker btn"><span class="text">{if $category == 'widget'}{t}Widget Home{/t}{else}{t}Listing{/t}{/if}</span> <span class="caret"></span></div>
                        <div class="options">
                            <a href="{url name=admin_specials_widget}" {if $category =='widget'}class="active"{/if}>{t}Widget Home{/t}</a>
                            <a href="{url name=admin_specials}" {if $category !=='widget'}class="active"{/if}>{t}Listing{/t}</a>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    {acl isAllowed="SPECIAL_SETTINGS"}
                    <li class="quicklinks">
                        <a class="btn btn-link"  href="{url name=admin_specials_config}" class="admin_add" title="{t}Config special module{/t}">
                            <span class="fa fa-cog"></span>
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    {/acl}

                    {acl isAllowed="SPECIAL_WIDGET"}
                     {if $category eq 'widget'}
                    <li class="quicklinks">
                        <a class="btn btn-white"  href="#" ng-click="savePositions('backend_ws_contents_save_positions')" title="{t}Save positions{/t}">
                            <span class="fa fa-save"></span>
                            {t}Save positions{/t}
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    {/if}
                    {/acl}
                    {acl isAllowed="SPECIAL_CREATE"}
                    <li class="quicklinks">
                        <a class="btn btn-primary" href="{url name=admin_special_create}">
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
                {acl isAllowed="SPECIAL_AVAILABLE"}
                <li class="quicklinks">
                    <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')">
                        <i class="icon-eye-open"></i>
                        {t}Publish{/t}
                    </a>
                </li>
                <li class="quicklinks">
                    <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')">
                        <i class="icon-eye-close"></i>
                        {t}Unpublish{/t}
                    </a>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 1, 'home_loading')">
                        <i class="go-home"></i>
                        {t escape="off"}In home{/t}
                    </a>
                </li>
                <li class="quicklinks">
                    <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 0, 'home_loading')">
                        <i class="no-home"></i>
                        {t escape="off"}Drop from home{/t}
                    </a>
                </li>
            {/acl}
            {acl isAllowed="SPECIAL_DELETE"}
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <a class="btn btn-link" href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_contents_batch_send_to_trash')">
                        <i class="icon-trash"></i>
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
                    <input type="hidden" name="in_home" ng-model="shvs.search.in_home">
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

    {if $category == 'widget'}
        <div class="messages" ng-if="{$total_elements_widget} > 0 && shvs.total != {$total_elements_widget}">
            <div class="alert alert-info">
                <button class="close" data-dismiss="alert">×</button>
                {t 1=$total_elements_widget}You must put %1 specials in the HOME{/t}<br>
            </div>
        </div>
    {/if}

    <div class="grid simple">
        <div class="grid-body no-padding">
            <div ng-include="'specials'"></div>
        </div>
    </div>


        <script type="text/ng-template" id="specials">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>

        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
                <tr>
                    <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                    <th class="title">{t}Title{/t}</th>
                    <th style="width:65px;" class="center">{t}Section{/t}</th>
                    <th class="center" style="width:100px;">Created</th>
                    {acl isAllowed="SPECIAL_AVAILABLE"}<th class="center" style="width:35px;">{t}Published{/t}</th>{/acl}
                    {acl isAllowed="SPECIAL_FAVORITE"}{if $category!='widget'}<th class="center" style="width:35px;">{t}Favorite{/t}</th>{/if}{/acl}
                    {acl isAllowed="SPECIAL_HOME"}<th class="center" style="width:35px;">{t}Home{/t}</th>{/acl}
                </tr>
            </thead>
            <tbody {if $category == 'widget'}ui-sortable ng-model="shvs.contents"{/if}>
                <tr ng-if="shvs.contents.length == 0">
                    <td class="empty" colspan="10">{t}No available specials.{/t}</td>
                </tr>
                <tr ng-if="shvs.contents.length >= 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                    <td class="center">
                        <checkbox index="[% content.id %]">
                    </td>
                    <td>
                        [% content.title %]
                        <div class="listing-inline-actions">
                            {acl isAllowed="SPECIAL_UPDATE"}
                            <a class="link" href="[% edit(content.id, 'admin_special_show') %]">
                                <i class="fa fa-pencil"></i> {t}Edit{/t}
                            </a>
                            {/acl}
                            {acl isAllowed="SPECIAL_DELETE"}
                            <button class="del link link-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                <i class="fa fa-trash-o"></i> {t}Remove{/t}
                            </button>
                            {/acl}
                        </div>
                    </td>
                    <td class="center">
                        [% content.category_name %]
                    </td>
                    <td class="center nowrap">
                        [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                    </td>
                    {acl isAllowed="SPECIAL_AVAILABLE"}
                    <td class="center">
                        <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.content_status == 1, unpublished: content.content_status == 0 }" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button"></button>
                    </td>
                    {/acl}

                    {if $category!='widget'}
                    {acl isAllowed="SPECIAL_FAVORITE"}
                    <td class="center">
                    <button class="btn-link" ng-class="{ loading: content.favorite_loading == 1, 'favorite': content.favorite == 1, 'no-favorite': content.favorite != 1 }" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_favorite', 'favorite', content.favorite != 1 ? 1 : 0, 'favorite_loading')" type="button"></button>
                    </td>
                    {/acl}
                    {/if}

                    {acl isAllowed="SPECIAL_HOME"}
                    <td class="center">
                        <button class="btn-link" ng-class="{ 'loading': content.home_loading == 1, 'go-home': content.in_home == 1, 'no-home': content.in_home == 0 }" ng-if="content.author.meta.is_blog != 1" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button"></button>
                    </td>
                    {/acl}

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
