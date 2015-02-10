<div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.contents.length == 0 }">
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
                {acl isAllowed="CONTENT_OTHER_UPDATE"}
                    {acl isAllowed="OPINION_AVAILABLE"}
                        <li class="quicklinks">
                            <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" tooltip="{t}Publish{/t}" tooltip-placement="bottom">
                                <i class="fa fa-check fa-lg"></i>
                            </a>
                        </li>
                        <li class="quicklinks">
                            <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" tooltip="{t}Unpublish{/t}" tooltip-placement="bottom">
                                <i class="fa fa-times fa-lg"></i>
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                    {/acl}
                    {acl isAllowed="OPINION_HOME"}
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
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                {/acl}
                {acl isAllowed="CONTENT_OTHER_DELETE"}
                    {acl isAllowed="OPINION_DELETE"}
                        <li class="quicklinks">
                            <a class="btn btn-link" href="#" ng-click="sendToTrashSelected()" tooltip="{t}Delete{/t}" tooltip-placement="bottom">
                                <i class="fa fa-trash-o fa-lg"></i>
                            </a>
                        </li>
                    {/acl}
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
                <li>
                    <select class="select2" ng-model="criteria.blog" data-label="{t}Type{/t}">
                        <option value="-1">-- All --</option>
                        <option value="0">Opinion</option>
                        {is_module_activated name="BLOG_MANAGER"}<option value="1">Blog</option>{/is_module_activated}
                    </select>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li>
                    <select class="select2" ng-model="criteria.content_status" data-label="{t}Status{/t}">
                        <option value="-1">{t}-- All --{/t}</option>
                        <option value="1">{t}Published{/t}</option>
                        <option value="0">{t}No published{/t}</option>
                    </select>
                </li>
                <li class="quicklinks">
                    <span class="h-seperate"></span>
                </li>
                <li>
                    <select class="select2" ng-model="criteria.author" data-label="{t}Author{/t}">
                        <option value="-1">{t}-- All authors --{/t}</option>
                        <option value="-2">{t}Director{/t}</option>
                        <option value="-3">{t}Editorial{/t}</option>
                        {section name=as loop=$autores}
                            <option value="{$autores[as]->id}" {if isset($author) && $author == $autores[as]->id} selected {/if}>{$autores[as]->name} {if $autores[as]->meta['is_blog'] eq 1} (Blogger) {/if}</option>
                        {/section}
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
                        <button class="btn btn-white" ng-click="goToPrevPage()" ng-disabled="isFirstPage()" type="button">
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
                        <tr>
                            <th style="width:15px;">
                                <div class="checkbox checkbox-default">
                                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                                    <label for="select-all"></label>
                                </div>
                            </th>
                            <th>{t}Author name{/t} - {t}Title{/t}</th>
                            <th class="center">{t}Created on{/t}</th>
                            <th class="center" style="width:40px"><i class="icon-eye-open" style="font-size: 130%;"></i></th>
                            <th class="center" style="width:70px;">{t}In home{/t}</th>
                            <th class="center" style="width:20px;">{t}Published{/t}</th>
                            <th class="center" style="width:20px;">{t}Favorite{/t}</th>
                      </tr>
                    </thead>
                    <tbody>
                        <tr ng-if="contents.length > 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                            <td>
                                <div class="checkbox check-default">
                                        <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                                        <label for="checkbox[%$index%]"></label>
                                    </div>
                            </td>
                            <td>
                                <strong>
                                    <span ng-if="content.fk_author">
                                        [% shvs.extra.authors[content.fk_author].name %]
                                    </span>
                                    <span ng-if="!content.fk_author || content.fk_author == 0">
                                        [% content.author %]
                                    </span>
                                </strong>
                                -
                                [% content.title %]
                                <div class="listing-inline-actions">
                                    {acl isAllowed="OPINION_UPDATE"}
                                        <a class="link" href="[% edit(content.id, 'admin_opinion_show') %]">
                                            <i class="fa fa-pencil"></i>
                                            {t}Edit{/t}
                                        </a>
                                    {/acl}
                                    {acl isAllowed="OPINION_DELETE"}
                                        <button class="link link-danger" {acl isNotAllowed="CONTENT_OTHER_DELETE"} ng-if="content.fk_author == {$smarty.session.userid}"{/acl} ng-click="sendToTrash(content)" type="button">
                                            <i class="fa fa-trash-o"></i>
                                            {t}Delete{/t}
                                        </button>
                                    {/acl}
                                </div>
                            </td>
                            <td class="center nowrap">
                                    [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                                </td>
                            <td class="center">
                                [% shvs.extra.views[content.id] %]
                            </td>
                            <td class="center">
                                {acl isAllowed="OPINION_HOME"}
                                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.home_loading == 1, 'fa-home text-info': content.in_home == 1, 'fa-home': content.in_home == 0 }" ng-if="content.author.meta.is_blog != 1" ></i>
                                        <i class="fa fa-times fa-sub text-danger" ng-if="!content.home_loading && content.in_home == 0"></i>
                                    </button>
                                    <span ng-if="content.author.meta.is_blog == 1">
                                        Blog
                                    </span>
                                {/acl}
                            </td>
                            <td class="center">
                                {acl isAllowed="OPINION_AVAILABLE"}
                                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading && content.content_status == 1, 'fa-times text-danger': !content.loading && content.content_status == 0 }"></i>
                                    </button>
                                {/acl}
                            </td>
                            <td class="right">
                                {acl isAllowed="OPINION_FAVORITE"}
                                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_favorite', 'favorite', content.favorite != 1 ? 1 : 0, 'favorite_loading')" ng-if="content.type_opinion == 0" type="button">
                                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.favorite_loading == 1, 'fa-star text-warning': !content.favorite_loading && content.favorite == 1, 'fa-star-o': !content.favorite_loading && content.favorite != 1 }"></i>
                                    </button>
                                {/acl}
                            </td>
                        </tr>
                        <tr ng-if="contents.length == 0">
                            <td class="empty" colspan="11">
                                {t}There is no opinions yet.{/t}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="grid-footer clearfix ng-cloak" ng-if="!loading">
            <div class="pagination-info pull-left" ng-if="contents.length > 0">
                {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
            </div>
            <div class="pull-right" ng-if="contents.length > 0">
                <pagination class="no-margin" max-size="5" direction-links="true"  on-select-page="selectPage(page, 'backend_ws_opinions_list')" ng-model="pagination.page" total-items="pagination.total" num-pages="pages"></pagination>
            </div>
        </div>
    </div>
</div>
