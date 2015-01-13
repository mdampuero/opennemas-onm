{extends file="base/admin.tpl"}

{block name="content"}
    <div ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('article', { content_status: -1, category_name: -1, title_like: '', in_litter: 0, fk_author: -1 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

        <div class="page-navbar actions-navbar">
            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <h4>
                                <i class="fa fa-file-text-o fa-lg"></i>
                                {t}Articles{/t}
                            </h4>
                        </li>
                    </ul>
                    <div class="all-actions pull-right">
                        <ul class="nav quick-section">
                            <li>
                                {acl isAllowed="ARTICLE_CREATE"}
                                    <a class="btn btn-primary" href="{url name=admin_article_create category=$category}">
                                        <i class="fa fa-plus"></i>
                                        {t}New article{/t}
                                    </a>
                                {/acl}
                            </li>
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
                        {acl isAllowed="ARTICLE_AVAILABLE"}
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
                        {/acl}
                        {acl isAllowed="ARTICLE_DELETE"}
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
                        <li class="m-r-10 input-prepend inside search-form no-boarder">
                            <input type="text" autofocus placeholder="{t}Search by title:{/t}" name="title" ng-model="shvs.search.title_like"/>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li>
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
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li>
                            <select class="select2" name="status" ng-model="shvs.search.content_status" data-label="{t}Status{/t}">
                                <option value="-1"> {t}-- All --{/t} </option>
                                <option value="1"> {t}Published{/t} </option>
                                <option value="0"> {t}No published{/t} </option>
                            </select>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li>
                            <select class="select2" ng-model="shvs.search.fk_author" data-label="{t}Author{/t}">
                                <option value="-1">{t}-- All --{/t}</option>
                                {foreach $authors as $author}
                                    <option value="{$author->id}">{$author->name}</option>
                                {/foreach}
                            </select>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks hidden-xs">
                            <select class="xmedium" ng-model="pagination.epp">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                            </select>
                        </li>
                        <li class="quicklinks hidden-xs">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="criteria = {  name_like: [ { value: '', operator: 'like' } ]}; orderBy = [ { name: 'last_login', value: 'desc' } ]; pagination = { page: 1, epp: 25 }; refresh()">
                                <i class="fa fa-trash-o fa-lg"></i>
                            </button>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="refresh()">
                                <i class="fa fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': loading, 'fa-repeat': !loading }"></i>
                            </button>
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
                        <div class="spinner"></div>
                        <div class="spinner-text">{t}Loading{/t}...</div>
                    </div>
                    <div class="table-wrapper">
                        <table class="table table-hover no-margin" ng-if="!loading">
                            <thead>
                                <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                                <th class="left" >{t}Title{/t}</th>
                                {if $category eq 'all' || $category == 0}
                                    <th class="left">{t}Section{/t}</th>
                                {/if}
                                <th class="center" style="width:130px;">{t}Created{/t}</th>
                                <th class="center" style="width:10px;">{t}Published{/t}</th>
                            </thead>
                            <tbody>
                                <tr ng-if="shvs.contents.length == 0">
                                    <td class="empty" colspan="10">{t}No available articles.{/t}</td>
                                </tr>
                                <tr ng-if="shvs.contents.length >= 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                                    <td>
                                        <checkbox index="[% content.id %]">
                                    </td>
                                    <td class="left" >
                                        <span tooltip="{t}Last editor{/t} [% shvs.extra.authors[content.fk_user_last_editor].name %]">[% content.title %]</span>
                                        <div>
                                            <small ng-if="content.fk_author != 0 || content.agency != ''">
                                                <strong>{t}Author{/t}:</strong>
                                                <span ng-if="content.fk_author != 0">
                                                    [% shvs.extra.authors[content.fk_author].name %]
                                                </span>
                                                <span ng-if="content.fk_author == 0 && content.agency != ''">
                                                    [% content.agency %]
                                                </span>
                                            </small>
                                        </div>
                                        <div class="listing-inline-actions">
                                            {acl isAllowed="ARTICLE_UPDATE"}
                                                <a class="link" href="[% edit(content.id, 'admin_article_show') %]">
                                                    <i class="fa fa-pencil"></i>
                                                    {t}Edit{/t}
                                                </a>
                                            {/acl}
                                            {acl isAllowed="ARTICLE_DELETE"}
                                                <button class="link link-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                                    <i class="fa fa-trash-o"></i>
                                                    {t}Delete{/t}
                                                </button>
                                            {/acl}
                                        </div>
                                    </td>
                                    {if $category eq 'all' || $category == 0}
                                    <td class="left">
                                        <span ng-if="content.category_name == 'unknown'">
                                            {t}Unasigned{/t}
                                        </span>
                                        <span ng-if="content.category_name != 'unknown'">
                                            [% content.category_name %]
                                        </span>
                                    </td>
                                    {/if}
                                    <td class="center nowrap">
                                        [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                                    </td>
                                    <td class="center">
                                        <span ng-if="content.category != 20">
                                        {acl isAllowed="ARTICLE_AVAILABLE"}
                                            <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                                                <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading, 'fa-check text-success' : !content.loading && content.content_status == '1', 'fa-times text-error': !content.loading && content.content_status == '0' }"></i>
                                            </button>
                                        {/acl}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="grid-footer clearfix" ng-if="!loading">
                    <div class="pull-left pagination-info" ng-if="shvs.contents.length > 0">
                        {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total|number %]
                    </div>
                    <div class="pull-right" ng-if="shvs.contents.length > 0">
                        <pagination class="no-margin" max-size="5" direction-links="true"  on-select-page="selectPage(page, 'backend_ws_contents_list')" ng-model="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
                    </div>
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
{/block}
