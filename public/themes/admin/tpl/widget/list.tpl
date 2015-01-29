{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('widget', { content_status: -1, renderlet: -1, title_like: '', in_litter: 0 }, 'title', 'asc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-gamepad"></i>
                            {t}Widgets{/t}
                        </h4>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        {acl isAllowed="ARTICLE_CREATE"}
                        <li class="quicklinks">
                            <a href="{url name=admin_widget_create category=$category}" class="btn btn-primary">
                                <i class="fa fa-plus fa-lg"></i>
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
                    {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
                        <li class="quicklinks">
                            <button class="btn btn-link"  ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
                                <i class="fa fa-times fa-lg"></i>
                            </button>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
                                <i class="fa fa-check fa-lg"></i>
                            </button>
                        </li>
                    {/acl}
                    {acl isAllowed="ADVERTISEMENT_DELETE"}
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
                        <span class="add-on">
                            <span class="fa fa-search fa-lg"></span>
                        </span>
                        <input class="no-boarder" placeholder="{t}Filter by title{/t}" ng-model="shvs.search.title_like" type="text" style="width:250px;">
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <select class="select2" name="type" ng-model="shvs.search.renderlet" data-label="{t}Type{/t}">
                            <option value="-1">{t}-- All --{/t}</option>
                            <option value="intelligentwidget">{t}IntelligentWidget{/t}</option>
                            <option value="html">{t}HTML{/t}</option>
                            <option value="smarty">{t}Smarty{/t}</option>
                        </select>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <select class="select2" name="status" ng-model="shvs.search.content_status" data-label="{t}Status{/t}">
                            <option value="-1"> {t}-- All --{/t} </option>
                            <option value="1"> {t}Published{/t} </option>
                            <option value="0"> {t}No published{/t} </option>
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
                            <th>{t}Name{/t}</th>
                            <th style="width:70px">{t}Type{/t}</th>
                            <th class="center" style="width:20px">{t}Published{/t}</th>
                        </thead>
                        <tbody>
                            <tr ng-if="shvs.contents.length == 0">
                                <td class="empty" colspan="5">
                                    {t}There is no available widgets{/t}
                                </td>
                            </tr>
                            <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                                <td>
                                    <checkbox index="[% content.id %]">
                                </td>
                                <td>
                                    [% content.title %]
                                    <div class="listing-inline-actions">
                                        {acl isAllowed="WIDGET_UPDATE"}
                                            <a class="link" href="[% edit(content.id, 'admin_widget_show') %]" title="{t}Edit widget '[% content.title %]'{/t}">
                                                <i class="fa fa-pencil"></i> {t}Edit{/t}
                                            </a>
                                        {/acl}
                                        {acl isAllowed="WIDGET_DELETE"}
                                            <button class="link link-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                                <i class="fa fa-trash-o"></i> {t}Delete{/t}
                                            </button>
                                        {/acl}
                                    </div>
                                </td>
                                <td>
                                    [% content.renderlet %]
                                </td>
                                <td class="center">
                                    {acl isAllowed="WIDGET_AVAILABLE"}
                                    <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.content_status == 1, unpublished: content.content_status == 0 }" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button"></button>
                                    {/acl}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="grid-footer clearfix" ng-if="!loading">
                <div class="pull-left pagination-info" ng-if="shvs.contents.length > 0">
                    {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                </div>
                <div class="pull-right" ng-if="shvs.contents.length > 0">
                    <pagination class="no-margin" max-size="5" direction-links="true" items-per-page="shvs.elements_per_page" on-select-page="selectPage(page, 'backend_ws_contents_list')" ng-model="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
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
