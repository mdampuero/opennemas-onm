{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="EventListCtrl" ng-init="init()">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-calendar m-r-10"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="{url name=backend_events}" title="{t}Go back to list{/t}">
                {t}Events{/t}
              </a>
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="EVENT_CREATE"}
            <li class="m-l-10">
              <a class="btn btn-success text-uppercase" href="{url name=backend_event_create}" title="{t}New cover{/t}" id="create-button">
                <i class="fa fa-plus m-r-5"></i>{t}Create{/t}
              </a>
            </li>
            {/acl}
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="page-navbar selected-navbar collapsed" class="hidden"  ng-class="{ 'collapsed': selected.items.length == 0 }">
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
          {acl isAllowed="EVENT_AVAILABLE"}
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
          {acl isAllowed="EVENT_DELETE"}
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" href="#" ng-click="patchSelected('in_litter', 1)">
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
          <li class="quicklinks hidden-xs ng-cloak">
            <onm-category-selector ng-model="criteria.pk_fk_content_category" categories="categories" label-text="{t}Category{/t}" default-value-text="{t}Any{/t}" placeholder="{t}Select a category{/t}" required />
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
        <ul class="nav quick-section pull-right ng-cloak" ng-if="items.length > 0">
          <li class="quicklinks hidden-xs">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
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
                <th class="center hidden-xs hidden-sm" style="width:10px"></th>
                <th>{t}Title{/t}</th>
                {if $category=='widget' || $category == 'all'}
                <th class="center hidden-xs" width="200">{t}Section{/t}</th>
                {/if}
                <th class="text-center" width="100">{t}Published{/t}</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
                <td class="checkbox-cell">
                  <div class="checkbox check-default" ng-if="isSelectable(item)">
                    <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td class="center hidden-xs hidden-sm">
                  <img ng-src="{$EVENT_IMG_URL}[% item.path%][% item.thumb_url %]"
                  title="{$cover->title|clearslash}" alt="{$cover->title|clearslash}" style="max-width:80px" class="thumbnail" />
                </td>
                <td>
                  <img ng-src="{$EVENT_IMG_URL}[% item.path%][% item.thumb_url %]"
                  title="{$cover->title|clearslash}" alt="{$cover->title|clearslash}" style="max-width:80px" class="thumbnail visible-xs visible-sm" />
                  <span uib-tooltip="{t}Last editor{/t} [% shvs.extra.authors[item.fk_user_last_editor].name %]">[% item.title%]</span>
                  <div>
                    <small>
                      <strong>{t}From:{/t}</strong> [% item.starttime %] <br>
                      <strong>{t}To:{/t}</strong> [% item.endtime %]
                    </small>
                  </div>
                  <div class="listing-inline-actions">
                    {acl isAllowed="EVENT_UPDATE"}
                      <a class="btn btn-small" href="[% routing.generate('backend_event_show', { id: item.id }) %]">
                        <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                      </a>
                    {/acl}
                    {acl isAllowed="EVENT_DELETE"}
                      <button class="btn btn-danger btn-small" ng-click="delete(item.pk_EVENT)" type="button">
                        <i class="fa fa-trash-o m-r-5"></i>{t}Remove{/t}
                      </button>
                    {/acl}
                  </div>
                </td>
                {if $category == 'widget' || $category == 'all'}
                  <td class="hidden-xs text-center">
                    [% item.category_name %]
                  </td>
                {/if}
                <td class="hidden-xs text-center">
                  [% item.date %]
                </td>
                {acl isAllowed="EVENT_AVAILABLE"}
                  <td class="text-center">
                    <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading, 'fa-check text-success' : !item.content_statusLoading && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading && item.content_status == 0 }"></i>
                    </button>
                  </td>
                {/acl}
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
    </div>
    <script type="text/ng-template" id="modal-delete">
      {include file="base/modal/modal.delete.tpl"}
    </script>
{*     <script type="text/ng-template" id="modal-delete">
      {include file="common/modals/_modalDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
      {include file="common/modals/_modalBatchDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-update-selected">
      {include file="common/modals/_modalBatchUpdate.tpl"}
    </script> *}
  </div>

</div>
{/block}
