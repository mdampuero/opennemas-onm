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
<form action="#" method="post" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init(null, { name_like: ''}, 'name', 'asc', 'backend_ws_usergroups_list')">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}User groups{/t}</h2></div>
            <ul class="old-button">
                <li ng-if="shvs.selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="GROUP_DELETE"}
                            <li>
                                <a href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_usergroups_batch_delete')">
                                    <i class="icon-trash"></i>
                                    {t}Delete{/t}
                                </a>
                            </li>
                        {/acl}
                    </ul>
                </li>
                <li class="separator" ng-if="shvs.selected.length > 0"></li>
                {acl isAllowed="GROUP_CREATE"}
                    <li>
                        <a href="{url name="admin_acl_usergroups_create"}">
                            <img src="{$params.IMAGE_DIR}usergroup_add.png" title="{t}New Privilege{/t}" alt="{t}New User Group{/t}"><br />{t}New User group{/t}
                        </a>
                    </li>
                {/acl}
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}
        <div ng-include="'usergroups'"></div>

        <script type="text/ng-template" id="usergroups">
            <div class="table-info clearfix">
                <div class="pull-left form-inline">
                    <strong>{t}FILTER:{/t}</strong>
                    &nbsp;&nbsp;
                    <input type="text" id="username" name="name" value="" placeholder="{t}Filter by name or email{/t}" ng-model="shvs.search.name_like"/>
                </div>
            </div>
            <div class="spinner-wrapper" ng-if="loading">
                <div class="spinner"></div>
                <div class="spinner-text">{t}Loading{/t}...</div>
            </div>
            <table class="table table-hover table-condensed" ng-if="!loading">
                <thead>
                    <tr>
                        <th style="width:15px;"><input type="checkbox" ng-checked="areSelected()" ng-click="selectAll($event)"></th>
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
                            <input type="checkbox" class="minput" ng-checked="isSelected(content.id)" ng-click="updateSelection($event, content.id)" value="[% content.id %]">
                        </td>
                        <td>
                            [% content.name %]
                        </td>
                        <td class="right nowrap">
                            <div class="btn-group">

                                <button class="btn" ng-click="edit(content.id, 'admin_acl_usergroup_show')" title="{t}Edit user{/t}" type="button">
                                    <i class="icon-pencil"></i> {t}Edit{/t}
                                </button>

                                <button class="btn btn-danger" ng-click="open('modal-delete', 'backend_ws_usergroup_delete', $index)"
                                    title="{t}Delete this user{/t}" type="button">
                                    <i class="icon-trash icon-white"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="center">
                            <div class="pull-left">
                                {t}Showing{/t} [% (shvs.page - 1) * shvs.elements_per_page %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                            </div>
                            <div class="pull-right">
                                <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_contents_list')" page="page" total-items="total" num-pages="pages"></pagination>
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
    </div>
</form>
{include file="acl/user_group/modal/_modalDelete.tpl"}
{/block}
