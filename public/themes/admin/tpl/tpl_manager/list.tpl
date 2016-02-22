{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="CacheManagerCtrl" ng-init="init('caches', { type: -1 }, 'created', 'desc', 'backend_ws_cachemanager_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-database"></i>
              {t}Cache Manager{/t}
            </h4>
          </li>
        </ul>
      </div>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a href="{url name=admin_tpl_manager_config}" class="btn btn-link">
              <i class="fa fa-cog fa-lg"></i>
            </a>
          </li>
          <li class="quicklinks"><span class="h-seperate"></span></li>
          <li class="quicklinks">
            <a href="{url name=admin_tpl_manager_deleteall}" class="btn btn-white">
              <i class="fa fa-trash-o fa-lg"></i> <span class="hidden-xs">{t}Remove all{/t}</span>
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
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="removePermanentlySelected()" tooltip="{t}Remove{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-trash-o fa-lg"></i> <span class="hidden-xs">{t}Remove{/t}</span>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks hidden-xs ng-cloak" ng-init="type = {json_encode($types)|clear_json}">
            <ui-select name="type" theme="select2" ng-model="criteria.type">
              <ui-select-match>
                <strong>{t}Type{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.value as item in type  | filter: $select.search">
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
        </ul>
        <ul class="nav quick-section pull-right ng-cloak" ng-if="contents.length > 0">
          <li class="quicklinks hidden-xs">
            <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div id="caches" class="content">
    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="spinner-wrapper" ng-if="loading">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>

        <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
          <div class="center">
            <h4>{t escape="no"}No cache files were generated for now.{/t}</h4>
            <h6>{t escape="no" 1=$smarty.const.SITE_URL}Visit some pages in <a href="%1" title="Visit your site">your site</a>  and come back here{/t}</h6>
          </div>
        </div>

        <div class="table-wrapper ng-cloak" ng-if="!loading">
          <table class="table table-hover no-margin">
            <thead ng-if="contents.length != 0">
              <tr>
                <th class="checkbox-cell">
                  <div class="checkbox checkbox-default">
                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                    <label for="select-all"></label>
                  </div>
                </th>
                <th class="left">{t}Resource{/t}</th>
                <th class="center hidden-xs" scope=col style="width:100px;">{t}Valid until{/t}</th>
                <th class="center hidden-xs hidden-sm" scope=col style="width:40px;">{t}Size{/t}</th>
              </tr>
            </thead>
            <tbody>
              <tr class="cache-element" ng-repeat="content in contents">
                <td class="checkbox-cell">
                  <div class="checkbox check-default">
                    <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td class="left">
                  {assign var="resource" value=$caches[c].resource}

                  <img ng-src="{$params.IMAGE_DIR}template_manager/elements/[% content.type %].png" ng-if="content.type != 'unknown'" alt="[% content.type_explanation %] cache file">

                  [% content.title %]

                  <div class="listing-inline-actions">
                    <a class="link" href="{$smarty.const.SITE_URL}[% content.url %]" ng-if="content.extra != 'unknown'" target="_blank"><span class="fa fa-external-link"></span> {t}View page{/t}</a>
                    <button class="link link-danger delete-cache-button" ng-click="removePermanently(content)" title="{t}Delete cache file{/t}">
                      <i class="fa fa-trash-o"></i> {t}Remove{/t}
                    </button>
                  </div>
                </td>

                <td class="left hidden-xs">
                  <div class="valid-until-date nowrap" ng-class="[% content.expires < date() ? 'expired' : 'valid' %]">
                    [% content.expires | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}'%]
                  </div>
                </td>
                <td class="center nowrap hidden-xs hidden-sm">[% content.size %] KB</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="grid-footer clearfix ng-cloak" ng-if="contents.length > 0">
        <div class="pull-right">
          <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
        </div>
      </div>
    </div>
  </div>

  <script type="text/ng-template" id="modal-cache-batch-remove">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
      <h4 class="modal-title">
        <i class="fa fa-trash-o"></i>
        {t}Delete cache elements{/t}
      </h4>
    </div>
    <div class="modal-body">
      <p>{t escape=off}Are you sure you want to delete [% template.selected.length %] cache elements?{/t}</p>
    </div>
    <div class="modal-footer">
      <span class="loading" ng-if="deleting == 1"></span>
      <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Yes, remove all{/t}</button>
      <button class="btn secondary" ng-click="close()">{t}No{/t}</button>
    </div>
  </script>

  <script type="text/ng-template" id="modal-cache-remove">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
      <h4 class="modal-title">
        <i class="fa fa-trash-o"></i>
        {t}Delete cache element{/t}
      </h4>
    </div>
    <div class="modal-body">
      <p>{t}Are you sure you want to delete the cache element "[% template.content.title %]".{/t}</p>
    </div>
    <div class="modal-footer">
      <span class="loading" ng-if="deleting == 1"></span>
      <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Yes, remove{/t}</button>
      <button class="btn secondary" ng-click="close()">{t}No{/t}</button>
    </div>
  </script>

</div>
{/block}
