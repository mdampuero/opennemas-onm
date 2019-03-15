{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="StaticPageListCtrl" ng-init="forcedLocale = '{$locale}'; init()">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-file-o m-r-10"></i>
              <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/238735-opennemas-p%C3%A1ginas-est%C3%A1ticas" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question"></i>
              </a>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>
              {t}Static Pages{/t}
            </h4>
          </li>
          <li class="quicklinks m-l-5 m-r-5 ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
            <translator keys="data.extra.keys" ng-model="config.locale.selected" options="data.extra.locale"></translator>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="STATIC_PAGE_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-success text-uppercase" href="{url name=backend_static_page_create}" title="{t}Create new page{/t}">
                <span class="fa fa-plus m-r-5"></span>{t}Create{/t}
              </a>
            </li>
            {/acl}
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="page-navbar selected-navbar collapsed" class="hidden" ng-class="{ 'collapsed': selected.items.length == 0 }">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section pull-left">
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right"type="button">
              <i class="fa fa-arrow-left fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <h4>
              [% selected.items.length %] <span class="hidden-xs">{t}items selected{/t}</span>
            </h4>
          </li>
        </ul>
        <ul class="nav quick-section pull-right">
          {acl isAllowed="STATIC_PAGE_AVAILABLE"}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="patchSelected('content_status', 0)" uib-tooltip="{t}Unpublish{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-times fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="patchSelected('content_status', 1)" uib-tooltip="{t}Publish{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-check fa-lg"></i>
            </button>
          </li>
          {/acl}
          {acl isAllowed="STATIC_PAGE_DELETE"}
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" href="#" ng-click="sendToTrash()">
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
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="no-boarder" name="title" ng-model="criteria.title" placeholder="{t}Search by title{/t}" type="text"/>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs ng-cloak" ng-init="status = [ { name: '{t}All{/t}', value: null }, { name: '{t}Published{/t}', value: 1 }, { name: '{t}No published{/t}', value: 0 } ]">
            <ui-select name="status" theme="select2" ng-model="criteria.content_status">
              <ui-select-match>
                <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.value as item in status | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="quicklinks hidden-xs ng-cloak">
            <ui-select name="view" theme="select2" ng-model="criteria.epp">
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
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="listing-no-contents" ng-hide="!flags.http.loading">
      <div class="text-center p-b-15 p-t-15">
        <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
        <h3 class="spinner-text">{t}Loading{/t}...</h3>
      </div>
    </div>
    <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && items.length == 0">
      <div class="text-center p-b-15 p-t-15">
        <i class="fa fa-4x fa-warning text-warning"></i>
        <h3>{t}Unable to find any item that matches your search.{/t}</h3>
        <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
      </div>
    </div>
    <div class="grid simple ng-cloak" ng-if="!flags.http.loading && items.length > 0">
      <div class="grid-body no-padding">
        <div class="table-wrapper ng-cloak">
          <table class="table table-hover no-margin">
            <thead>
              <tr>
                <th class="checkbox-cell">
                  <div class="checkbox checkbox-default">
                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                    <label for="select-all"></label>
                  </div>
                </th>
                <th>{t}Title{/t}</th>
                <th class="hidden-sm hidden-xs">{t}URL{/t}</th>
                <th class="text-center" width="100">{t}Published{/t}</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(getId(item)) }">
                <td class="checkbox-cell">
                  <div class="checkbox check-default" ng-if="isSelectable(item)">
                    <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="getId(item)" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td>
                  [% item.title %]
                  <span class="hidden-md hidden-lg">
                 </a>
               </span>
               <div class="listing-inline-actions">
                {acl isAllowed="STATIC_PAGE_UPDATE"}
                <a class="btn btn-default btn-small" href="[% routing.generate('backend_static_page_show', { id: getId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
                  <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                </a>
                <translator item="data.items[$index]" keys="data.extra.keys" link="[% routing.generate('backend_static_page_show', { id: getId(item) }) %]" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
                {/acl}
                {acl isAllowed="STATIC_PAGE_DELETE"}
                  <button class="btn btn-danger btn-small" ng-click="sendToTrash(item)" type="button">
                    <i class="fa m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': item.in_litterLoading, 'fa-trash-o': !item.in_litterLoading }"></i>{t}Delete{/t}
                  </button>
                {/acl}
                <a class="btn btn-small btn-white hidden-md hidden-lg" href="{$smarty.const.INSTANCE_MAIN_DOMAIN}/{$smarty.const.STATIC_PAGE_PATH}/[% item.slug %]/" target="_blank">
                  <span class="fa fa-external-link"></span>
                </a>
              </div>
            </td>
            <td class="hidden-sm hidden-xs">
              <a href="[% routing.generate('frontend_static_page', { slug: item.slug }, true) %]" target="_blank" title="{t}Open in a new window{/t}">
                [% routing.generate('frontend_static_page', { slug: item.slug }, true) %]
              </a>
            </td>
            <td class="text-center">
              {acl isAllowed="STATIC_PAGE_AVAILABLE"}
              <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
                <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading, 'fa-check text-success' : !item.content_statusLoading && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading && item.content_status == 0 }"></i>
              </button>
              {/acl}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="grid-footer clearfix ng-cloak" ng-if="!loading && items.length > 0">
    <div class="pull-right">
      <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
    </div>
  </div>

  <script type="text/ng-template" id="modal-delete">
    {include file="common/modals/modal.trash.tpl"}
  </script>
</div>
{/block}
