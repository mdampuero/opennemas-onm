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
  <div ng-app="BackendApp" ng-controller="NewsAgencyListCtrl" ng-init="init('', { source: '', title: '', type: 'text' }, 'created', 'desc', 'backend_ws_news_agency_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
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
                <a class="btn btn-link" href="{url name=backend_news_agency_servers_list}">
                  <i class="fa fa-cog fa-lg"></i>
                </a>
              </li>
            {/acl}
            {acl isAllowed="ONLY_MASTERS"}
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <a class="btn btn-primary" href="{url name=admin_news_agency_sync}">
                  <i class="fa fa-retweet"></i>
                  <span class="hidden-xs">{t}Sync{/t}</span>
                </a>
              </li>
            {/acl}
          </ul>
        </div>
      </div>
    </div>
    <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.contents.length == 0 }">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section pull-left">
            <li class="quicklinks">
              <button class="btn btn-link" ng-click="deselectAll()" tooltip="{t}Clear selection{/t}" tooltip-placement="right" type="button">
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
              <a href="#" class="btn btn-link" ng-click="importSelected()" tooltip="{t}Import{/t}" tooltip-placement="left">
                <i class="fa fa-cloud-download"></i>
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
              <input class="no-boarder" name="title" ng-model="criteria.title" placeholder="{t}Search by title{/t}" type="search"/>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs ng-cloak">
              <ui-select name="view" theme="select2" ng-model="criteria.source">
                <ui-select-match>
                  <strong>{t}Source{/t}:</strong> [% $select.selected.name %]
                </ui-select-match>
                <ui-select-choices repeat="item.value as item in extra.sources | filter: $select.search">
                  <div ng-bind-html="item.name | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
            <li class="quicklinks hidden-xs ng-cloak">
              <ui-select name="view" theme="select2" ng-model="criteria.type">
                <ui-select-match>
                  <strong>{t}Type{/t}:</strong> [% $select.selected.name %]
                </ui-select-match>
                <ui-select-choices repeat="item.value as item in extra.type | filter: $select.search">
                  <div ng-bind-html="item.name | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
            <li class="quicklinks hidden-xs ng-cloak">
              <ui-select name="view" theme="select2" ng-model="pagination.epp">
                <ui-select-match>
                  <strong>{t}View{/t}:</strong> [% $select.selected %]
                </ui-select-match>
                <ui-select-choices repeat="item in views  | filter: $select.search">
                  <div ng-bind-html="item | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
            <li class="quicklinks">
              <button class="btn btn-link" ng-click="list('backend_ws_news_agency_list')" tooltip="{t}Reload{/t}" tooltip-placement="bottom" type="button">
                <i class="fa fa-refresh" ng-class="{ 'fa-spin': loading }"></i>
              </button>
            </li>
          </ul>
          <ul class="nav quick-section pull-right ng-cloak">
            <li class="quicklinks">
              <span class="info">
                [% extra.last_sync %]
              </span>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs" ng-if="contents.length > 0">
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
          <table class="table table-hover table-condensed no-margin ng-cloak" ng-if="!loading && contents.length > 0">
            <thead>
              <tr>
                <th class="checkbox-cell">
                  <div class="checkbox checkbox-default">
                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                    <label for="select-all"></label>
                  </div>
                </th>
                <th>{t}Title{/t}</th>
                <th class="center hidden-xs hidden-sm" style="width: 140px;">{t}Origin{/t}</th>
                <th class="center hidden-xs hidden-sm" style="width: 140px;">{t}Date{/t}</th>
                <th class="right hidden-xs" style="width: 50px;">{t}Priority{/t}</th>
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
                  <i class="fa m-r-5" ng-class="{ 'fa-file-text-o': content.type === 'text', 'fa-picture-o': content.type === 'photo', 'fa-film': content.type === 'video' }"></i>
                  [% content.title %]
                  <p>
                    <div class="tags small-text">
                      <span ng-repeat="tag in content.tags">[% tag %][% $last ? '' : ', ' %]</span>
                    </div>
                  </p>
                  <p class="visible-xs-block visible-sm-block">
                    <span class="label label-important m-r-5" style="background-color:[% extra.servers[content.source].color %];">
                      [% extra.servers[content.source].agency_string %]
                    </span>
                  </p>
                  <p class="visible-xs-block visible-sm-block">
                    <span title="[% content.created_time.date %] [% content.created_time.timezone %]">
                      [% content.created_time | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                    </span>
                  </p>
                  <div class="listing-inline-actions">
                    <button class="btn btn-link" ng-click="open('modal-view-content', content)" title="{t}View{/t}">
                      <i class="fa fa-eye"></i> {t}View content{/t}
                    </button>
                    <button class="btn btn-link" ng-click="import(content)" title="{t}Import{/t}">
                      <span class="fa fa-cloud-download"></span> {t}Import{/t}
                    </button>
                    <span class="imported label label-success" ng-if="content.already_imported">{t}Already imported{/t}</span>
                    <span class="btn btn-link" ng-if="content.related.length > 0" ng-click="content.expanded = !content.expanded">
                      <i class="fa fa-caret-right m-r-5" ng-class="{ 'fa-caret-down': content.expanded, 'fa-caret-right': !content.expanded }"></i>
                      [% content.related.length %] {t}Related{/t}
                    </span>
                  </div>
                  <div class="attachments clearfix p-b-10 p-l-10 p-t-15" ng-show="content.expanded && content.related.length > 0">
                    <div class="clearfix p-b-10" ng-repeat="id in content.related">
                      <div class="checkbox check-default pull-left">
                        <input id="checkbox-related-[% content.id %]-[% $index %]" checklist-model="content.import" checklist-value="extra.related[id].id" ng-disabled="!isSelected(content.id)" type="checkbox">
                        <label for="checkbox-related-[% content.id %]-[% $index %]"></label>
                      </div>
                      <div class="m-l-10 pull-left">
                        <label class="pointer" for="checkbox-related-[% content.id %]-[% $index %]">
                          <i class="fa m-l-10 m-r-5" ng-class="{ 'fa-file-text-o': extra.related[id].type === 'text', 'fa-picture-o': extra.related[id].type === 'photo', 'fa-film': extra.related[i].type === 'video' }"></i>
                          [% extra.related[id].title %]
                        </label>
                        <div class="listing-inline-actions">
                          <button class="btn btn-link" ng-click="open('modal-view-content', extra.related[id])" title="{t}View{/t}">
                            <i class="fa fa-eye"></i> {t}View content{/t}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="nowrap center hidden-xs hidden-sm">
                  <span class="label label-important" style="background-color:[% extra.servers[content.source].color %];">
                    [% extra.servers[content.source].agency_string %]
                  </span>
                </td>
                <td class="nowrap center hidden-xs hidden-sm">
                  <span title="[% content.created_time.date %] [% content.created_time.timezone %]">
                    [% content.created_time | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                  </span>
                </td>
                <td class="nowrap">
                  <span class="priority">
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
      {include file="news_agency/modals/_modal_sync_dialog.tpl"}
      <script type="text/ng-template" id="modal-import-selected">
        {include file="news_agency/modals/_modal_batch_import.tpl"}
      </script>
      <script type="text/ng-template" id="modal-view-content">
        {include file="news_agency/modals/_modal_view_content.tpl"}
      </script>
    </div>
  </div>
{/block}
