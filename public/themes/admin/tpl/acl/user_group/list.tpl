{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init(null, { name_like: ''}, 'name', 'asc', 'backend_ws_usergroups_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-home fa-lg"></i>
                            {t}User groups{/t}
                        </h4>
                    </li>
                </ul>
            </div>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    {acl isAllowed="GROUP_CREATE"}
                    <li>
                        <a href="{url name="admin_acl_usergroups_create"}" class="btn btn-primary">
                            {t}Create{/t}
                        </a>
                    </li>
                    {/acl}
                </ul>
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
                    <li class="quicklinks">
                        {acl isAllowed="GROUP_DELETE"}
                        <a class="btn btn-link" href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_usergroups_batch_delete')">
                            <i class="fa fa-trash-o"></i>
                        </a>
                        {/acl}
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-navbar filters-navbar" ng-if="!loading">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="m-r-10 input-prepend inside search-input no-boarder">
                        <span class="add-on"> <span class="fa fa-search fa-lg"></span> </span>
                        <input class="no-boarder" name="name" ng-model="shvs.search.name_like" placeholder="Search by name" type="text"> </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content">

        {render_messages}
        <div class="grid simple">
            <div class="grid-body no-padding">
                <div ng-include="'usergroups'"></div>
            </div>
        </div>

        <script type="text/ng-template" id="usergroups">
            <div class="spinner-wrapper" ng-if="loading">
                <div class="spinner"></div>
                <div class="spinner-text">{t}Loading{/t}...</div>
            </div>
            <table class="table table-hover table-condensed" ng-if="!loading">
                <thead>
                    <tr>
                        <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                        <th>{t}Group name{/t}</th>
                        <th class="center" style="width:10px"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-if="shvs.contents.length == 0">
                        <td colspan="3" class="empty">
                            {t escape=off}There is no user groups created yet or <br/>results matching your searching criteria.{/t}
                        </td>
                    </tr>
                    <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                        <td>
                            <checkbox index="[% content.id %]">
                        </td>
                        <td>
                            [% content.name %]
                            <div class="listing-inline-actions">
                                <a class="link" href="[% edit(content.id, 'admin_acl_usergroup_show') %]" title="{t}Edit user group{/t}">
                                    <i class="fa fa-pencil"></i> {t}Edit{/t}
                                </a>
                                <button class="link link-danger" ng-click="open('modal-delete', 'backend_ws_usergroup_delete', $index)"
                                    title="{t}Delete this user group{/t}" type="button">
                                    <i class="fa fa-trash-o"></i>
                                    {t}Delete{/t}
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="center">
                            <div class="pull-left" ng-if="shvs.contents.length > 0">
                                {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                            </div>
                            <div class="pull-right" ng-if="shvs.contents.length > 0">
                                <pagination max-size="0" direction-links="true"  on-select-page="selectPage(page, 'backend_ws_contents_list')" page="page" total-items="total" num-pages="pages"></pagination>
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
