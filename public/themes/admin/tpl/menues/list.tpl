{extends file="base/admin.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
<form action="{url name=admin_menus}" method="GET" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init(null, { content_status: -1, renderlet: -1 }, 'name', 'asc', 'backend_ws_menus_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-home fa-lg"></i>
                            {t}Menus{/t}
                        </h4>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            {acl isAllowed="MENU_CREATE"}
                            <a href="{url name=admin_menu_create}" class="btn btn-primary">
                                <i class="fa fa-plus fa-lg"></i>
                                {t}Create menu{/t}
                            </a>
                            {/acl}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="page-navbar selected-navbar ng-scope" ng-class="{ 'collapsed': shvs.contents.length == 0 }" style="display:none">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section pull-left">
                    <li class="quicklinks">
                        <h4 class="ng-binding">
                            <i class="fa fa-check"></i>
                            Items selected
                        </h4>
                    </li>
                </ul>
                <ul class="nav quick-section pull-right">
                    <!-- <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="MENU_DELETE"}
                            <li>
                                <a href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_menus_batch_delete')">
                                    <i class="icon-trash"></i>
                                    {t}Delete{/t}
                                </a>
                            </li>
                        {/acl}
                    </ul> -->
                    <li class="quicklinks">
                        <button class="btn btn-link ng-scope" ng-click="selected.instances = []; selected.all = 0" tooltip="Clear selection" tooltip-placement="bottom" type="button">
                          Deselect
                        </button>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <button class="btn btn-link ng-scope" ng-click="deleteSelected()" tooltip="Delete" tooltip-placement="bottom" type="button">
                            <i class="fa fa-trash-o fa-lg"></i>
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
                <div class="grid-overlay" ng-if="loading"></div>
                <div ng-include="'menus'"></div>

            </div>
        </div>
    </div><!--fin wrapper-content-->
    <script type="text/ng-template" id="menus">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div class="table-wrapper">

            <table class="table table-hover no-margin" ng-if="!loading">
                <thead>
                    <tr>
                        <th class="pointer" style="width:15px;">
                            <checkbox select-all="true"></checkbox>
                        </th>
                        <th class="pointer">{t}Name{/t}</th>
                        {if count($menu_positions) > 1}
                        <th class="pointer nowrap center" style="width:100px;">{t}Position assigned{/t}</th>
                        {/if}
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                        <td class="center">
                            <checkbox index="[% content.id %]">
                        </td>
                        <td>
                            [% content.name %]
                            <div class="listing-inline-actions">
                                <a class="link" href="[% edit(content.id, 'admin_menu_show') %]" title="{t}Edit{/t}">
                                    <i class="fa fa-pencil"></i>{t}Edit{/t}
                                </a>
                                <button class="link link-danger" ng-if="content.type == 'user'" ng-click="open('modal-delete', 'backend_ws_menu_delete', $index)" type="button">
                                    <i class="fa fa-trash-o"></i>{t}Delete{/t}
                                </button>
                            </div>
                        </td>
                        {if count($menu_positions) > 1}
                        <td class="left">
                            <span ng-if="content.position">
                                [% content.position %]
                            </span>
                            <span ng-if="!content.position">
                                {t}Unasigned{/t}
                            </span>
                        </td>
                        {/if}
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="grid-footer clearfix">
            <div class="pull-left pagination-info" ng-if="shvs.contents.length > 0">
                {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
            </div>
            <div class="pull-right" ng-if="shvs.contents.length > 0">
                <pagination max-size="0" direction-links="true"  on-select-page="selectPage(page, 'backend_ws_menus_list')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
            </div>
            <span ng-if="shvs.contents.length == 0">&nbsp;</span>
        </div>
    </script>
    <script type="text/ng-template" id="modal-delete">
        {include file="common/modals/_modalDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
        {include file="common/modals/_modalBatchDelete.tpl"}
    </script>
</form>
{/block}
