{extends file="base/admin.tpl"}

{block name="header-js" append}
    <style type="text/css">
        .panel{ border:0 !important; }

        .drag-category{
            cursor:pointer;
            padding:10 px;
            list-style-type: none;
            border: 1px solid #CCCCCC;
            width:200px;
        }
    </style>
    {script_tag src="router.js" language="javascript" bundle="fosjsrouting" basepath="js"}
    {script_tag src="routes.js" language="javascript" common=1 basepath="js"}
    {script_tag src="angular.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="ui-bootstrap-tpls-0.10.0.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="app.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="services.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="controllers.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="content-modal.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="content.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="fos-js-routing.js" language="javascript" bundle="backend" basepath="js/services"}
    {script_tag src="shared-vars.js" language="javascript" bundle="backend" basepath="js/services"}
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
                        <input type="checkbox" ng-checked="isSelectedAll()" ng-click="selectAll($event)">
                    </th>
                    <th>{t}Title{/t}</th>
                    {if count($menu_positions) > 1}
                    <th class="nowrap center" style="width:100px;">{t}Position assigned{/t}</th>
                    {/if}
                    <th class="center" style="width:100px;"></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="content in shvs.contents">
                    <td class="center">
                        <input type="checkbox" class="minput"  id="[% content.pk_menu %]" ng-checked="isSelected(content.pk_menu)" ng-click="updateSelection($event, content.pk_menu)">
                    </td>
                    <td>
                        {acl isAllowed="MENU_UPDATE"}
                        <a href="#" title="{t}Edit page '[% content.name %]'{/t}" title={t}"Edit"{/t}>
                        {/acl}
                            [% content.name %]
                        {acl isAllowed="MENU_UPDATE"}
                        </a>
                        {/acl}
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
                            <button class="btn" ng-click="edit(content.id, 'admin_menu_show')" title="{t}Edit page '[% content.name %]'{/t}" type="button">
                                <i class="icon-pencil"></i> {t}Edit{/t}
                            </button>
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
                    <td colspan="4">&nbsp;</td>
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
