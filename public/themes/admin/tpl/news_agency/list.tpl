{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
  jQuery(document).ready(function ($){
    jQuery('.sync_with_server').on('click',function(e, ui) {
      $('#modal-sync').modal('show');
    });
  });
</script>
{/block}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init('', { source: '*', title_like: '' }, 'created', 'desc', 'admin_news_agency_ws', '{{$smarty.const.CURRENT_LANGUAGE}}')">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-microphone fa-lg"></i>
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
              <i class="fa fa-cog fa-lg"></i>
            </a>
          </li>
          {/acl}
          {acl isAllowed="ONLY_MASTERS"}
          <li>
            <a class="btn btn-white" href="{url name=admin_news_agency_sync}" class="sync_with_server" title="{t}Sync with server{/t}">
              <span class="fa fa-cloud"></span> <span class="hidden-xs">{t}Sync with server{/t}</span>
            </a>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          {/acl}
          <li>
            <button class="btn btn-primary" ng-click="list('admin_news_agency_ws')" title="{t}Reload list{/t}" type="button">
              <span class="fa fa-refresh"></span>
              <span class="hidden-xs">{t}Reload list{/t}</span>
            </button>
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
            <button class="btn btn-link" ng-click="deselectAll()" tooltip="{t}Clear selection{/t}" tooltip-placement="right"type="button">
              <i class="fa fa-arrow-left fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <h4>
              [% selected.contents.length %] <span class="hidden-xs">{t}items selected{/t}</span>
            </h4>
          </li>
        </ul>
        <ul class="nav quick-section pull-right">
          <li>
            <a href="#" class="btn btn-link" title="{t}Batch import{/t}" ng-click="open('modal-import-selected', 'admin_news_agency_batch_import')">
              {t}Import{/t}
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
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs">
            <select id="source" name="source" ng-model="criteria.source" data-label="{t}Source{/t}" class="select2">
              <option value="*">{t}-- All --{/t}</option>
              {html_options options=$source_names}
            </select>
          </li>
          <li class="quicklinks hidden-xs">
            <ui-select name="view" theme="select2" ng-model="pagination.epp">
              <ui-select-match>
                <strong>{t}View{/t}:</strong> [% $select.selected %]
              </ui-select-match>
              <ui-select-choices repeat="item in views  | filter: $select.search">
                <div ng-bind-html="item | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
        </ul>
        <ul class="nav quick-section pull-right ng-cloak" ng-if="contents.length > 0">
          <li class="quicklinks hidden-xs">
            <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="content">
   <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="spinner-wrapper" ng-if="loading">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
        <div class="center">
          <h4>{t}There is no elements to import{/t}</h4>
          {acl isAllowed="ONLY_MASTERS"}
          <h6>{t}Try syncing from server by click over the "Sync with server" button above.{/t}</h6>
          {/acl}
        </div>
      </div>
      <table class="table table-hover table-condensed ng-cloak" ng-if="!loading && contents.length > 0">
        <thead>
          <tr>
            <th class="checkbox-cell">
              <div class="checkbox checkbox-default">
                <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                <label for="select-all"></label>
              </div>
            </th>
            <th>{t}Title{/t}</th>
            <th class="center hidden-xs hidden-sm">{t}Origin{/t}</th>
            <th class="center hidden-xs hidden-sm" style='width:10px !important;'>{t}Date{/t}</th>
            <th class="right hidden-xs" style="width:10px;">{t}Priority{/t}</th>
          </tr>
        </thead>

        <tbody>
          <tr ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id), already_imported: content.already_imported }">
            <td class="checkbox-cell">
              <div class="checkbox check-default">
                <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                <label for="checkbox[%$index%]"></label>
              </div>
            </td>
            <td >
              <span tooltip="[% content.body | striptags | limitTo: 250 %]...">[% content.title %]</span>
              <p>
                <div class="tags small-text">
                  <span ng-repeat="tag in content.tags">[% tag %][% $last ? '' : ', ' %]</span>
                </div>

                <span ng-if="content.photos.length > 0 || content.videos.length > 0" class=" small-text">
                  <!--{t}Attachments{/t}:-->
                  <span ng-if="content.photos.length > 0"><img src="{$params.IMAGE_DIR}template_manager/elements/album.png" alt="[{t}With image{/t}] " title="{t}This new has attached images{/t}"> [% content.photos.length %]</span>
                  <span ng-if="content.videos.length > 0"><img src="{$params.IMAGE_DIR}template_manager/elements/video.png" alt="[{t}With video{/t}] " title="{t}This new has attached images{/t}"> [% content.videos.length %]</span>
                </span>
              </p>

              <p class="visible-xs-block visible-sm-block">
                <span class="label label-important" style="background-color:[% content.source_color %];">[% content.source_name %]</span>
                <span title="[% content.created_time.date %] [% content.created_time.timezone %]">
                  [% content.created_time | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                </span>
              </p>


              <div class="listing-inline-actions">
                <a class="link" href="[% content.view_url %]" title="{t}View{/t}">
                  <i class="fa fa-eye"></i> {t}View contents{/t}
                </a>
                <a class="link link-success" href="[% content.import_url %]" title="{t}Import{/t}">
                  <span class="fa fa-cloud-download"></span> {t}Import{/t}
                </a>
                <span class="imported label label-success" ng-if="content.already_imported">{t}Already imported{/t}</span>
              </div>
            </td>

            <td class="nowrap center hidden-xs hidden-sm">
              <span class="label label-important" style="background-color:[% content.source_color %];">[% content.source_name %]</span>
            </td>
            <td class="nowrap center hidden-xs hidden-sm">
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
      </table>
    </div>
    <div class="grid-footer clearfix ng-cloak" ng-if="!loading && contents.length > 0">
      <div class="pull-right">
        <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
      </div>
    </div>
  </div>
</div>
{include file="news_agency/modals/_modal_sync_dialog.tpl"}
</div>


<script type="text/ng-template" id="modal-import-selected">
  {include file="news_agency/modals/_modal_batch_import.tpl"}
</script>
</div>
{/block}
