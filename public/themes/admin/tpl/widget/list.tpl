{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="router.js" language="javascript" bundle="fosjsrouting" basepath="js"}
    {script_tag src="routes.js" language="javascript" common=1 basepath="js"}
    {script_tag src="angular.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="ui-bootstrap-tpls-0.10.0.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="app.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="services.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="controllers.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="directives.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="content-modal.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="content.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="fos-js-routing.js" language="javascript" bundle="backend" basepath="js/services"}
    {script_tag src="shared-vars.js" language="javascript" bundle="backend" basepath="js/services"}
{/block}

{block name="content"}
<form action="{url name=admin_widgets}" method="GET" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('widget', { available: -1, renderlet: -1, title_like: '', in_litter: 0 }, 'title', 'asc', 'backend_ws_contents_list')">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Widgets{/t}</h2>
            </div>
            <ul class="old-button">
                <li ng-if="shvs.selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="ARTICLE_AVAILABLE"}
                        <li>
                            <a href="#" id="batch-publish" ng-click="batchSetContentStatus(1, 'backend_ws_contents_batch_set_content_status')">
                                <i class="icon-eye-open"></i>
                                {t}Publish{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="batch-unpublish" ng-click="batchSetContentStatus(0, 'backend_ws_contents_batch_set_content_status')">
                                <i class="icon-eye-close"></i>
                                {t}Unpublish{/t}
                            </a>
                        </li>
                        {/acl}
                        {acl isAllowed="ARTICLE_DELETE"}
                            <li class="divider"></li>
                            <li>
                                <a href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_contents_batch_send_to_trash')">
                                    <i class="icon-trash"></i>
                                    {t}Delete{/t}
                                </a>
                            </li>
                        {/acl}
                    </ul>
                </li>
                <li class="separator" ng-if="shvs.selected.length > 0"></li>
                {acl isAllowed="ARTICLE_CREATE"}
                    <li>
                        <a href="{url name=admin_widget_create category=$category}">
                            <img border="0" src="{$params.IMAGE_DIR}/article_add.png" alt="Nuevo"><br />{t}New widget{/t}
                        </a>
                    </li>
                {/acl}
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        {render_messages}
        <div class="table-info clearfix">
            <div class="pull-left">
                <div class="form-inline">
                    <strong>{t}FILTER:{/t}</strong>
                    &nbsp;&nbsp;
                    <input type="text" placeholder="{t}Search by title:{/t}" ng-model="shvs.search.title_like"/>
                    &nbsp;&nbsp;
                    <select class="select2 input-medium" name="type" ng-model="shvs.search.renderlet" data-label="{t}Type{/t}">
                        <option value="-1">{t}-- All --{/t}</option>
                        <option value="intelligentwidget">{t}IntelligentWidget{/t}</option>
                        <option value="html">{t}HTML{/t}</option>
                        <option value="smarty">{t}Smarty{/t}</option>
                    </select>
                    &nbsp;&nbsp;
                    <select class="select2 input-medium" name="status" ng-model="shvs.search.available" data-label="{t}Status{/t}">
                        <option value="-1"> {t}-- All --{/t} </option>
                        <option value="1"> {t}Published{/t} </option>
                        <option value="0"> {t}No published{/t} </option>
                    </select>
                </div>
            </div>
        </div>
        <div ng-include="'widgets'"></div>
    </div>
    <script type="text/ng-template" id="widgets">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
                <th style="width:15px;"><input type="checkbox" ng-checked="areSelected()" ng-click="selectAll($event)"></th>
                <th>{t}Name{/t}</th>
                <th style="width:70px">{t}Type{/t}</th>
                <th class="center" style="width:20px">{t}Published{/t}</th>
                <th class="center" style="width:10px"></th>
            </thead>
            <tbody>
                <tr ng-if="shvs.contents.length == 0">
                    <td class="empty" colspan="5">
                        {t}There is no available widgets{/t}
                    </td>
                </tr>
                <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                    <td>
                        <input type="checkbox" class="minput" ng-checked="isSelected(content.id)" ng-click="updateSelection($event, content.id)" value="[% content.id %]">
                    </td>
                    <td>
                        [% content.title %]
                    </td>
                    <td>
                        [% content.renderlet %]
                    </td>
                    <td class="center">
                        {acl isAllowed="WIDGET_AVAILABLE"}
                        <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.content_status == 1, unpublished: content.content_status == 0 }" ng-click="setContentStatus($index, 'backend_ws_content_set_content_status', content.content_status != 1 ? 1 : 0)" type="button"></button>
                        {/acl}
                    </td>
                    <td class="right">
                        <div class="btn-group">
                            {acl isAllowed="WIDGET_UPDATE"}
                                <button class="btn" ng-click="edit(content.id, 'admin_widget_show')" title="{t}Edit widget '[% content.title %]'{/t}" type="button">
                                    <i class="icon-pencil"></i> {t}Edit{/t}
                                </button>
                            {/acl}
                            {acl isAllowed="WIDGET_DELETE"}
                                <button class="btn btn-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                    <i class="icon-trash icon-white"></i>
                                </button>
                            {/acl}
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="center">
                        <div class="pull-left">
                            {t}Showing{/t} [% (shvs.page - 1) * shvs.elements_per_page %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                        </div>
                        <div class="pull-right">
                            <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_contents_list')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
                        </div>
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
