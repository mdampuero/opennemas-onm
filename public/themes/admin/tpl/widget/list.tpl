{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="router.js" language="javascript" bundle="fosjsrouting" basepath="js"}
    {script_tag src="routes.js" language="javascript" common=1 basepath="js"}
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
    <div class="wrapper-content" ng-controller="WidgetsCtrl" ng-init="list(filters)">
        {render_messages}
        <div class="table-info clearfix">
            {acl hasCategoryAccess=$category}
                <div class="pull-left">
                    <strong>
                        <span class="loading" ng-if="loading == 1" style="display: inline-block;">&nbsp;</span>
                        <span ng-if="loading == 0">[% total %]</span>
                        {t}widgets{/t}
                    </strong>
                </div>
            {/acl}
            <div class="pull-right">
                <div class="form-inline">
                    <select name="type" ng-model="type">
                        <option value="-1" {if $status === -1} selected {/if}> {t}-- All --{/t} </option>
                        <option value="intelligentwidget" {if $status === intelligentwidget} selected {/if}> {t}IntelligentWidget{/t} </option>
                        <option value="html" {if  $status === html} selected {/if}> {t}HTML{/t} </option>
                        <option value="smarty" {if $status === smarty} selected {/if}> {t}Smarty{/t} </option>
                    </select>
                    {t}Status:{/t}
                    <select name="status" ng-model="available">
                        <option value="-1"> {t}-- All --{/t} </option>
                        <option value="1"> {t}Published{/t} </option>
                        <option value="0"> {t}No published{/t} </option>
                    </select>
                </div>
            </div>
        </div>
        <div ng-if="loading" style="text-align: center; padding: 40px 0px;">
            <img src="/assets/images/facebox/loading.gif" style="margin: 0 auto;">
        </div>
        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
                <th>{t}Name{/t}</th>
                <th style="width:70px">{t}Type{/t}</th>
                <th class="center" style="width:20px">{t}Published{/t}</th>
                <th class="center" style="width:10px">Actions</th>
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
            <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.available == 1, unpublished: content.available == 0 }" ng-click="toggleAvailable($index, content.pk_widget)" type="button">
            </button>
            {/acl}
        </td>
        <td class="right">
            <div class="btn-group">
                {acl isAllowed="WIDGET_UPDATE"}
                    <button class="btn" ng-click="edit(content.pk_widget)" title="{t}Edit widget '[% content.title %]'{/t}" type="button">
                        <i class="icon-pencil"></i> {t}Edit{/t}
                    </button>
                {/acl}
                {acl isAllowed="WIDGET_DELETE"}
                    <button class="del btn btn-danger" ng-click="open($index, content.pk_widget)" type="button">
                        <i class="icon-trash icon-white"></i>
                    </button>
                {/acl}
            </div>
        </td>
    </script>
    <script type="text/ng-template" id="modal-delete">
        {include file="widget/modals/_modalDelete.tpl"}
    </script>
</form>
{/block}
