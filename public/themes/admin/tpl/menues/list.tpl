{extends file="base/admin.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
<form action="{url name=admin_menus}" method="GET" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init(null, { available: -1, renderlet: -1 }, 'name', 'asc', 'backend_ws_menus_list')">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Menus{/t}</h2></div>
              <ul class="old-button">
                <li ng-if="shvs.selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="MENU_DELETE"}
                            <li>
                                <a href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_menus_batch_delete')">
                                    <i class="icon-trash"></i>
                                    {t}Delete{/t}
                                </a>
                            </li>
                        {/acl}
                    </ul>
                </li>
                <li class="separator" ng-if="shvs.selected.length > 0"></li>
                <li>
                    {acl isAllowed="MENU_CREATE"}
                    <a href="{url name=admin_menu_create}" class="admin_add">
                        <img border="0" src="{$params.IMAGE_DIR}/article_add.png" alt="Nuevo"><br />{t}New menu{/t}
                    </a>
                    {/acl}
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">
        {render_messages}
        <div ng-include="'menus'"></div>
    </div><!--fin wrapper-content-->
    <script type="text/ng-template" id="menus">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
                <tr>
                    <th style="width:15px;">
                        <checkbox select-all="true"></checkbox>
                    </th>
                    <th>{t}Title{/t}</th>
                    {if count($menu_positions) > 1}
                    <th class="nowrap center" style="width:100px;">{t}Position assigned{/t}</th>
                    {/if}
                    <th class="center" style="width:100px;"></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected($index) }">
                    <td class="center">
                        <checkbox index="[% $index %]">
                    </td>
                    <td>
                        [% content.name %]
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
                    <td class="right">
                        <div class="btn-group">
                            {acl isAllowed="MENU_UPDATE"}
                            <a class="btn" href="[% edit(content.id, 'admin_menu_show') %]" title="{t}Edit page '[% content.name %]'{/t}">
                                <i class="icon-pencil"></i> {t}Edit{/t}
                            </a>
                            {/acl}
                            {acl isAllowed="MENU_DELETE"}
                                <button class="btn btn-danger" ng-if="content.type == 'user'" ng-click="open('modal-delete', 'backend_ws_menu_delete', $index)" type="button">
                                    <i class="icon-trash icon-white"></i>
                                </button>
                            {/acl}
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <div class="pull-left" ng-if="shvs.contents.length > 0">
                            {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                        </div>
                        <div class="pull-right" ng-if="shvs.contents.length > 0">
                            <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_menus_list')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
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
</form>
{/block}
