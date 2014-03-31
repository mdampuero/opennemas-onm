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
<div  ng-app="BackendApp" ng-controller="ContentCtrl" ng-init="init('', { source: -1, title_like: '' }, 'created', 'desc', 'admin_news_agency_ws')">
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}News Agency{/t}</h2></div>
        <ul class="old-button">
            <li class="batch-actions">
                <a class="importChecked" data-controls-modal="modal-news-agency-batch-import" href="#" title="{t}Batch import{/t}">
                    <img src="{$params.IMAGE_DIR}select.png" title="{t}Batch import{/t}" alt="{t}Batch import{/t}"/><br/>{t}Batch import{/t}
                </a>
            </li>
			<li>
				<a href="{url name=admin_news_agency_sync}" class="sync_with_server" title="{t}Sync with server{/t}">
				    <img src="{$params.IMAGE_DIR}sync.png" title="{t}Sync list  with server{/t}" alt="{t}Sync with server{/t}" ><br />{t}Sync with server{/t}
				</a>
			</li>
			<li>
				<a href="{url name=admin_news_agency}" class="admin_add" title="{t}Reload list{/t}">
				    <img src="{$params.IMAGE_DIR}template_manager/refresh48x48.png" title="{t}Sync list  with server{/t}" alt="{t}Reload list{/t}" ><br />{t}Reload list{/t}
				</a>
			</li>
            <li class="separator"></li>
            {acl isAllowed="IMPORT_NEWS_AGENCY_CONFIG"}
			<li>
				<a href="{url name=admin_news_agency_servers}" class="admin_add" title="{t}Reload list{/t}">
				    <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" title="{t}Config Europapress module{/t}" alt="{t}Config Europapress module{/t}" ><br />{t}Config{/t}
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
                <input type="search" id="username" name="title" class="input-medium" placeholder="{t}Filter by title or content{/t}" ng-model="shvs.search.title"/>
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
                    <th style="width:15px;"><input type="checkbox" ng-checked="areSelected()" ng-click="selectAll($event)"></th>
                    <th class="right" style='width:10px !important;'>{t}Priority{/t}</th>
                    <th>{t}Title{/t}</th>
                    <th>{t}Attachments{/t}</th>
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
                <tr ng-if="shvs.contents.length > 0" ng-repeat="content in shvs.contents" ng-class="{ row_selected: isSelected(content.source_id+','+content.xmlFile) }" class="{if is_array($already_imported) && in_array($element->urn,$already_imported)}already-imported{/if}">
                    <td>
                        <input type="checkbox" name="selected[]" value="[% content.source_id %],[%content.xmlFile %]" class="minput"/>
                    </td>
                    <td  class="right">
                        <span ng-if="content.priority == 1" class="badge badge-important">{t}Urgent{/t}</span>
                        <span ng-if="content.priority == 2" class="badge badge-warning">{t}Important{/t}</span>
                        <span ng-if="content.priority == 3" class="badge badge-info">{t}Normal{/t}</span>
                        <span ng-if="content.priority < 1 || content.priority > 3" class="badge">{t}Basic{/t}</span>
                    </td>
                    <td >
                        [% content.title %]
                        <div class="tags">
                            <span ng-repeat="tag in content.tags">[% tag %][%$last ? '' : ', ' %]</span>
                        </div>
                    </td>

                    <td>
                        <span ng-if="content.photos.length > 0"><img src="{$params.IMAGE_DIR}template_manager/elements/gallery16x16.png" alt="[{t}With image{/t}] " title="{t}This new has attached images{/t}"> [% content.photos.length %]</span>
                        <span ng-if="content.videos.length > 0"><img src="{$params.IMAGE_DIR}template_manager/elements/video16x16.png" alt="[{t}With image{/t}] " title="{t}This new has attached images{/t}"> [% content.videos.length %]</span>
                    </td>
                    <td class="nowrap center">
                        <span class="label" style="background-color:{$servers[$element->source_id]['color']};">{$servers[$element->source_id]['name']}</span>
                    </td>
                    <td class="nowrap center">
                        <span title="[% content.created_time.date %] [% content.created_time.timezone %]">[% content.created_time.date %]  [% content.created_time.timezone %]</span>
                    </td>

                    <td class="nowrap">
                        <ul class="btn-group">
                            <li>
                                <a class="btn btn-small" href="{url name=admin_news_agency_pickcategory source_id=$element->source_id id=$element->xmlFile|urlencode}" title="{t}Import{/t}">
                                    {t}Import{/t}
                                </a>
                            </li>
                        </ul>
                    </td>

                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="center">
                        <div class="pull-left">
                            {t}Showing{/t} [% (shvs.page - 1) * shvs.elements_per_page %]-[% (shvs.page * shvs.elements_per_page) < shvs.total ? shvs.page * shvs.elements_per_page : shvs.total %] {t}of{/t} [% shvs.total|number %]
                        </div>
                        <div class="pull-right">
                            <pagination max-size="0" direction-links="true" direction-links="false" on-select-page="selectPage(page, 'backend_ws_contents_list')" page="shvs.page" total-items="shvs.total" num-pages="pages"></pagination>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
        </script>
	</form>
</div>
{include file="news_agency/modals/_modal_sync_dialog.tpl"}
{include file="news_agency/modals/_modal_batch_import.tpl"}

</div>
{/block}
