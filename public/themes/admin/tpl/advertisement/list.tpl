{extends file="base/admin.tpl"}

{block name="content"}
    <div ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('advertisement',{ fk_content_categories: 0, type_advertisement: -1, content_status: -1, with_script: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
        <div class="page-navbar actions-navbar">
            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <h4>
                                <i class="fa fa-bullhorn fa-lg"></i>
                                {t}Advertisements{/t}
                            </h4>
                        </li>
                    </ul>
                </div>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        {acl isAllowed="ADVERTISEMENT_SETTINGS"}
                            <li class="quicklinks">
                                <a class="btn btn-link" href="{url name=admin_ads_config}">
                                    <i class="fa fa-cog fa-lg"></i>
                                </a>
                            </li>
                            <li class="quicklinks">
                                <span class="h-seperate"></span>
                            </li>
                        {/acl}
                        <li class="quicklinks">
                            <a href="{url name=admin_ad_create category=$category page=$page filter=$filter}" class="btn btn-primary">
                                <i class="fa fa-plus fa-lg"></i>
                                {t}Create{/t}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="page-navbar selected-navbar" ng-class="{ 'collapsed': shvs.selected.length == 0 }">
            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <ul class="nav quick-section pull-left">
                        <li class="quicklinks">
                          <button class="btn btn-link" ng-click="shvs.selected = []; selected.all = 0" tooltip="Clear selection" tooltip-placement="right"type="button">
                            <i class="fa fa-check fa-lg"></i>
                          </button>
                        </li>
                         <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <h4>
                                [% shvs.selected.length %] {t}items selected{/t}
                            </h4>
                        </li>
                    </ul>
                    <ul class="nav quick-section pull-right">

                        <li class="quicklinks">
                            <a class="btn btn-link" ng-href="{url name=manager_ws_instances_list_export}?ids=[% selected.instances.join(); %]" tooltip="{t}Download CSV of selected{/t}" tooltip-placement="bottom">
                                <i class="fa fa-download fa-lg"></i>
                            </a>
                        </li>
                        {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
                            <li class="quicklinks">
                                <button class="btn btn-link"  ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
                                    <i class="fa fa-times fa-lg"></i>
                                </button>
                            </li>
                            <li class="quicklinks">
                                <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
                                    <i class="fa fa-check fa-lg"></i>
                                </button>
                            </li>
                        {/acl}
                        {acl isAllowed="ADVERTISEMENT_DELETE"}
                            <li class="quicklinks">
                                <button class="btn btn-link" ng-click="open('modal-delete-selected', 'backend_ws_contents_batch_send_to_trash')" tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
                                    <i class="fa fa-trash-o fa-lg"></i>
                                </button>
                            </li>
                        {/acl}
                    </ul>
                </div>
            </div>
        </div>

        <div class="page-navbar filters-navbar">
            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <ul class="nav quick-section">
                        <li class="m-r-10 input-prepend inside search-form no-boarder">
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
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li>
                            <select class="select2" name="filter[type_advertisement]" ng-model="shvs.search.type_advertisement" data-label="{t}Banner type{/t}">
                                {html_options options=$filter_options.type_advertisement selected=$filterType}
                            </select>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li>
                            <select class="input-medium select2" ng-model="shvs.search.content_status" data-label="{t}Status{/t}">
                                {html_options options=$filter_options.content_status selected=$filterAvailable}
                            </select>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li>
                            <select class="input-medium select2" ng-model="shvs.search.with_script" data-label="{t}Type{/t}">
                                {html_options options=$filter_options.type}
                            </select>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks hidden-xs">
                            <select class="xmedium" ng-model="pagination.epp">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                            </select>
                        </li>
                        <li class="quicklinks hidden-xs">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="criteria = {  name_like: [ { value: '', operator: 'like' } ]}; orderBy = [ { name: 'last_login', value: 'desc' } ]; pagination = { page: 1, epp: 25 }; refresh()">
                                <i class="fa fa-trash-o fa-lg"></i>
                            </button>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-link" ng-click="refresh()">
                                <i class="fa fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': loading, 'fa-repeat': !loading }"></i>
                            </button>
                        </li>
                    </ul>
                    <ul class="nav quick-section pull-right">
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks form-inline pagination-links">
                            <div class="btn-group">
                                <button class="btn btn-white" ng-click="pagination.page = pagination.page - 1" ng-disabled="pagination.page - 1 < 1" type="button">
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                                <button class="btn btn-white" ng-click="pagination.page = pagination.page + 1" ng-disabled="pagination.page == pagination.pages" type="button">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="grid simple">
                <div class="grid-body no-padding">
                    <div class="spinner-wrapper" ng-if="loading">
                        <div class="spinner"></div>
                        <div class="spinner-text">{t}Loading{/t}...</div>
                    </div>
                    <div class="table-wrapper" ng-if="!loading">
                        <table class="table table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th style="width:15px">
                                        <checkbox select-all="true"></checkbox>
                                    </th>
                                    <th>{t}Title{/t}</th>
                                    <th class="title"  style="width:250px">{t}Type{/t}</th>
                                    <th class="center" style="width:30px">{t}Permanence{/t}</th>
                                    <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}clicked.png" alt="{t}Clicks{/t}" title="{t}Clicks{/t}"></th>
                                    {acl isAllowed="ADVERTISEMENT_AVAILABLE"}
                                    <th class="center" style="width:40px;">{t}Available{/t}</th>
                                    {/acl}
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
                                    <td style="">
                                        [% content.title %]
                                        <div class="listing-inline-actions">
                                            {acl isAllowed="ADVERTISEMENT_UPDATE"}
                                                <a class="link" href="[% edit(content.id, 'admin_advertisement_show') %]" title="{t}Edit{/t}">
                                                    <i class="fa fa-pencil"></i>{t}Edit{/t}
                                                </a>
                                            {/acl}
                                            {acl isAllowed="ADVERTISEMENT_DELETE"}
                                                <button class="link link-danger" ng-click="open('modal-delete', 'backend_ws_content_send_to_trash', $index)" type="button">
                                                    <i class="fa fa-trash-o"></i>{t}Delete{/t}
                                                </button>
                                            {/acl}
                                        </div>
                                    </td>
                                    <td>
                                        <label>
                                            <img ng-if="content.with_script == 1" src="{$params.IMAGE_DIR}iconos/script_code_red.png" alt="Javascript" title="Javascript"/>
                                            <img ng-if="content.with_script != 1 && content.is_flash == 1" src="{$params.IMAGE_DIR}flash.gif" alt="{t}Media flash{/t}" title="{t}Media flash element (swf){/t}" style="width: 16px; height: 16px;"/>
                                            <img ng-if="content.with_script != 1 && content.is_flash != 1" src="{$params.IMAGE_DIR}iconos/picture.png" alt="{t}Media{/t}" title="{t}Media element (jpg, png, gif){/t}" />
                                            [% shvs.map[content.type_advertisement].name %]
                                        </label>
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
                                        <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                                            <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading, 'fa-check text-success' : !content.loading && content.content_status == '1', 'fa-times text-error': !content.loading && content.content_status == '0' }"></i>
                                        </button>
                                    </td>
                                    {/acl}
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="grid-footer clearfix" ng-if="!loading">
                    <tr>
                        <td colspan="8" class="center">
                            <div class="pull-left pagination-info" ng-if="shvs.contents.length > 0">
                                {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total %]
                            </div>
                            <div class="pull-right" ng-if="shvs.contents.length > 0">
                                <pagination class="no-margin" max-size="5" direction-links="true" items-per-page="shvs.elements_per_page" ng-model="shvs.page" on-select-page="selectPage(page, 'backend_ws_contents_list')" num-pages="pages" page="shvs.page" total-items="shvs.total"></pagination>
                            </div>
                        </td>
                    </tr>
                </div>
            </div>
        </div>

        <script type="text/ng-template" id="modal-delete">
            {include file="common/modals/_modalDelete.tpl"}
        </script>
        <script type="text/ng-template" id="modal-delete-selected">
            {include file="common/modals/_modalBatchDelete.tpl"}
        </script>
    </div>
{/block}
