{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="angular.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="ui-bootstrap-tpls-0.10.0.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="app.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="controllers.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="widgets.js" language="javascript" bundle="backend" basepath="js/controllers"}
{/block}

{block name="content"}
<form action="{url name=admin_widgets}" method="GET" name="formulario" id="formulario" ng-app="BackendApp">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Widgets{/t}</h2>
            </div>
            <ul class="old-button">
                  {acl isAllowed="WIDGET_CREATE"}
                <li>
                    <a href="{url name=admin_widget_create}" class="admin_add"
                       title="{t}New widget{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="" alt="" />
                        <br />{t}New{/t}
                    </a>
                </li>
                {/acl}
            </ul>
        </div>
    </div>
    <div class="wrapper-content" ng-controller="WidgetsController" ng-init="list(filters)" data-url="{url name=backend_ws_widgets_list}">
        {render_messages}
        <div class="table-info clearfix">
            {acl hasCategoryAccess=$category}<div class="pull-left"><strong>{t 1=$totalWidgets}%1 widgets{/t}</strong></div> {/acl}
            <div class="pull-right">
                <div class="form-inline">
                    <select name="type">
                        <option value="-1" {if $status === -1} selected {/if}> {t}-- All --{/t} </option>
                        <option value="intelligentwidget" {if $status === intelligentwidget} selected {/if}> {t}IntelligentWidget{/t} </option>
                        <option value="html" {if  $status === html} selected {/if}> {t}HTML{/t} </option>
                        <option value="smarty" {if $status === smarty} selected {/if}> {t}Smarty{/t} </option>
                    </select>
                    {t}Status:{/t}
                    <div class="input-append">
                        <select name="status" ng-model="available">
                            <option value="-1"> {t}-- All --{/t} </option>
                            <option value="1"> {t}Published{/t} </option>
                            <option value="0"> {t}No published{/t} </option>
                        </select>
                        <button type="submit" class="btn"><i class="icon-search"></i> </button>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-hover table-condensed" >
            <thead>
                {if count($widgets) > 0}
                <th>{t}Name{/t}</th>
                <th style="width:70px">{t}Type{/t}</th>
                <th class="center" style="width:20px">{t}Published{/t}</th>
                <th class="center" style="width:10px">Actions</th>
                {else}
                <th scope="col" colspan=4>&nbsp;</th>
                {/if}
            </thead>
            <tbody>
                <tr ng-if="contents.length == 0">
                    <td class="empty" colspan="5">
                        {t}There is no available widgets{/t}
                    </td>
                </tr>
                <tr ng-if="contents.length > 0" ng-include="'widget'" ng-repeat="content in contents"></tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="center">
                        <pagination boundary-links="true" direction-links="false" on-select-page="selectPage(page)" page="page" total-items="total"></pagination>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <script type="text/ng-template" id="widget">
        <td>
            [% content.title %]
        </td>
        <td>
            [% content.renderlet %]
        </td>
        <td class="center">
            {acl isAllowed="WIDGET_AVAILABLE"}
            <a href="#" title="{t}Published{/t}" ng-if="content.available == 1">
                <img src="{$params.IMAGE_DIR}publish_g.png"alt="{t}Published{/t}" />
            </a>
            <a href="#" title="{t}Unpublished{/t}" ng-if="content.available == 0">
                <img src="{$params.IMAGE_DIR}publish_r.png" alt="{t}Unpublished{/t}" />
            </a>
            {/acl}
        </td>
        <td class="right nowrap">
            <div class="btn-group">
                {acl isAllowed="WIDGET_UPDATE"}
                    <button class="btn">
                        <i class="icon-pencil"></i> {t}Edit{/t}
                    </button>
                {/acl}
                {acl isAllowed="WIDGET_DELETE"}
                    <button class="del btn btn-danger">
                        <i class="icon-trash icon-white"></i>
                    </button>
                {/acl}
            </div>
        </td>
    </script>
    {include file="widget/modals/_modalDelete.tpl"}
</form>
{/block}
