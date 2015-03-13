{extends file="base/admin.tpl"}

{block name="content"}
<div action="{url name=admin_letters}" method="GET" name="formulario" id="formulario"  ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init('letter', { content_status: -1, in_home: {if $category == 'widget'}1{else}-1{/if}, title_like: '', in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-envelope"></i>
              <span class="hidden-xs">{t}Letters to the Editor{/t}</span> <span class="visible-xs-inline">{t}Letters{/t}</span>
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="LETTER_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=admin_letter_create}" class="admin_add" accesskey="N" tabindex="1">
                <span class="fa fa-plus"></span> {t}Create{/t}
              </a>
            </li>
            {/acl}
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="page-navbar selected-navbar collapsed" class="hidden" ng-class="{ 'collapsed': selected.contents.length == 0 }">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section pull-left">
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deselectAll()" tooltip="Clear selection" tooltip-placement="right"type="button">
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
          {acl isAllowed="LETTER_AVAILABLE"}
          <li class="quicklinks">
            <a class="btn btn-link" href="#" id="batch-publish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" tooltip="{t}Publish{/t}" tooltip-placement="bottom">
              <i class="fa fa-check fa-lg"></i>
            </a>
          </li>
          <li class="quicklinks">
            <a class="btn btn-link" href="#" id="batch-unpublish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" tooltip="{t}Unpublish{/t}" tooltip-placement="bottom">
              <i class="fa fa-times fa-lg"></i>
            </a>
          </li>
          {/acl}
          {acl isAllowed="LETTER_DELETE"}
          <li class="quicklinks"><span class="h-seperate"></span></li>
          <li class="quicklinks">
            <a class="btn btn-link" href="#" id="batch-delete" ng-click="sendToTrashSelected()" tooltip="{t}Delete{/t}" tooltip-placement="bottom">
              <i class="fa fa-trash-o fa-lg"></i>
            </a>
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
            <input class="no-boarder" name="title" ng-model="criteria.title_like" placeholder="{t}Search by title{/t}" type="text"/>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks dropdown hidden-xs">
            <select id="content_status" ng-model="criteria.content_status" data-label="{t}Status{/t}">
              <option value="-1">-- All --</option>
              <option value="1">{t}Published{/t}</option>
              <option value="0">{t}No published{/t}</option>
              <option value="2">{t}Rejected{/t}</option>
            </select>
          </li>
          <li class="quicklinks hidden-xs">
            <select class="select2 input-medium" name="status" ng-model="criteria.elements_per_page" data-label="{t}View{/t}">
              <option value="10a">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </li>
        </ul>
        <ul class="nav quick-section pull-right simple-pagination ng-cloak" ng-if="contents.length > 0">
          <li class="quicklinks hidden-xs">
            <span class="info">
              [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
            </span>
          </li>
          <li class="quicklinks form-inline pagination-links">
            <div class="btn-group">
              <button class="btn btn-white" ng-click="goToPrevPage()" ng-disabled="isFirstPage()" type="button">
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
        <div class="spinner-wrapper" ng-if="loading">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
          <div class="center">
            <h4>{t}Unable to find any letter that matches your search.{/t}</h4>
            <h6>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h6>
          </div>
        </div>
        <div class="table-wrapper ng-cloak" ng-if="!loading && contents.length > 0">
          <table class="table table-hover no-margin">
            <thead>
              <tr>
                <th class="checkbox-cell">
                  <div class="checkbox checkbox-default">
                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                    <label for="select-all"></label>
                  </div>
                </th>
                <th style='width:10px;' class="hidden-xs">{t}Image{/t}</th>
                <th>{t}Author{/t} - {t}Title{/t}</th>
                <th style='width:110px;' class="left hidden-xs">{t}Date{/t}</th>
                {acl isAllowed="LETTER_AVAILABLE"}
                <th class="center" style='width:10px;'>{t}Available{/t}</th>
                {/acl}
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                <td class="checkbox-cell">
                  <div class="checkbox check-default">
                    <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td class="center hidden-xs">
                  <span class="thumbnail">
                    <i class="fa fa-picture-o fa-lg" ng-if="!content.image" title="{t}Media element (jpg, png, gif){/t}"></i>
                    <img ng-if="content.image" ng-src="content.image">
                  </span>
                </td>
                <td>
                  <span class="thumbnail center visible-xs">
                    <i class="fa fa-picture-o fa-lg" ng-if="!content.image" title="{t}Media element (jpg, png, gif){/t}"></i>
                    <img ng-if="content.image" ng-src="content.image">
                  </span>
                  <div>
                    <small>"[% content.author %]" ([% content.email %])</small>
                  </div>
                  <span tooltip="[% content.body | striptags | limitTo: 140 %]...">[% content.title %]</span>
                  <div class="listing-inline-actions">
                    {acl isAllowed="LETTER_UPDATE"}
                    <a class="link" href="[% edit(content.id, 'admin_letter_show') %]">
                      <i class="fa fa-pencil"></i>
                      {t}Edit{/t}
                    </a>
                    {/acl}

                    {acl isAllowed="LETTER_AVAILABLE"}
                    <a class="link pointer" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', 2, 'loading')" ng-if="content.content_status != 2" type="button" title="{t}Reject{/t}">
                      <i class="fa fa-ban"></i>
                      {t}Reject{/t}
                    </a>
                    {/acl}

                    {acl isAllowed="LETTER_DELETE"}
                    <button class="del link link-danger" ng-click="sendToTrash(content)" type="button">
                      <i class="fa fa-trash-o"></i>
                      {t}Delete{/t}
                    </button>
                    {/acl}
                  </div>
                </td>
                <td class="center nowrap hidden-xs">
                  [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                </td>
                <td class="right">
                  {acl isAllowed="LETTER_AVAILABLE"}
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" title="{t}Publish/Unpublish{/t}" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading && content.content_status == 1, 'fa-times text-danger': !content.loading && content.content_status != 1 }"></i>
                  </button>
                  {/acl}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="grid-footer clearfix ng-cloak" ng-if="!loading && contents.length > 0">
        <div class="pagination-info pull-left">
          {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
        </div>
        <div class="pull-right pagination-wrapper">
          <pagination class="no-margin" max-size="5" direction-links="true" ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total" num-pages="pagination.pages"></pagination>
        </div>
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
