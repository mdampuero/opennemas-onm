{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-app="BackendApp" ng-controller="NewsAgencyListCtrl" ng-init="criteria = { epp: 10, page: 1, source: '', title: '', type: 'text'}; init('', 'backend_ws_news_agency_list')">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-microphone fa-lg"></i>
                <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/788682-opennemas-agencias-de-noticias" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                  <i class="fa fa-question"></i>
                </a>
                {t}News Agency{/t}
              </h4>
            </li>
            <li class="quicklinks hidden-xs ng-cloak">
              <h5>
                <small class="p-l-10">
                  [% extra.last_sync %]
                </small>
              </h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              {acl isAllowed="IMPORT_NEWS_AGENCY_CONFIG"}
                <li class="quicklinks">
                  <a class="btn btn-link" href="{url name=backend_news_agency_servers_list}">
                    <i class="fa fa-cog fa-lg"></i>
                  </a>
                </li>
              {/acl}
              {acl isAllowed="MASTER"}
                <li class="quicklinks">
                  <span class="h-seperate"></span>
                </li>
                <li class="quicklinks">
                  <a class="btn btn-primary" href="{url name=admin_news_agency_sync}" id="sync-button">
                    <i class="fa fa-retweet"></i>
                    <span class="hidden-xs">{t}Sync{/t}</span>
                  </a>
                </li>
              {/acl}
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.contents.length == 0 }">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section pull-left">
            <li class="quicklinks">
              <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right" type="button">
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
              <a href="#" class="btn btn-link" ng-click="importSelected()" uib-tooltip="{t}Import{/t}" tooltip-placement="bottom">
                <i class="fa fa-cloud-download"></i>
                <span class="hidden-xs hidden-sm" id="import-button">
                  {t}Import{/t}
                </span>
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
              <ui-select name="source" theme="select2" ng-model="criteria.source">
                <ui-select-match>
                  <strong>{t}Source{/t}:</strong> [% $select.selected.name %]
                </ui-select-match>
                <ui-select-choices repeat="item.value as item in extra.sources | filter: $select.search">
                  <div ng-bind-html="item.name | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
            <li class="quicklinks hidden-xs ng-cloak">
              <ui-select name="type" theme="select2" ng-model="criteria.type">
                <ui-select-match>
                  <strong>{t}Type{/t}:</strong> [% $select.selected.name %]
                </ui-select-match>
                <ui-select-choices repeat="item.value as item in extra.type | filter: $select.search">
                  <div ng-bind-html="item.name | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
            <li class="quicklinks hidden-xs hidden-sm ng-cloak" ng-if="mode !== 'grid'">
              <ui-select name="view" theme="select2" ng-model="criteria.epp">
                <ui-select-match>
                  <strong>{t}View{/t}:</strong> [% $select.selected %]
                </ui-select-match>
                <ui-select-choices repeat="item in views  | filter: $select.search">
                  <div ng-bind-html="item | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
            </li>
            <li class="quicklinks" ng-if="mode !== 'grid'">
              <button class="btn btn-link" ng-click="list('backend_ws_news_agency_list')" uib-tooltip="{t}Reload{/t}" tooltip-placement="bottom" type="button">
                <i class="fa fa-refresh fa-lg" ng-class="{ 'fa-spin': loading }"></i>
              </button>
            </li>
          </ul>
          <ul class="nav quick-section pull-right ng-cloak visible-md visible-lg" ng-if="contents.length > 1 && mode !== 'grid'">
            <li class="quicklinks hidden-xs" ng-if="contents.length > 0">
              <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="grid simple" ng-if="criteria.type === 'text'">
        <div class="grid-body no-padding">
          <div class="spinner-wrapper" ng-if="loading">
            <div class="loading-spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
          </div>
          <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
            <div class="center">
              <h4>{t}There is no elements to import{/t}</h4>
              {acl isAllowed="MASTER"}
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
              <tr ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id), already_imported: imported.indexOf(content.urn) !== -1 }">
                <td class="checkbox-cell">
                  <div class="checkbox check-default" ng-if="imported.indexOf(content.urn) === -1">
                    <input id="checkbox[% $index %]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                    <label for="checkbox[% $index %]"></label>
                  </div>
                </td>
                <td>
                  <div class="pointer p-b-10" ng-click="expanded[$index] = !expanded[$index]">
                    <i class="fa fa-caret-right m-r-5" ng-class="{ 'fa-caret-down': expanded[$index], 'fa-caret-right': !expanded[$index] }" ng-if="content.related.length > 0" style="width: 8px;"></i>
                    [% content.title %]
                  </div>
                  <p class="visible-xs-block visible-sm-block">
                    <span class="label label-important m-r-5" style="background-color:[% extra.servers[content.source].color %];">
                      [% extra.servers[content.source].agency_string %]
                    </span>
                  </p>
                  <p class="visible-xs-block visible-sm-block">
                    <span title="[% content.created_time.date %] [% content.created_time.timezone %]">
                      [% content.created_time | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : extra.timezone %]
                    </span>
                  </p>
                  <div ng-show="!expanded[$index]" >
                    <span ng-repeat="id in content.related">
                      <img class="img-thumbnail" ng-class="{ 'selected': content.import && content.import.indexOf(id) !== -1 }" ng-if="extra.related[id].type === 'photo'" ng-src="[% routing.generate('backend_ws_news_agency_show_image', { source: extra.related[id].source, id: extra.related[id].id }) %]" style="height: 48px;" />
                    </span>
                  </div>
                  <div class="related clearfix p-b-10" ng-show="expanded[$index] && content.related.length > 0">
                    <div class="p-b-10" ng-class="{ 'col-xs-4': extra.related[id].type !== 'text' }" ng-repeat="id in content.related">
                      <div class="checkbox check-default" ng-class="{ 'selected': content.import && content.import.indexOf(id) !== -1 }">
                        <input id="checkbox-related-[% content.id %]-related-[% $index %]" checklist-model="content.import" checklist-value="id" ng-disabled="!isSelected(content.id) || (content.import.length > 1 && content.import.indexOf(id) === -1)" type="checkbox">
                        <label for="checkbox-related-[% content.id %]-related-[% $index %]" ng-class="{ 'p-t-7 p-l-7': extra.related[id].type !== 'text' }">
                          <i class="fa m-l-30 m-r-5 fa-file-text-o" ng-show="extra.related[id].type === 'text'"></i>
                          <span ng-if="extra.related[id].type === 'text'">[% extra.related[id].title %]</span>
                          <div class="img-thumbnail-wrapper">
                            <img class="img-thumbnail" ng-class="{ 'selected': content.import && content.import.indexOf(id) !== -1 }" ng-src="[% routing.generate('backend_ws_news_agency_show_image', { source: extra.related[id].source, id: extra.related[id].id }) %]" />
                            <span class="badge badge-success no-animate" ng-if="imported.indexOf(content.urn) !== -1">{t}Imported{/t}</span>
                          </div>
                        </label>
                      </div>
                      <div class="m-l-15" ng-show="extra.related[id].type === 'text'">
                        <div class="listing-inline-actions">
                          <button class="btn btn-link" ng-click="open('modal-view-content', extra.related[id])" title="{t}View{/t}">
                            <i class="fa fa-eye"></i> {t}View content{/t}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="listing-inline-actions">
                    <button class="btn btn-link" ng-click="preview(content)" title="{t}View{/t}">
                      <i class="fa fa-eye"></i> {t}View content{/t}
                    </button>
                    <span class="badge badge-success no-animate" ng-if="imported.indexOf(content.urn) !== -1">{t}Imported{/t}</span>
                    <button class="btn btn-link no-animate" ng-click="import(content)" ng-if="imported.indexOf(content.urn) === -1" title="{t}Import{/t}">
                      <span class="fa fa-cloud-download"></span> {t}Import{/t}
                    </button>
                  </div>
                </td>
                <td class="nowrap center hidden-xs hidden-sm">
                  <span class="badge badge-success" style="background-color:[% extra.servers[content.source].color %];">
                    [% extra.servers[content.source].agency_string %]
                  </span>
                </td>
                <td class="nowrap center hidden-xs hidden-sm">
                  <span title="[% content.created_time.date %] [% content.created_time.timezone %]">
                    [% content.created_time | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : extra.timezone %]
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
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
          </div>
        </div>
      </div>
      <div class="content-wrapper">
        <div class="ng-cloak spinner-wrapper" ng-if="mode === 'grid' && loading && contents.length < total">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div class="clearfix infinite-row ng-cloak" ng-if="mode === 'grid'">
          <div class="listing-no-contents ng-cloak" ng-if="!loading && !loadingMore && contents.length == 0">
            <div class="center">
              <h4>{t}Unable to find any image that matches your search.{/t}</h4>
              <h6>{t}Maybe changing any filter could help or add one using the "Upload" button above.{/t}</h6>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-15 infinite-col media-item selectable" ng-class="{ 'selected': isSelected(content.id) }" ng-repeat="content in contents">
            <div class="dynamic-image-placeholder no-margin" ng-click="select(content);xsOnly($event, toggle, content)">
              <dynamic-image class="img-thumbnail" path="[% routing.generate('backend_ws_news_agency_show_image', { source: content.source, id: content.id }) %]" raw="true">
                <div class="hidden-select" ng-click="toggle(content)"></div>
                <div class="thumbnail-actions thumbnail-actions-fixed text-right">
                  <span class="badge badge-success" ng-if="imported.indexOf(content.urn) !== -1">{t}Imported{/t}</span>
                </div>
              </dynamic-image>
            </div>
          </div>
        </div>
        <div class="ng-cloak p-t-15 p-b-15 pointer text-center" ng-click="scroll('backend_ws_news_agency_list')" ng-if="!loading && criteria.type === 'photo' && total != contents.length">
          <h5>
            <i class="fa fa-circle-o-notch fa-spin fa-lg" ng-if="loadingMore"></i>
            <span ng-if="!loadingMore">{t}Load more{/t}</span>
            <span ng-if="loadingMore">{t}Loading{/t}</span>
          </h5>
        </div>
        <div class="infinite-row master-row ng-cloak">
          <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-15 infinite-col media-item">
          </div>
        </div>
      </div>
      <div class="content-sidebar hidden-sm ng-cloak" ng-if="criteria.type === 'photo'">
        <div class="center p-t-15" ng-if="!selected.lastSelected">
          <h4>{t}No item selected{/t}</h4>
          <h6>{t}Click in one item to show information about it{/t}</h6>
        </div>
        <h4 class="ng-cloak" ng-show="selected.lastSelected">{t}Image details{/t}</h4>
        <div ng-if="selected.lastSelected">
          <div class="pointer thumbnail-wrapper" ng-click="open('modal-image', selected.lastSelected)">
              <dynamic-image class="img-thumbnail" ng-model="selected.lastSelected.url" raw="true"></dynamic-image>
          </div>
          <ul class="media-information">
            <li>
              <strong>[% selected.lastSelected.name %]</strong>
            </li>
            <li>
              <strong>{t}Agency{/t}:</strong>
              [% selected.lastSelected.agency_name %]
            </li>
            <li>[% selected.lastSelected.created | moment %]</li>
            <li><strong>{t}Size:{/t}</strong> [% selected.lastSelected.width %] x [% selected.lastSelected.height %] ([% selected.lastSelected.size %] KB)</li>
            <li>
              <div class="form-group">
                <label for="description">
                  <strong>{t}Description{/t}</strong>
                </label>
                <p>[% selected.lastSelected.title %]</p>
              </div>
            </li>
          </ul>
        </div>
      </div>
      {include file="news_agency/modals/_modal_sync_dialog.tpl"}
      <script type="text/ng-template" id="modal-import-selected">
        {include file="news_agency/modals/_modal_batch_import.tpl"}
      </script>
      <script type="text/ng-template" id="modal-view-content">
        {include file="news_agency/modals/_modal_view_content.tpl"}
      </script>
      <script type="text/ng-template" id="modal-image">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
          <h4 class="modal-title">{t}Image preview{/t}</h4>
        </div>
        <div class="modal-body">
          <div class="resource">
            <img class="img-responsive" ng-src="[% template.selected.url %]"/>
          </div>
        </div>
      </script>
    </div>
  </div>
{/block}
