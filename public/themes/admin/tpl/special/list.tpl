{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_specials}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentListController" ng-init="init('special', { content_status: -1, category_name: -1, in_home: {if $category == 'widget'}1{else}-1{/if}, title_like: '', in_litter: 0 }, {if $category == 'widget'}'position', 'asc'{else}'created', 'desc'{/if}, 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-star"></i>
                            {t}Specials{/t}
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
                                <a href="{url name=admin_specials_widget}" {if $category =='widget'}class="active"{/if}>
                                    {t}Widget Home{/t}
                                </a>
                            </li>
                            <li>
                                <a href="{url name=admin_specials}" {if $category !=='widget'}class="active"{/if}>
                                    {t}Listing{/t}
                                </a>
                            </li>
                        </ul>
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

    <div class="page-navbar selected-navbar" class="hidden" ng-class="{ 'collapsed': selected.contents.length == 0 }">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section pull-left">
                    <li class="quicklinks">
                      <button class="btn btn-link" ng-click="selected.contents = []; selected.all = 0" tooltip="Clear selection" tooltip-placement="right" type="button">
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
                    {acl isAllowed="SPECIAL_AVAILABLE"}
                    <li class="quicklinks">
                        <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" tooltip="{t}Publish{/t}" tooltip-placement="bottom">
                            <i class="fa fa-check"></i>
                        </a>
                    </li>
                    <li class="quicklinks">
                        <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" tooltip="{t}Unpublish{/t}" tooltip-placement="bottom">
                            <i class="fa fa-times"></i>
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks">
                        <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 1, 'home_loading')" tooltip="{t escape="off"}In home{/t}" tooltip-placement="bottom">
                            <i class="fa fa-home"></i>
                        </a>
                    </li>
                    <li class="quicklinks">
                        <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 0, 'home_loading')" tooltip="{t escape="off"}Drop from home{/t}" tooltip-placement="bottom">
                            <i class="fa fa-home"></i>
                            <i class="fa fa-times fa-sub text-danger"></i>
                        </a>
                    </li>
                {/acl}
                {acl isAllowed="SPECIAL_DELETE"}
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
                        <input type="hidden" name="in_home" ng-model="criteria.in_home">
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
        {if $category == 'widget'}
            <div class="messages" ng-if="{$total_elements_widget} > 0 && pagination.total != {$total_elements_widget}">
                <div class="alert alert-info">
                    <button class="close" data-dismiss="alert">×</button>
                    {t 1=$total_elements_widget}You must put %1 specials in the HOME{/t}<br>
                </div>
            </div>
        {/if}
        <div class="grid simple">
            <div class="grid-body no-padding">
                <div class="spinner-wrapper" ng-if="loading">
                    <div class="spinner"></div>
                    <div class="spinner-text">{t}Loading{/t}...</div>
                </div>
                <div class="table-wrapper">
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
                                <th class="center" style="width:100px;">Created</th>
                                {acl isAllowed="SPECIAL_AVAILABLE"}<th class="center" style="width:35px;">{t}Published{/t}</th>{/acl}
                                {acl isAllowed="SPECIAL_FAVORITE"}{if $category!='widget'}<th class="center" style="width:35px;">{t}Favorite{/t}</th>{/if}{/acl}
                                {acl isAllowed="SPECIAL_HOME"}<th class="center" style="width:35px;">{t}Home{/t}</th>{/acl}
                            </tr>
                        </thead>
                        <tbody {if $category == 'widget'}ui-sortable ng-model="contents"{/if}>
                            <tr ng-if="contents.length == 0">
                                <td class="empty" colspan="10">{t}No available specials.{/t}</td>
                            </tr>
                            <tr ng-if="contents.length >= 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                                <td class="center">
                                    <div class="checkbox check-default">
                                        <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                                        <label for="checkbox[%$index%]"></label>
                                    </div>
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
                                        <button class="del link link-danger" ng-click="sendToTrash(content)" type="button">
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
                                        <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                                            <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading && content.content_status == 1, 'fa-times text-danger': !content.loading && content.content_status == 0 }"></i>
                                        </button>
                                    </td>
                                {/acl}
                                {if $category!='widget'}
                                    {acl isAllowed="SPECIAL_FAVORITE"}
                                        <td class="center">
                                            <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_favorite', 'favorite', content.favorite != 1 ? 1 : 0, 'favorite_loading')" type="button">
                                                <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.favorite_loading == 1, 'fa-star text-warning': !content.favorite_loading && content.favorite == 1, 'fa-star-o': !content.favorite_loading && content.favorite == 0 }"></i>
                                            </button>
                                        </td>
                                    {/acl}
                                {/if}
                                {acl isAllowed="SPECIAL_HOME"}
                                    <td class="center">
                                        <button class="btn btn-white" ng-if="content.author.meta.is_blog != 1" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                                            <i class="fa fa-home" ng-class="{ 'fa-circle-o-notch fa-spin': content.home_loading == 1, 'text-info': content.in_home == 1 }"></i>
                                            <i class="fa fa-times fa-sub" ng-if="content.in_home == 0"></i>
                                        </button>
                                    </td>
                                {/acl}
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="grid-footer clearfix" ng-if="contents.length > 0">
                <div class="pagination-info pull-left">
                    {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
                </div>
                <div class="pull-right">
                    <pagination class="no-margin" max-size="5" direction-links="true"  on-select-page="selectPage(page, 'backend_ws_contents_list')" page="pagination.page" total-items="pagination.total" num-pages="pages"></pagination>
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
