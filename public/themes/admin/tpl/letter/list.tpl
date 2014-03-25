{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="router.js" language="javascript" bundle="fosjsrouting" basepath="js"}
    {script_tag src="routes.js" language="javascript" common=1 basepath="js"}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}
    {script_tag src="angular.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="ui-bootstrap-tpls-0.10.0.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="app.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="services.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="controllers.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="content-modal.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="content.js" language="javascript" bundle="backend" basepath="js/controllers"}
    {script_tag src="fos-js-routing.js" language="javascript" bundle="backend" basepath="js/services"}
{/block}

{block name="footer-js" append}
    <script type="text/javascript">
        $('[rel=tooltip]').tooltip({ placement : 'bottom' });
    </script>
{/block}

{block name="content"}
<form action="{url name=admin_letters}" method="GET" name="formulario" id="formulario"  ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('letter', { content_status: 0, in_home: {if $category == 'widget'}1{else}-1{/if}, title_like: '' }, 'title', 'backend_ws_contents_list')">
    <div class="top-action-bar clearfix" class="clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2> {t}Letters to the Editor{/t}</h2>
            </div>
            <ul class="old-button">
                <li ng-if="selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="LETTER_AVAILABLE"}
                        <li>
                            <a href="#" id="batch-publish" ng-click="batchToggleAvailable(1, 'backend_ws_contents_batch_toggle_available')">
                                <i class="icon-eye-open"></i>
                                {t}Publish{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="batch-unpublish" ng-click="batchToggleAvailable(0, 'backend_ws_contents_batch_toggle_available')">
                                <i class="icon-eye-close"></i>
                                {t}Unpublish{/t}
                            </a>
                        </li>
                        {/acl}
                        {acl isAllowed="LETTER_DELETE"}
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
                {acl isAllowed="LETTER_CREATE"}
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_letter_create}" class="admin_add" accesskey="N" tabindex="1">
                        <img src="{$params.IMAGE_DIR}list-add.png" title="Nueva" alt="Nueva"><br />{t}New letter{/t}
                    </a>
                </li>
                {/acl}

            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <div class="table-info clearfix">
            {acl hasCategoryAccess=$category}<div class="pull-left"><strong>{t}[% total %] letters{/t}</strong></div>{/acl}
            <div class="pull-right">
                <div class="form-inline">
                    <input type="text" placeholder="{t}Search by title{/t}" name="title" ng-model="filters.search.title_like"/>
                    <label>
                        {t}Status:{/t}
                        <select id="content_status" ng-model="filters.search.content_status">
                            <option value="0"> {t}Pending{/t} </option>
                            <option value="1"> {t}Published{/t} </option>
                            <option value="2"> {t}Rejected{/t} </option>
                        </select>
                    </label>
                </div>
            </div>
        </div>
        <div ng-include="'letters'"></div>

        <script type="text/ng-template" id="letters">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>

        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
                <tr ng-if="contents.length > 0">
                    <th style="width:15px;"><input type="checkbox" ng-checked="areSelected()" ng-click="selectAll($event)"></th>
                    <th>{t}Title{/t}</th>
                    <th>{t}Author{/t}</th>
                    <th style='width:110px;' class="left">{t}Date{/t}</th>
                    <th style='width:110px;'>{t}Image{/t}</th>
                    {acl isAllowed="LETTER_AVAILABLE"}
                    <th class="center" style='width:40px;'>{t}Available{/t}</th>
                    {/acl}
                    <th style='width:90px;' class="right">{t}Actions{/t}</th>
               </tr>
            </thead>

            <tbody>
                <tr ng-if="contents.length == 0">
                    <td class="empty" colspan="10">{t}No available letters.{/t}</td>
                </tr>

                <tr ng-if="contents.length >= 0" ng-repeat="content in contents">
                    <td>
                        <input type="checkbox" ng-checked="isSelected(content.id)" ng-click="updateSelection($event, content.id)">
                    </td>
                    <td>
                        <span rel="tooltip" data-original-title="[% content.body %]">[% content.title %]</span>
                    </td>
                    <td>[% content.author %]: [% content.email %]</td>
                    <td class="left"> [% content.created %] </td>
                    <td >
                        <img ng-if="content.image" ng-src="{$params.IMAGE_DIR}iconos/picture.png" alt="{t}Media{/t}" title="{t}Media element (jpg, image, gif){/t}" />
                    </td>
                    {acl isAllowed="LETTER_AVAILABLE"}
                    <td class="center">
                        {if $letter->content_status eq 0}
                            <a href="{url name=admin_letter_toggleavailable status=1 id=$letter->id letterStatus=$letterStatus page=$page}" title="Publicar">
                                    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicar" /></a>
                            <a href="{url name=admin_letter_toggleavailable id=$letter->id status=2 letterStatus=$letterStatus page=$page}" title="Rechazar">
                                    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Rechazar" /></a>
                        {elseif $letter->content_status eq 2}
                            <a href="{url name=admin_letter_toggleavailable id=$letter->id status=1 letterStatus=$letterStatus page=$page}" title="Publicar">
                                <img border="0" src="{$params.IMAGE_DIR}publish_r.png">
                            </a>
                        {else}
                            <a class="publishing" href="{url name=admin_letter_toggleavailable id=$letter->id status=2 letterStatus=$letterStatus page=$page}" title="Rechazar">
                                <img border="0" src="{$params.IMAGE_DIR}publish_g.png">
                            </a>
                        {/if}
                    </td>
                    {/acl}

                    <td class="right">
                        <div class="btn-group">
                            {acl isAllowed="LETTER_UPDATE"}
                            <button class="btn" ng-click="edit(content.id, 'admin_poll_show')" type="button">
                                <i class="icon-pencil"></i>
                            </button>
                            {/acl}
                            {acl isAllowed="LETTER_DELETE"}
                            <button class="del btn btn-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                <i class="icon-trash icon-white"></i>
                            </button>
                            {/acl}
                       </ul>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="center">
                        <div class="pull-left">
                            [% (page - 1) * 10 %]-[% (page * 10) < total ? page * 10 : total %] of [% total %]
                        </div>
                        <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_contents_list')" page="page" total-items="total" num-pages="pages"></pagination>
                        <div class="pull-right">
                            [% page %] / [% pages %]
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
{/block}
