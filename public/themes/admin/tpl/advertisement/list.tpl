{extends file="base/admin.tpl"}
{block name="header-js" append}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}
    {script_tag src="router.js" language="javascript" bundle="fosjsrouting" basepath="js"}
    {script_tag src="routes.js" language="javascript" common=1 basepath="js"}
    {script_tag src="angular.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="ui-bootstrap-tpls-0.10.0.min.js" language="javascript" bundle="backend" basepath="lib"}
    {script_tag src="app.js" language="jjavascript" bundle="backend" basepath="js"}
    {script_tag src="controllers.js" language="javascript" bundle="backend" basepath="js"}
    {script_tag src="advertisements.js" language="javascript" bundle="backend" basepath="js/controllers"}
{/block}

{block name="content"}
    <form action="{url name=admin_ads}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="AdvertisementsController" ng-init="list(filters)">
        <div class="top-action-bar clearfix">
            <div class="wrapper-content">
                <div class="title">
                    <h2>{t}Advertisements{/t}</h2>
                </div>
                <ul class="old-button">
                    {acl isAllowed="ADVERTISEMENT_DELETE"}
                    <li>
                         <button ng-if="selected.length > 0" title="{t}Delete{/t}" type="button" ng-click="open('modal-delete-all', $index)">
                            <img src="{$params.IMAGE_DIR}trash.png" title="{t}Delete{/t}" alt="{t}Delete{/t}"><br />{t}Delete{/t}
                        </button>
                    </li>
                    {/acl}
                    {acl isAllowed="ADVERTISEMENT_AVAILA"}
                    <li>
                        <button ng-if="selected.length > 0" ng-click="batchToggleAvailable(0)" type="button">
                            <img src="{$params.IMAGE_DIR}publish_no.gif" alt="noFrontpage" ><br />{t}Unpublish{/t}
                        </button>
                    </li>
                    <li>
                        <button ng-if="selected.length > 0" ng-click="batchToggleAvailable(1)" type="button">
                            <img src="{$params.IMAGE_DIR}publish.gif" alt="Frontpage" ><br />{t}Publish{/t}
                        </button>
                    </li>
                    {/acl}
                    {acl isAllowed="ALBUM_SETTINGS"}
                    <li class="separator" ng-if="selected.length > 0"></li>
                        <li>
                            <a href="{url name=admin_ads_config}" title="{t}Config ads module{/t}">
                                <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                                {t}Settings{/t}
                            </a>
                        </li>
                    {/acl}
                    {acl isAllowed="ADVERTISEMENT_CREATE"}
                    <li class="separator"></li>
                    <li>
                        <a href="{url name=admin_ad_create category=$category page=$page filter=$filter}" class="admin_add" accesskey="N" tabindex="1">
                            <img src="{$params.IMAGE_DIR}list-add.png" alt="{t}New{/t}"><br />{t}New{/t}
                        </a>
                    </li>
                    {/acl}
                </ul>
            </div>
        </div>
        <div class="wrapper-content">

            {render_messages}

            <div class="table-info clearfix">
                <div class="pull-right form-inline">
                    <label for="filter[type_advertisement]">{t}Category:{/t}</label>
                    <select class="input-medium select2" id="category" ng-model="filters.search.fk_content_categories">
                        <option value="-1">{t}-- All --{/t}</option>
                        <optgroup label="{t}Special elements{/t}">
                            <option value="0">{t}HOMEPAGE{/t}</option>
                            <option value="4">{t}OPINION{/t}</option>
                            <option value="3">{t}ALBUM{/t}</option>
                            <option value="6">{t}VIDEO{/t}</option>
                        </optgroup>
                        <optgroup label="Categories">
                            {section name=as loop=$allcategorys}
                            {assign var=ca value=$allcategorys[as]->pk_content_category}
                                <option value="{$allcategorys[as]->pk_content_category}">
                                    {$allcategorys[as]->title}
                                    {if $allcategorys[as]->inmenu eq 0}
                                        <span class="inactive">{t}(inactive){/t}</span>
                                    {/if}
                                </option>
                                    {section name=su loop=$subcat[as]}
                                    {assign var=subca value=$subcat[as][su]->pk_content_category}
                                    {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                                        {assign var=subca value=$subcat[as][su]->pk_content_category}
                                        <option value="{$subcat[as][su]->pk_content_category}">
                                            &rarr;
                                            {$subcat[as][su]->title}
                                            {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}
                                                <span class="inactive">{t}(inactive){/t}</span>
                                            {/if}
                                        </option>
                                    {/acl}
                                    {/section}
                            {/section}
                        </optgroup>
                    </select>
                    <label for="filter[type_advertisement]">{t}Banner type:{/t}</label>
                    <select class="input-large select2" name="filter[type_advertisement]" ng-model="filters.search.type_advertisement">
                        {html_options options=$filter_options.type_advertisement selected=$filterType}
                    </select>
                    &nbsp;&nbsp;&nbsp;
                    <label>{t}Status:{/t}</label>
                    <select class="input-medium select2" ng-model="filters.search.available">
                        {html_options options=$filter_options.available selected=$filterAvailable}
                    </select>
                     &nbsp;&nbsp;&nbsp;
                    <label>{t}Type:{/t}</label>
                    <select class="input-medium select2" ng-model="filters.search.with_script">
                        {html_options options=$filter_options.type}
                    </select>
                </div>
            </div>

            <div ng-if="loading" style="text-align: center; padding: 40px 0px;">
                <img src="/assets/images/facebox/loading.gif" style="margin: 0 auto;">
            </div>
            <table class="table table-hover table-condensed" ng-if="!loading">
                <thead>
                    <tr>
                        <th style="width:15px">
                            <input type="checkbox" ng-checked="isSelectedAll()" ng-click="selectAll($event)">
                        </th>
                        <th class="title"  style="width:250px">{t}Type{/t}</th>
                        <th>{t}Title{/t}</th>
                        <th class="center" style="width:30px">{t}Permanence{/t}</th>
                        <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}clicked.png" alt="{t}Clicks{/t}" title="{t}Clicks{/t}"></th>
                        {acl isAllowed="ADVERTISEMENT_AVAILA"}
                        <th class="center" style="width:40px;">{t}Available{/t}</th>
                        {/acl}
                        <th class="right" style="width:70px">{t}Actions{/t}</th>
                    </tr>
                </thead>

                <tbody>
                    <tr ng-if="contents.length == 0">
                        <td class="empty" colspan="10">
                            {t}There is no advertisement stored in this section{/t}
                        </td>
                    </tr>
                    <tr ng-if="contents.length > 0" ng-include="'advertisement'" ng-repeat="content in contents"></tr>
                </tbody>
                <tfoot >
                    <tr>
                        <td colspan="8" class="center">
                            <pagination max-size="5" direction-links="true" on-select-page="selectPage(page)" page="page" total-items="total"></pagination>
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div> <!--end wrapper-->
        <script type="text/ng-template" id="advertisement">
            <td style="text-align:center;">
                <input type="checkbox" class="minput"  id="[% content.id %]" ng-checked="isSelected(content.id)" ng-click="updateSelection($event, content.id)">
            </td>
            <td>
                <label>
                    <img ng-if="content.with_script == 1" src="{$params.IMAGE_DIR}iconos/script_code_red.png" alt="Javascript" title="Javascript"/>
                    <img ng-if="content.with_script != 1 && content.is_flash == 1" src="{$params.IMAGE_DIR}flash.gif" alt="{t}Media flash{/t}" title="{t}Media flash element (swf){/t}" style="width: 16px; height: 16px;"/>
                    <img ng-id="content.with_script != 1 && content.is_flash != 1" src="{$params.IMAGE_DIR}iconos/picture.png" alt="{t}Media{/t}" title="{t}Media element (jpg, image, gif){/t}" />
                    [% map[content.type_advertisement].name %]
                </label>
            </td>
            <td style="">
                [% content.title %]
            </td>
            <td style="text-align:center;" class="center">
                <span ng-if="content.type_medida == 'NULL'">{t}Undefined{/t}</span>
                <span ng-if="content.type_medida == 'CLICK'">{t}Clicks:{/t} [% content.num_clic %]</span>
                <span ng-if="content.type_medida == 'VIEW'">{t}Viewed:{/t} [% num_view.num_clic %]</span>
                <span ng-if="content.type_medida == 'DATE'">{t}Date:{/t} [% content.startime %]-[% content.endtime %]</span>
            </td>
            <td style="text-align:center;">
                [% content.num_clic %]
            </td>
            {acl isAllowed="ADVERTISEMENT_AVAILA"}
            <td class="center" style="width:40px;">
                <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.available == 1, unpublished: content.available == 0 }" ng-click="toggleAvailable($index, content.pk_advertisement)" type="button">
                </button>
            </td>
            {/acl}
            <td class="right">
                <div class="btn-group">
                {acl isAllowed="ADVERTISEMENT_UPDATE"}
                    <button class="btn" ng-click="edit(content.pk_advertisement)" title="{t}Edit{/t}" type="button">
                        <i class="icon-pencil"></i>
                    </button>
                {/acl}
                {acl isAllowed="ADVERTISEMENT_DELETE"}
                  <button class="btn btn-danger" ng-click="open('modal-delete', $index)" type="button">
                        <i class="icon-trash icon-white"></i>
                    </button>
                {/acl}
                </ul>
            </td>
        </script>
        <script type="text/ng-template" id="modal-delete">
            {include file="advertisement/modals/_modalDelete.tpl"}
        </script>
        <script type="text/ng-template" id="modal-delete-all">
            {include file="advertisement/modals/_modalBatchDelete.tpl"}
        </script>
    </form>
{/block}
