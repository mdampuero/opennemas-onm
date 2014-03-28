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
    {script_tag src="shared-vars.js" language="javascript" bundle="backend" basepath="js/services"}

    <script>
        var album_manager_urls = {
            batch_delete: '{url name=admin_album_batchdelete category=$category}'
        }
    </script>
{/block}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('album', { available: -1, title_like: '', category_name: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list')">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Albums{/t} :: </h2>
                <div class="section-picker">
                    <div class="title-picker btn"><span class="text">{if $category == 'widget'}{t}Widget home{/t}{else}{t}All categories{/t}{/if}</span> <span class="caret"></span></div>
                    <div class="options">
                        <h4>{t}Other{/t}</h4>
                        <a href="{url name=admin_albums_widget}" {if $category == 'widget'}class="active"{/if}>{t}WIDGET HOME{/t}</a>
                        <a href="{url name=admin_albums}" {if $category !== 'widget'}class="active"{/if}>{t}Listing{/t}</a>
                    </div>
                </div>
            </div>
            <ul class="old-button">
                {acl isAllowed="ALBUM_SETTINGS"}
                <li>
                    <a href="{url name=admin_albums_config}" title="{t}Config album module{/t}">
                        <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                        {t}Settings{/t}
                    </a>
                </li>
                <li class="separator"></li>
                {/acl}
                <li ng-if="shvs.selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        {acl isAllowed="ALBUM_AVAILABLE"}
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
                        <li class="divider"></li>
                        <li>
                            <a href="#" ng-click="batchToggleInHome(1, 'backend_ws_contents_batch_toggle_in_home')">
                                <i class="go-home"></i>
                                {t escape="off"}In home{/t}
                            </a>
                        </li>
                        <li>
                            <a href="#" ng-click="batchToggleInHome(0, 'backend_ws_contents_batch_toggle_in_home')">
                                <i class="no-home"></i>
                                {t escape="off"}Drop from home{/t}
                            </a>
                        </li>
                        {/acl}
                        {acl isAllowed="ALBUM_DELETE"}
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
                {acl isAllowed="ALBUM_WIDGET"}
                     {if $category eq 'widget'}
                        <li class="separator"></li>
                        <li>
                            <a href="#" onClick="javascript:saveSortPositions('{url name=admin_albums_savepositions}');" title="{t}Save positions{/t}">
                                <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save positions{/t}"><br />{t}Save positions{/t}
                            </a>
                        </li>
                    {/if}
                {/acl}
                {acl isAllowed="ALBUM_CREATE"}
                <li>
                    <a href="{url name=admin_album_create category=$category}" title="{t}New album{/t}" >
                        <img src="{$params.IMAGE_DIR}/album.png" alt="{t}New album{/t}"><br />{t}New album{/t}
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
                    <input type="text" placeholder="{t}Search by title{/t}" name="title" ng-model="shvs.search.title_like"/>
                    &nbsp;&nbsp;
                    <select class="select2" id="category" ng-model="shvs.search.category_name" data-label="{t}Category{/t}">
                        <option value="-1">{t}-- All --{/t}</option>
                            {section name=as loop=$allcategorys}
                                {assign var=ca value=$allcategorys[as]->pk_content_category}
                                <option value="{$allcategorys[as]->name}">
                                    {$allcategorys[as]->title}
                                    {if $allcategorys[as]->inmenu eq 0}
                                        <span class="inactive">{t}(inactive){/t}</span>
                                    {/if}
                                </option>
                                {section name=su loop=$subcat[as]}
                                {assign var=subca value=$subcat[as][su]->pk_content_category}
                                {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                                    {assign var=subca value=$subcat[as][su]->pk_content_category}
                                    <option value="{$subcat[as][su]->name}">
                                        &rarr;
                                        {$subcat[as][su]->title}
                                        {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}
                                            <span class="inactive">{t}(inactive){/t}</span>
                                        {/if}
                                    </option>
                                {/acl}
                                {/section}
                            {/section}
                    </select>
                    &nbsp;&nbsp;
                    <select class="select2" name="status" ng-model="shvs.search.available" data-label="{t}Status{/t}">
                        <option value="-1"> {t}-- All --{/t} </option>
                        <option value="1"> {t}Published{/t} </option>
                        <option value="0"> {t}No published{/t} </option>
                    </select>
                    &nbsp;&nbsp;
                    <input type="hidden" name="in_home" ng-model="shvs.search.in_home">
                </div>
            </div>
        </div>
        <div ng-include="'albums'"></div>

        <script type="text/ng-template" id="albums">
            <div class="spinner-wrapper" ng-if="loading">
                <div class="spinner"></div>
                <div class="spinner-text">{t}Loading{/t}...</div>
            </div>

            <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
                <tr>
                    <th style="width:15px;"><input type="checkbox" ng-checked="areSelected()" ng-click="selectAll($event)"></th>
                    <th></th>
                    <th class="title">{t}Title{/t}</th>
                    {if $category=='widget' || $category=='all'}<th style="width:65px;" class="left">{t}Section{/t}</th>{/if}
                    <th class="left nowrap" style="width:100px;">Created</th>
                    <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                    <th class="center" style="width:35px;">{t}Published{/t}</th>
                    {if $category!='widget'} <th class="center" style="width:35px;">{t}Favorite{/t}</th>{/if}
                    <th class="center" style="width:35px;">{t}Home{/t}</th>
                    <th class="right" style="width:10px;"></th>
                </tr>
            </thead>
            <tbody class="sortable">
            <tr ng-if="shvs.contents.length == 0">
                <td class="empty" colspan="10">{t}No available albums.{/t}</td>
            </tr>

            <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }" data-id="[% content.id %]">
                <td>
                    <input type="checkbox" ng-checked="isSelected(content.id)" ng-click="updateSelection($event, content.id)">
                </td>
                <td>
                    <span ng-if="content.cover != ''">
                        <img ng-src="{$smarty.const.MEDIA_IMG_PATH_WEB}[% content.cover %]" style="max-height:60px; max-width:80px;"/>
                    </span>
                    <span ng-if="content.cover == ''">
                        <img ng-src="http://placehold.it/80x60" />
                    </span>
                </td>
                <td>
                    [% content.title %]
                </td>
                {if $category=='widget' || $category=='all'}
                <td class="left">
                     [% content.category_name %]
                </td>
                {/if}

                <td class="center nowrap">[% content.created %]</td>
                <td class="center">[% content.views %]</td>

                {acl isAllowed="ALBUM_AVAILABLE"}
                <td class="center">
                    <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.content_status == 1, unpublished: content.content_status == 0 }" ng-click="setContentStatus($index, 'backend_ws_content_set_content_status', content.content_status != 1 ? 1 : 0)" type="button"></button>
                </td>
                {/acl}
                {acl isAllowed="ALBUM_FAVORITE"}
                <td class="center">
                    <button class="btn-link" ng-class="{ loading: content.favorite_loading == 1, 'favorite': content.favorite == 1, 'no-favorite': content.favorite != 1 }" ng-click="toggleFavorite(content.id, $index, 'backend_ws_content_toggle_favorite')" type="button"></button>
                </td>
                {/acl}
                {acl isAllowed="ALBUM_HOME"}
                <td class="center">
                    <button class="btn-link" ng-class="{ 'loading': content.home_loading == 1, 'go-home': content.in_home == 1, 'no-home': content.in_home == 0 }" ng-click="toggleInHome(content.id, $index, 'backend_ws_content_toggle_in_home')" type="button"></button>
                </td>
                {/acl}

                <td class="right">
                    <div class="btn-group">
                        {acl isAllowed="ALBUM_UPDATE"}
                        <button class="btn" ng-click="edit(content.id, 'admin_album_show')" type="button">
                            <i class="icon-pencil"></i>
                        </button>
                        {/acl}
                        {acl isAllowed="ALBUM_DELETE"}
                        <button class="del btn btn-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                            <i class="icon-trash icon-white"></i>
                        </button>
                        {/acl}
                    </div>
                </td>

            </tr>
            </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10" class="center">
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
    </div>
</form>
    <script>
    // <![CDATA[
        {if $category eq 'widget'}
            jQuery(document).ready(function() {
                makeSortable();
            });
        {/if}
    // ]]>
    </script>
{/block}
