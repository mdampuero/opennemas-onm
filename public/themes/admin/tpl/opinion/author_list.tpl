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
<form action="{url name=admin_opinion_authors}" method="get" id="authorform" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init(null, { fk_user_group: 3 }, 'name', 'asc', 'backend_ws_users_list')">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Authors{/t}</h2></div>
            <ul class="old-button">
                <li ng-if="shvs.selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="ARTICLE_DELETE"}
                            <li>
                                <a href="#" id="batch-delete" ng-click="open('modal-delete-selected', 'backend_ws_users_batch_delete')">
                                    <i class="icon-trash"></i>
                                    {t}Delete{/t}
                                </a>
                            </li>
                        {/acl}
                    </ul>
                </li>
                <li class="separator" ng-if="shvs.selected.length > 0"></li>
                <li>
                    <a href="{url name=admin_opinion_author_create}" title="{t}Create new author{/t}">
                        <img src="{$params.IMAGE_DIR}user_add.png" alt="Nuevo"><br />{t}New author{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        {render_messages}
        <div ng-include="'authors'"></div>
    </div>
    <script type="text/ng-template" id="authors">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
                <tr>
                    <th style="width:15px;">
                        <input type="checkbox" ng-checked="areSelected()" ng-click="selectAll($event)">
                    </th>
                    <th class="center" style="width:20px;">{t}Avatar{/t}</th>
                    <th class="left">{t}Full name{/t}</th>
                    <th class="left" >{t}Biography{/t}</th>
                    <th class="center" style="width:10px">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                    <td>
                        <input ng-checked="isSelected(content.id)" ng-click="updateSelection($event, content.id)" type="checkbox" value="[% content.id %]">
                    </td>
                    <td class="center">
                        {if is_object($user->photo) && !is_null($user->photo->name)}
                        {dynamic_image src="{$user->photo->path_file}/{$user->photo->name}" transform="thumbnail,40,40"}
                        {else}
                        {gravatar email="{$user->email}" image_dir=$params.IMAGE_DIR image=true size="40"}
                        {/if}
                    </td>

                    <td class="left">
                        <a ng-click="edit(content.id, 'admin_opinion_author_show')" title="{t}Edit user{/t}">
                            [% content.name %]
                        </a>
                    </td>

                    <td class="left">
                        <span ng-if="content.is_blog == 1">
                            <strong>Blog </strong>:
                        </span>
                        [% content.bio %]
                    </td>
                    <td class="right nowrap">
                        <div class="btn-group">
                            <button class="btn" ng-click="edit(content.id, 'admin_opinion_author_show')" title="{t}Edit user{/t}" type="button">
                                <i class="icon-pencil"></i> {t}Edit{/t}
                            </button>
                            <button class="btn btn-danger" ng-click="open('modal-delete', 'backend_ws_user_delete', $index)" type="button">
                                <i class="icon-trash icon-white"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <tr ng-if="shvs.contents.length == 0">
                    <td colspan="7" class="empty">
                        {t escape=off}There is no users created yet or <br/>your search don't match your criteria{/t}
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="center">
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
</form>
{/block}
