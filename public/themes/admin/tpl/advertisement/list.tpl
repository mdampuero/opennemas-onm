{extends file="base/admin.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
    <form action="{url name=admin_ads}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('advertisement',{ fk_content_categories: -1, type_advertisement: -1, available: -1, with_script: -1, in_litter: 0 }, 'title', 'asc', 'backend_ws_contents_list')">
        <div class="top-action-bar clearfix">
            <div class="wrapper-content">
                <div class="title">
                    <h2>{t}Advertisements{/t}</h2>
                </div>
                <ul class="old-button">
                    {acl isAllowed="ADVERTISEMENT_SETTINGS"}
                        <li>
                            <a href="{url name=admin_ads_config}" title="{t}Config ads module{/t}">
                                <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                                {t}Settings{/t}
                            </a>
                        </li>
                    {/acl}
                    <li class="separator"></li>
                    <li ng-if="shvs.selected.length > 0">
                        <a href="#">
                            <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                            <br/>{t}Batch actions{/t}
                        </a>
                        <ul class="dropdown-menu" style="margin-top: 1px;">
                            {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
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
                            {acl isAllowed="ADVERTISEMENT_DELETE"}
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
                    {acl isAllowed="ADVERTISEMENT_CREATE"}
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
                <div class="pull-left form-inline">
                    <strong>{t}FILTER:{/t}</strong>
                    &nbsp;&nbsp;
                    <select class="select2" id="category" ng-model="shvs.search.fk_content_categories" data-label="{t}Category{/t}">
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
                    &nbsp;&nbsp;
                    <select class="select2" name="filter[type_advertisement]" ng-model="shvs.search.type_advertisement" data-label="{t}Banner type{/t}">
                        {html_options options=$filter_options.type_advertisement selected=$filterType}
                    </select>
                    &nbsp;&nbsp;
                    <select class="input-medium select2" ng-model="shvs.search.available" data-label="{t}Status{/t}">
                        {html_options options=$filter_options.available selected=$filterAvailable}
                    </select>
                     &nbsp;&nbsp;
                    <select class="input-medium select2" ng-model="shvs.search.with_script" data-label="{t}Type{/t}">
                        {html_options options=$filter_options.type}
                    </select>
                </div>
            </div>
            <div ng-include="'advertisements'"></div>
        </div> <!--end wrapper-->
        <script type="text/ng-template" id="advertisements">
            <div class="spinner-wrapper" ng-if="loading">
                <div class="spinner"></div>
                <div class="spinner-text">{t}Loading{/t}...</div>
            </div>
            <table class="table table-hover table-condensed" ng-if="!loading">
                <thead>
                    <tr>
                        <th style="width:15px">
                            <checkbox select-all="true"></checkbox>
                        </th>
                        <th class="title"  style="width:250px">{t}Type{/t}</th>
                        <th>{t}Title{/t}</th>
                        <th class="center" style="width:30px">{t}Permanence{/t}</th>
                        <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}clicked.png" alt="{t}Clicks{/t}" title="{t}Clicks{/t}"></th>
                        {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
                        <th class="center" style="width:40px;">{t}Available{/t}</th>
                        {/acl}
                        <th class="right" style="width:10px"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-if="shvs.contents.length == 0">
                        <td class="empty" colspan="10">
                            {t}There is no advertisement stored in this section{/t}
                        </td>
                    </tr>
                    <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id) }">
                        <td>
                            <checkbox index="[% content.id %]">
                        </td>
                        <td>
                            <label>
                                <img ng-if="content.with_script == 1" src="{$params.IMAGE_DIR}iconos/script_code_red.png" alt="Javascript" title="Javascript"/>
                                <img ng-if="content.with_script != 1 && content.is_flash == 1" src="{$params.IMAGE_DIR}flash.gif" alt="{t}Media flash{/t}" title="{t}Media flash element (swf){/t}" style="width: 16px; height: 16px;"/>
                                <img ng-if="content.with_script != 1 && content.is_flash != 1" src="{$params.IMAGE_DIR}iconos/picture.png" alt="{t}Media{/t}" title="{t}Media element (jpg, image, gif){/t}" />
                                [% shvs.map[content.type_advertisement].name %]
                            </label>
                        </td>
                        <td style="">
                            [% content.title %]
                        </td>
                        <td style="text-align:center;" class="center">
                            <span ng-if="content.type_medida == 'NULL'">{t}Undefined{/t}</span>
                            <span ng-if="content.type_medida == 'CLICK'">{t}Clicks:{/t} [% content.num_clic %]</span>
                            <span ng-if="content.type_medida == 'VIEW'">{t}Viewed:{/t} [% content.num_view %]</span>
                            <span ng-if="content.type_medida == 'DATE'">{t}Date:{/t} [% content.startime %]-[% content.endtime %]</span>
                        </td>
                        <td style="text-align:center;">
                            [% content.num_clic_count %]
                        </td>
                        {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
                        <td class="center" style="width:40px;">
                            <button class="btn-link" ng-class="{ loading: content.loading == 1, published: content.content_status == 1, unpublished: content.content_status == 0 }" ng-click="setContentStatus($index, 'backend_ws_content_set_content_status', content.content_status != 1 ? 1 : 0)" type="button">
                            </button>
                        </td>
                        {/acl}
                        <td class="right">
                            <div class="btn-group">
                            {acl isAllowed="ADVERTISEMENT_UPDATE"}
                                <a class="btn" href="[% edit(content.id, 'admin_ad_show') %]" title="{t}Edit{/t}">
                                    <i class="icon-pencil"></i>
                                </a>
                            {/acl}
                            {acl isAllowed="ADVERTISEMENT_DELETE"}
                              <button class="btn btn-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                    <i class="icon-trash icon-white"></i>
                                </button>
                            {/acl}
                            </ul>
                        </td>
                    </tr>
                </tbody>
                <tfoot >
                    <tr>
                        <td colspan="8" class="center">
                            <div class="pull-left" ng-if="shvs.contents.length > 0">
                                {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                            </div>
                            <div class="pull-right" ng-if="shvs.contents.length > 0">
                                <pagination max-size="0" direction-links="true" on-select-page="selectPage(page, 'backend_ws_contents_list')" num-pages="pages" page="shvs.page" total-items="shvs.total"></pagination>
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
    </form>
{/block}
