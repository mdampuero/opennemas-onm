{extends file="base/admin.tpl"}

{block name="footer-js" append}
    <script type="text/javascript">
        jQuery(document).ready(function ($){
            jQuery('.sync_with_server').on('click',function(e, ui) {
                $('#modal-sync').modal('show');
            });
            $('[rel="tooltip"]').tooltip({ placement: 'bottom', html: true });
        });
    </script>
{/block}

{block name="content"}
<div  ng-app="BackendApp" ng-controller="ContentListController" ng-init="init('', { source: '*', title_like: '' }, 'created', 'desc', 'admin_news_agency_ws', '{{$smarty.const.CURRENT_LANGUAGE}}')">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-home fa-lg"></i>
                            {t}News Agency{/t}
                        </h4>
                    </li>
                </ul>
            </div>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    {acl isAllowed="IMPORT_NEWS_AGENCY_CONFIG"}
                    <li class="quicklinks">
                            <a class="btn btn-link" href="{url name=admin_news_agency_servers}">
                                <i class="fa fa-cog"></i>
                            </a>
                    </li>
                    {/acl}
                    {acl isAllowed="ONLY_MASTERS"}
                    <li>
                        <a class="btn btn-link" href="{url name=admin_news_agency_sync}" class="sync_with_server" title="{t}Sync with server{/t}">
                            {t}Sync with server{/t}
                        </a>
                    </li>
                    {/acl}
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li>
                        <a class="btn btn-primary" href="{url name=admin_news_agency}" id="reload_listing" title="{t}Reload list{/t}">
                            {t}Reload list{/t}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.contents.length == 0 }">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section pull-left">
                    <li class="quicklinks">
                      <button class="btn btn-link" ng-click="selected.contents = []; selected.all = 0" tooltip="Clear selection" tooltip-placement="right"type="button">
                        <i class="fa fa-check fa-lg"></i>
                      </button>
                    </li>
                     <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h4>
                            [% selected.contents.length %] {t}items selected{/t}
                        </h4>
                    </li>
                </ul>
                <ul class="nav quick-section pull-right">
                    <li class="quicklinks">
                        <button class="btn btn-link" ng-click="deselectAll()" tooltip="{t}Clear selection{/t}" tooltip-placement="bottom" type="button">
                          {t}Deselect{/t}
                        </button>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li>
                        <a href="#" title="{t}Batch import{/t}" ng-click="open('modal-import-selected', 'admin_news_agency_batch_import')">
                            {t}Import selected{/t}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-navbar filters-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="m-r-10 input-prepend inside search-input no-boarder">
                        <span class="add-on">
                            <span class="fa fa-search fa-lg"></span>
                        </span>
                        <input class="no-boarder" name="title" ng-model="criteria.title" placeholder="{t}Search by title or content{/t}" type="search"/>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        {t}from source{/t}
                    </li>
                    <li class="quicklinks">
                        <select id="source" name="source" ng-model="criteria.source" data-label="{t}Source{/t}">
                            <option value="*">{t}-- All --{/t}</option>
                            {html_options options=$source_names}
                        </select>
                    </li>


                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <span class="info">
                        {t}Results{/t}: [% pagination.total %]
                        </span>
                    </li>
                </ul>
                <ul class="nav quick-section pull-right">
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks form-inline pagination-links">
                        <div class="btn-group">
                            <button class="btn btn-white" ng-click="goToPrevPage()" ng-disabled="isFirstpage()" type="button">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button class="btn btn-white" ng-click="goToNextPage()" ng-disabled="isLastPage()" type="button">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content">
    	{render_messages}

        <div class="grid simple">
            <div class="grid-body no-padding">
                <div ng-include="'contents'"></div>
            </div>
        </div>

        <script type="text/ng-template" id="contents">
            <div class="spinner-wrapper" ng-if="loading">
                <div class="loading-spinner"></div>
                <div class="spinner-text">{t}Loading{/t}...</div>
            </div>
            <table class="table table-hover table-condensed" ng-if="!loading">
                <thead>
                    <tr>
                        <th style="width:15px;">
                            <div class="checkbox checkbox-default">
                                <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                                <label for="select-all"></label>
                            </div>
                        </th>
                        <th>{t}Title{/t}</th>
                        <th class="center">{t}Origin{/t}</th>
                        <th class="center" style='width:10px !important;'>{t}Date{/t}</th>
                        <th class="right" style="width:10px;">{t}Priority{/t}</th>
                    </tr>
                </thead>

                <tbody>
                    <tr ng-if="contents.length == 0">
                        <td colspan="7" class="center">
                            <h4>
                                <b>{t}There is no elements to import{/t}</b>
                            </h4>
                            <p>{t}Try syncing from server by click over the "Sync with server" button above.{/t}</p>
                        </td>
                    </tr>
                    <tr ng-if="contents.length > 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id), already_imported: content.already_imported }">
                        <td>
                            <div class="checkbox check-default">
                                        <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                                        <label for="checkbox[%$index%]"></label>
                                    </div>
                        </td>
                        <td >
                            <span tooltip="[% content.body | striptags | limitTo: 250 %]...">[% content.title %]</span>
                            <small>
                                <div class="tags">
                                    <span ng-repeat="tag in content.tags">[% tag %][% $last ? '' : ', ' %]</span>
                                </div>

                                <span ng-if="content.photos.length > 0 || content.videos.length > 0">
                                    <!--{t}Attachments{/t}:-->
                                    <span ng-if="content.photos.length > 0"><img src="{$params.IMAGE_DIR}template_manager/elements/gallery16x16.png" alt="[{t}With image{/t}] " title="{t}This new has attached images{/t}"> [% content.photos.length %]</span>
                                    <span ng-if="content.videos.length > 0"><img src="{$params.IMAGE_DIR}template_manager/elements/video16x16.png" alt="[{t}With image{/t}] " title="{t}This new has attached images{/t}"> [% content.videos.length %]</span>
                                </span>
                            </small>

                            <div class="listing-inline-actions">
                                <a class="link" href="[% content.view_url %]" title="{t}View{/t}">
                                    <i class="fa fa-eye"></i> {t}View contents{/t}
                                </a>
                                <a class="link link-success" href="[% content.import_url %]" title="{t}Import{/t}">
                                    <span class="fa fa-cloud-download"></span> {t}Import{/t}
                                </a>
                            </div>
                        </td>

                        <td class="nowrap center">
                            <span class="label label-important" style="background-color:[% content.source_color %];">[% content.source_name %]</span>
                        </td>
                        <td class="nowrap center">
                            <span title="[% content.created_time.date %] [% content.created_time.timezone %]">
                                [% content.created_time | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                            </span>
                        </td>

                        <td class="nowrap">
                            <span class="priority">
                                <!--{t}Priority{/t}-->
                                <span ng-if="content.priority == 1" class="badge badge-important">{t}Urgent{/t}</span>
                                <span ng-if="content.priority == 2" class="badge badge-warning">{t}Important{/t}</span>
                                <span ng-if="content.priority == 3" class="badge badge-info">{t}Normal{/t}</span>
                                <span ng-if="content.priority < 1 || content.priority > 3" class="badge">{t}Basic{/t}</span>
                            </span>
                        </td>

                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10" class="center">
                            <div class="pull-left" ng-if="contents.length > 0">
                                {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total|number %]
                            </div>
                            <div class="pull-right" ng-if="contents.length > 0">
                                <pagination max-size="0" direction-links="true"  on-select-page="selectPage(page, 'admin_news_agency_ws')" page="pagination.page" total-items="pagination.total" num-pages="pages"></pagination>
                            </div>
                            <span ng-if="contents.length == 0">&nbsp;</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </script>
    </div>
    {include file="news_agency/modals/_modal_sync_dialog.tpl"}
</div>


<script type="text/ng-template" id="modal-import-selected">
    {include file="news_agency/modals/_modal_batch_import.tpl"}
</script>
</div>
{/block}
