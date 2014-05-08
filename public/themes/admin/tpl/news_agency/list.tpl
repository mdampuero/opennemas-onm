{extends file="base/admin.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}

    <script type="text/javascript">

        jQuery(document).ready(function ($){
            jQuery('.sync_with_server').on('click',function(e, ui) {
                $('#modal-sync').modal('show');
            });
            $('[rel="tooltip"]').tooltip({ placement: 'bottom', html: true });

            $('#reload_listing').on('click', function(e, ui) {
                location.reload();
            })
        });
    </script>
{/block}

{block name="content"}
<div  ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('', { source: '*', title_like: '' }, 'created', 'desc', 'admin_news_agency_ws', '{{$smarty.const.CURRENT_LANGUAGE}}')">
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}News Agency{/t}</h2></div>
        <ul class="old-button">
            <li ng-if="shvs.selected.length > 0">
                    <a href="#">
                        <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                        <br/>{t}Batch actions{/t}
                    </a>
                    <ul class="dropdown-menu" style="margin-top: 1px;">
                        <li>
                            <a href="{url name=admin_news_agency_sync}" title="{t}Batch import{/t}" ng-click="open('modal-import-selected', 'admin_news_agency_batch_import')">
                                {t}Batch import{/t}
                            </a>
                        </li>
                    </ul>
                </li>
            <li ng-if="shvs.selected.length > 0">

            </li>
            {acl isAllowed="ONLY_MASTERS"}
            <li>
                <a href="{url name=admin_news_agency_sync}" class="sync_with_server" title="{t}Sync with server{/t}">
                    <img src="{$params.IMAGE_DIR}sync.png" title="{t}Sync list  with server{/t}" alt="{t}Sync with server{/t}" ><br />{t}Sync with server{/t}
                </a>
            </li>
            {/acl}
            <li>
                <a href="{url name=admin_news_agency}" id="reload_listing" title="{t}Reload list{/t}">
                    <img src="{$params.IMAGE_DIR}template_manager/refresh48x48.png" alt="{t}Reload list{/t}" ><br />{t}Reload list{/t}
                </a>
            </li>
            <li class="separator"></li>
            {acl isAllowed="IMPORT_NEWS_AGENCY_CONFIG"}
			<li>
				<a href="{url name=admin_news_agency_servers}" class="admin_add" title="{t}Config{/t}">
				    <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" title="{t}Config{/t}" alt="{t}Config{/t}" ><br />{t}Config{/t}
				</a>
			</li>
            {/acl}
        </ul>
    </div>
</div>
<div class="wrapper-content">
    <div class="warnings-validation"></div><!-- / -->

    <form action="{url name=admin_news_agency}" method="GET" id="formulario">

    	{render_messages}
        <div class="table-info clearfix">
            <div class="pull-left form-inline">
                <strong>{t}FILTER:{/t}</strong>
                &nbsp;&nbsp;
                <input type="search" autofocus id="username" name="title" class="input-large" placeholder="{t}Filter by title or content{/t}" ng-model="shvs.search.title"/>
                <label for="usergroup">
                    {t}and in{/t}
                    <select id="source" name="source" class="select2" ng-model="shvs.search.source" data-label="{t}Source{/t}">
                        <option value="*">{t}-- All --{/t}</option>
                        {html_options options=$source_names}
                    </select>
                </label>
            </div>
        </div>

        <div ng-include="'contents'"></div>

        <script type="text/ng-template" id="contents">
        <div class="spinner-wrapper" ng-if="loading">
            <div class="spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <table class="table table-hover table-condensed" ng-if="!loading">
            <thead>
                <tr>
                    <th style="width:15px;"><checkbox select-all="true"></checkbox></th>
                    <th class="right" style='width:10px !important;'>{t}Priority{/t}</th>
                    <th>{t}Title{/t}</th>
                    <th class="center">{t}Origin{/t}</th>
                    <th class="center" style='width:10px !important;'>{t}Date{/t}</th>
                    <th class="right" style="width:10px;"></th>
                </tr>
            </thead>

            <tbody>
                <tr ng-if="shvs.contents.length == 0">
                    <td colspan="7" class="empty">
                        <h2>
                            <b>{t}There is no elements to import{/t}</b>
                        </h2>
                        <p>{t}Try syncing from server by click over the "Sync with server" button above.{/t}</p>
                    </td>
                </tr>
                <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.id), already_imported: content.already_imported }">
                    <td>
                        <checkbox index="[% content.id %]">
                    </td>
                    <td  class="right">
                        <span ng-if="content.priority == 1" class="badge badge-important">{t}Urgent{/t}</span>
                        <span ng-if="content.priority == 2" class="badge badge-warning">{t}Important{/t}</span>
                        <span ng-if="content.priority == 3" class="badge badge-info">{t}Normal{/t}</span>
                        <span ng-if="content.priority < 1 || content.priority > 3" class="badge">{t}Basic{/t}</span>
                    </td>
                    <td >
                        <span tooltip="[% content.body | striptags | limitTo: 250 %]...">[% content.title %]</span>
                        <small>
                            <div class="tags">
                                <span ng-repeat="tag in content.tags">[% tag %][% $last ? '' : ', ' %]</span>
                            </div>
                            <span ng-if="content.photos.length > 0 || content.videos.length > 0">
                                {t}Attachments{/t}:
                                <span ng-if="content.photos.length > 0"><img src="{$params.IMAGE_DIR}template_manager/elements/gallery16x16.png" alt="[{t}With image{/t}] " title="{t}This new has attached images{/t}"> [% content.photos.length %]</span>
                                <span ng-if="content.videos.length > 0"><img src="{$params.IMAGE_DIR}template_manager/elements/video16x16.png" alt="[{t}With image{/t}] " title="{t}This new has attached images{/t}"> [% content.videos.length %]</span>
                            </span>
                        </small>
                    </td>

                    <td class="nowrap center">
                        <span class="label" style="background-color:[% content.source_color %]};">[% content.source_name %]</span>
                    </td>
                    <td class="nowrap center">
                        <span title="[% content.created_time.date %] [% content.created_time.timezone %]">
                            [% content.created_time.date + ' ' + content.created_time.timezone | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                        </span>
                    </td>

                    <td class="nowrap">
                        <div class="btn-group">
                            <a class="btn btn-small" href="[% content.view_url %]" title="{t}View{/t}">
                                <i class="icon icon-eye-open"></i>
                            </a>
                            <a class="btn btn-small" href="[% content.import_url %]" title="{t}Import{/t}">
                                {t}Import{/t}
                            </a>
                        </div>
                    </td>

                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="center">
                        <div class="pull-left" ng-if="shvs.contents.length > 0">
                            {t}Showing{/t} [% ((shvs.page - 1) * shvs.elements_per_page > 0) ? (shvs.page - 1) * shvs.elements_per_page : 1 %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total|number %]
                        </div>
                        <div class="pull-right" ng-if="shvs.contents.length > 0">
                            <pagination max-size="0" direction-links="true"  on-select-page="selectPage(page, 'admin_news_agency_ws')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
                        </div>
                        <span ng-if="shvs.contents.length == 0">&nbsp;</span>
                    </td>
                </tr>
            </tfoot>
        </table>
        </script>
	</form>
    {include file="news_agency/modals/_modal_sync_dialog.tpl"}
</div>

<script type="text/ng-template" id="modal-import-selected">
    {include file="news_agency/modals/_modal_batch_import.tpl"}
</script>
</div>
{/block}
