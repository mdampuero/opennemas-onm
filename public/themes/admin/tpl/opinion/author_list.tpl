{extends file="base/admin.tpl"}
{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init(null, { fk_user_group: 3 }, 'name', 'asc', 'backend_ws_users_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-user"></i>
                            {t}Authors{/t}
                        </h4>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <a class="btn btn-primary" href="{url name=admin_opinion_author_create}" title="{t}Create new author{/t}">
                                <i class="fa fa-plus"></i>
                                {t}New author{/t}
                            </a>
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
                    {acl isAllowed="AUTHOR_DELETE"}
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="open('modal-delete-selected', 'backend_ws_users_batch_delete')" tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
                        <input class="no-boarder" name="title" ng-model="shvs.search.name_like" placeholder="{t}Search by title{/t}" type="text"/>
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
                <div class="table-wrapper" ng-if="!loading">
                    <table class="table table-hover table-condensed" ng-if="!loading">
                        <thead>
                            <tr>
                                <th style="width:15px;">
                                    <checkbox select-all="true"></checkbox>
                                </th>
                                <th class="center" style="width:20px;">{t}Avatar{/t}</th>
                                <th class="left">{t}Full name{/t}</th>
                                <th class="left" >{t}Biography{/t}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                                <td>
                                    <checkbox type="checkbox" index="[% content.id %]">
                                </td>
                                <td class="center">
                                    <dynamic-image instance="{$smarty.const.INSTANCE_MEDIA}" path="[% shvs.extra.photos[content.avatar_img_id].path_file + '/' + shvs.extra.photos[content.avatar_img_id].name %]" transform="thumbnail,40,40" ng-if="content.avatar_img_id != 0"></dynamic-image>
                                    <gravatar email="[% content.email %]" image_dir="$params.IMAGE_DIR" image=true size="40" ng-if="content.avatar_img_id == 0"></gravatar>
                                </td>

                                <td class="left">
                                    [% content.name %]
                                    <div class="listing-inline-actions">
                                        {acl isAllowed="AUTHOR_UPDATE"}
                                            <a class="link" href="[% edit(content.id, 'admin_opinion_author_show') %]" title="{t}Edit user{/t}">
                                                <i class="fa fa-pencil"></i> {t}Edit{/t}
                                            </a>
                                        {/acl}
                                        {acl isAllowed="AUTHOR_DELETE"}
                                            <button class="link link-danger" ng-click="open('modal-delete', 'backend_ws_user_delete', $index)" type="button">
                                                <i class="fa fa-trash-o"></i>
                                                {t}Delete{/t}
                                            </button>
                                        {/acl}
                                    </div>
                                </td>
                                <td class="left">
                                    <span ng-if="content.is_blog == 1">
                                        <strong>Blog </strong>:
                                    </span>
                                    [% content.bio %]
                                </td>
                            </tr>
                            <tr ng-if="shvs.contents.length == 0">
                                <td colspan="7" class="empty">
                                    {t escape=off}There is no users created yet or <br/>your search don't match your criteria{/t}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="grid-footer clearfix" ng-if="!loading">
                <div class="pagination-info pull-left" ng-if="shvs.contents.length > 0">
                    {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                </div>
                <div class="pull-right" ng-if="shvs.contents.length > 0">
                    <pagination class="no-margin" max-size="5" direction-links="true"  on-select-page="selectPage(page, 'backend_ws_users_list')" ng-model="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
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
