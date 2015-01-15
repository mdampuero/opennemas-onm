{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_acl_user}" method="get" id="userform" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init(null, { name_like: '', fk_user_group: -1, type: -1 }, 'name', 'asc', 'backend_ws_users_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-user fa-lg"></i>
                            {t}Users{/t}
                        </h4>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="quick-section">
                        <li class="quicklinks">
                            <a class="btn btn-primary" href="{url name=admin_acl_user_create}">
                                <i class="fa fa-plus"></i>
                                {t}New user{/t}
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
                    {acl isAllowed="USER_AVAILABLE"}
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_users_batch_set_enabled', 'activated', 0, 'loading')" tooltip="{t}Disable{/t}" tooltip-placement="bottom">
                                <i class="fa fa-times fa-lg"></i>
                            </button>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_users_batch_set_enabled', 'activated', 1, 'loading')" tooltip="{t}Enable{/t}" tooltip-placement="bottom">
                                <i class="fa fa-check fa-lg"></i>
                            </button>
                        </li>
                    {/acl}
                    {acl isAllowed="ARTICLE_DELETE"}
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="open('modal-delete-selected', 'backend_ws_users_batch_delete')" tooltip="{t}Delete{/t}" tooltip-placement="bottom">
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
                        <input id="username" name="name" ng-model="shvs.search.name_like" placeholder="{t}Filter by name or email{/t}" type="text" value="{$smarty.request.name|default:""}"/>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li>
                        <select class="select2" data-label="{t}Type{/t}" id="usertype" name="type"  ng-model="shvs.search.type">
                            {assign var=type value=$smarty.request.type}
                            <option value="-1">{t}--All--{/t}</option>
                            <option value="0">{t}Backend{/t}</option>
                            <option value="1">{t}Frontend{/t}</option>
                        </select>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li>
                        <select class="select2" data-label="{t}Group{/t}" id="usergroup" name="group" ng-model="shvs.search.fk_user_group">
                            <option value="-1">{t}--All--{/t}</option>
                            {html_options options=$groupsOptions selected=$smarty.request.group|default:""}
                        </select>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks hidden-xs">
                        <select class="xmedium" ng-model="shvs.elements_per_page">
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
                            <tr>
                                <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                                <th></th>
                                <th class="left">{t}Full name{/t}</th>
                                <th class="center nowrap" style="width:110px">{t}Username{/t}</th>
                                <th class="center" >{t}E-mail{/t}</th>
                                <th class="center" >{t}Group{/t}</th>
                                <th class="center" >{t}Activated{/t}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-if="shvs.contents.length == 0">
                                <td colspan="8" class="empty">
                                    {t escape=off}There is no user created yet or <br/>not results for your searching criteria.{/t}
                                </td>
                            </tr>
                            <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                                <td>
                                    <checkbox index="[% content.id %]">
                                </td>
                                <td>
                                    <dynamic-image instance="{$smarty.const.INSTANCE_MEDIA}" path="[% shvs.extra.photos[content.avatar_img_id].path_file + '/' + shvs.extra.photos[content.avatar_img_id].name %]" transform="thumbnail,40,40" ng-if="content.avatar_img_id != 0"></dynamic-image>

                                    <gravatar email="[% content.email %]" image_dir="$params.IMAGE_DIR" image=true size="40" ng-if="content.avatar_img_id == 0"></gravatar>
                                </td>
                                <td class="left">
                                    <strong>[% content.name %]</strong>
                                    <div class="listing-inline-actions">
                                        <a class="link" href="[% edit(content.id, 'admin_acl_user_show') %]" title="{t}Edit user{/t}">
                                            <i class="fa fa-pencil"></i> {t}Edit{/t}
                                        </a>
                                        <button class="link link-danger" ng-click="open('modal-delete', 'backend_ws_user_delete', $index)" type="button">
                                            <i class="fa fa-trash-o"></i>
                                            {t}Delete{/t}
                                        </button>
                                    </div>
                                </td>
                                <td class="center nowrap">
                                    [% content.username %]
                                </td>

                                <td class="center">
                                    [% content.email %]
                                </td>
                                <td class="center">
                                    <span ng-repeat="group in content.id_user_group">[% shvs.extra.groups[group].name %][% $last ? '' : ', ' %]</span>
                                </td>
                                <td class="center">
                                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_user_set_enabled', 'activated', content.activated != 1 ? 1 : 0, 'loading')" type="button">
                                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading, 'fa-check text-success' : !content.loading && content.activated == '1', 'fa-times text-error': !content.loading && content.activated == '0' }"></i>
                                    </button>
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
                <div class="pull-right">
                    <pagination class="no-margin" max-size="5" direction-links="true" on-select-page="selectPage(page, 'backend_ws_users_list')" ng-model="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
                </div>
            </div>
        </div>
    </div>

	<script type="text/ng-template" id="users">

	</script>
	<script type="text/ng-template" id="modal-delete">
		{include file="common/modals/_modalDelete.tpl"}
	</script>
    <script type="text/ng-template" id="modal-delete-selected">
        {include file="common/modals/_modalBatchDelete.tpl"}
    </script>
</form>
{/block}
