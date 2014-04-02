{extends file="base/admin.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
<form action="{url name=admin_specials}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('special', { available: -1, category_name: -1, in_home: {if $category == 'widget'}1{else}-1{/if}, title_like: '', in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list')">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Specials{/t} :: </h2>
                <div class="section-picker">
                    <div class="title-picker btn"><span class="text">{if $category == 'widget'}{t}Widget Home{/t}{else}{t}Listing{/t}{/if}</span> <span class="caret"></span></div>
                    <div class="options">
                        <a href="{url name=admin_specials_widget}" {if $category =='widget'}class="active"{/if}>{t}Widget Home{/t}</a>
                        <a href="{url name=admin_specials}" {if $category !=='widget'}class="active"{/if}>{t}Listing{/t}</a>
                    </div>
                </div>
            </div>
            <ul class="old-button">
                {acl isAllowed="SPECIAL_SETTINGS"}
                    <li>
                        <a href="{url name=admin_specials_config}" class="admin_add" title="{t}Config special module{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
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
                        {acl isAllowed="SPECIAL_AVAILABLE"}
                            <li>
                                <a href="#" ng-click="batchSetContentStatus(1, 'backend_ws_contents_batch_set_content_status')">
                                    <i class="icon-eye-open"></i>
                                    {t}Publish{/t}
                                </a>
                            </li>
                            <li>
                                <a href="#" ng-click="batchSetContentStatus(0, 'backend_ws_contents_batch_set_content_status')">
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
                        {acl isAllowed="SPECIAL_DELETE"}
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
                {acl isAllowed="SPECIAL_WIDGET"}
                     {if $category eq 'widget'}
                        <li class="separator"></li>
                        <li>
                            <a href="#" class="admin_add" onClick="javascript:saveSortPositions('{url name=admin_special_widget_save_positions category=$category page=$page}');" title="Guardar Positions" alt="Guardar Posiciones">
                                <img border="0" src="{$params.IMAGE_DIR}save.png" alt="Guardar Posiciones"><br />{t}Save positions{/t}
                            </a>
                        </li>
                    {/if}
                {/acl}
                {acl isAllowed="SPECIAL_CREATE"}
                <li>
                    <a href="{url name=admin_special_create}">
                        <img src="{$params.IMAGE_DIR}special.png" alt="Nuevo Special"><br />{t}New special{/t}
                    </a>
                </li>
                {/acl}
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}
        {if $category == 'widget'}
            <div class="messages" ng-if="{$total_elements_widget} > 0 && shvs.total != {$total_elements_widget}">
                <div class="alert alert-info">
                    <button class="close" data-dismiss="alert">×</button>
                    {t 1=$total_elements_widget}You must put %1 specials in the HOME{/t}<br>
                </div>
            </div>
        {/if}

        <div class="table-info clearfix">
            <div class="pull-left">
                <div class="form-inline">
                    <strong>{t}FILTER:{/t}</strong>
                    &nbsp;&nbsp;
                    <input type="text" autofocus placeholder="{t}Search by title{/t}" name="title" ng-model="shvs.search.title_like"/>
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
                    <select class="select2"  name="status" ng-model="shvs.search.available" data-label="{t}Status{/t}">
                        <option value="-1"> {t}-- All --{/t} </option>
                        <option value="1"> {t}Published{/t} </option>
                        <option value="0"> {t}No published{/t} </option>
                    </select>

                    <input type="hidden" name="in_home" ng-model="shvs.search.in_home">
                </div>
            </div>
        </div>
        <div ng-include="'specials'"></div>

        <script type="text/ng-template" id="specials">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>

        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
                <tr>
                    <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                    <th class="title">{t}Title{/t}</th>
                    <th style="width:65px;" class="center">{t}Section{/t}</th>
                    <th class="center" style="width:100px;">Created</th>
                    {acl isAllowed="SPECIAL_AVAILABLE"}<th class="center" style="width:35px;">{t}Published{/t}</th>{/acl}
                    {acl isAllowed="SPECIAL_FAVORITE"}{if $category!='widget'}<th class="center" style="width:35px;">{t}Favorite{/t}</th>{/if}{/acl}
                    {acl isAllowed="SPECIAL_HOME"}<th class="center" style="width:35px;">{t}Home{/t}</th>{/acl}
                    <th class="right" style="width:110px;">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody class="sortable">
                <tr ng-if="shvs.contents.length == 0">
                    <td class="empty" colspan="10">{t}No available specials.{/t}</td>
                </tr>
                <tr ng-if="shvs.contents.length >= 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected($index) }">
                    <td class="center">
                        <checkbox index="[% $index %]">
                    </td>
                    <td>
                        [% content.title %]
                    </td>
                    <td class="center">
                        [% content.category_name %]
                    </td>
                    <td class="center">
                        [% content.created | moment %]
                    </td>
                    {acl isAllowed="SPECIAL_AVAILABLE"}
                    <td class="center">
                        <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.content_status == 1, unpublished: content.content_status == 0 }" ng-click="setContentStatus($index, 'backend_ws_content_set_content_status', content.content_status != 1 ? 1 : 0)" type="button"></button>
                    </td>
                    {/acl}

                    {if $category!='widget'}
                    {acl isAllowed="SPECIAL_FAVORITE"}
                    <td class="center">
                    <button class="btn-link" ng-class="{ loading: content.favorite_loading == 1, 'favorite': content.favorite == 1, 'no-favorite': content.favorite != 1 }" ng-click="toggleFavorite(content.id, $index, 'backend_ws_content_toggle_favorite')" type="button"></button>
                    </td>
                    {/acl}
                    {/if}

                    {acl isAllowed="SPECIAL_HOME"}
                    <td class="center">
                        <button class="btn-link" ng-class="{ 'loading': content.home_loading == 1, 'go-home': content.in_home == 1, 'no-home': content.in_home == 0 }" ng-if="content.author.meta.is_blog != 1" ng-click="toggleInHome(content.id, $index, 'backend_ws_content_toggle_in_home')" type="button"></button>
                    </td>
                    {/acl}

                    <td class="right">
                        <div class="btn-group">
                            {acl isAllowed="SPECIAL_UPDATE"}
                            <button class="btn" ng-click="edit(content.id, 'admin_special_show')" type="button">
                                <i class="icon-pencil"></i>
                            </button>
                            {/acl}
                            {acl isAllowed="SPECIAL_DELETE"}
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
                        <div class="pull-left" ng-if="shvs.contents.length > 0">
                            {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                        </div>
                        <div class="pull-right" ng-if="shvs.contents.length > 0">
                            <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_contents_list')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
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
    </div>
</form>
{/block}
