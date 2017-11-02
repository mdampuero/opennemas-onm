{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="KeywordListCtrl" ng-init=" criteria = { epp: 10, orderBy: { pclave:  'asc' }, page: 1 }; init(null, 'backend_ws_keywords_list');">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-tags"></i>
              {t}Keywords{/t}
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="PCLAVE_CREATE"}
            <li>
              <a href="{url name=admin_keyword_create}" class="btn btn-primary" id="create-button">
                <i class="fa fa-plus"></i>
                {t}Create{/t}
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
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deleteSelectedKeywords()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-trash-o fa-lg"></i>
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
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="no-boarder" type="text" name="title" ng-model="criteria.pclave" ng-keyup="searchByKeypress($event)" placeholder="{t}Filter by name{/t}" />
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
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
    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="spinner-wrapper" ng-if="loading">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
          <div class="center">
            <h4>{t}Unable to find any keyword that matches your search.{/t}</h4>
            <h6>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h6>
          </div>
        </div>
        <div class="table-wrapper ng-cloak" ng-if="!loading && contents.length > 0">
          <table class="table table-hover no-margin">
            <thead>
              <tr>
                <th style="width:15px;">
                  <div class="checkbox checkbox-default">
                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                    <label for="select-all"></label>
                  </div>
                </th>
                <th>{t}Keyword{/t}</th>
                <th class="hidden-xs"></th>
                <th class="hidden-xs"></th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                <td>
                  <div class="checkbox check-default">
                    <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td>
                  [% content.pclave %]
                  <p class="visible-xs">
                    <span ng-if="content.tipo == 'url'">
                      <span class="fa fa-external-link"></span> {t}External link to {/t}
                    </span>
                    <span ng-if="content.tipo == 'intsearch'" >
                      <span class="fa fa-link"></span> {t}Internal search to keyword{/t}
                    </span>
                    <span ng-if="content.tipo == 'email'">
                      <span class="fa fa-envelope"></span> {t}Link to send email to{/t}
                    </span>
                    [% content.value %]
                  </p>

                  <div class="listing-inline-actions">
                    <a class="link" href="[% edit(content.id, 'admin_keyword_show') %]" title="{t}Edit this content{/t}">
                      <i class="fa fa-pencil"></i>
                      {t}Edit{/t}
                    </a>
                    <button class="link link-danger" ng-click="deleteKeyword(content)" type="button">
                      <i class="fa fa-trash-o"></i>
                      {t}Delete{/t}
                    </button>
                  </div>
                </td>
                <td class="hidden-xs">
                  <span ng-if="content.tipo == 'url'">
                    <span class="fa fa-external-link"></span> {t}External link to {/t}
                  </span>
                  <span ng-if="content.tipo == 'intsearch'" >
                    <span class="fa fa-link"></span> {t}Internal search to keyword{/t}
                  </span>
                  <span ng-if="content.tipo == 'email'">
                    <span class="fa fa-envelope"></span> {t}Link to send email to{/t}
                  </span>
                </td>
                <td class="hidden-xs">
                  [% content.value %]
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="grid-footer clearfix ng-cloak" ng-if="!loading && contents.length > 0">
        <div class="pull-right pagination-wrapper">
          <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
        </div>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="modal-remove-permanently">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
      <h4 class="modal-title">
        <i class="fa fa-trash-o"></i>
        {t}Permanently remove item{/t}
      </h4>
    </div>
    <div class="modal-body">
      <p>{t escape=off}Are you sure that do you want remove "[% template.content.title %]"?{/t}</p>
      <p class="alert alert-error">{t} You will not be able to restore it back.{/t}</p>
    </div>
    <div class="modal-footer">
      <span class="loading" ng-if="deleting == 1"></span>
      <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Yes, remove{/t}</button>
      <button class="btn secondary" ng-click="close()">{t}No{/t}</button>
    </div>
  </script>
  <script type="text/ng-template" id="modal-batch-remove-permanently">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
      <h4 class="modal-title">
        <i class="fa fa-trash-o"></i>
        {t}Remove permanently selected items{/t}
      </h4>
    </div>
    <div class="modal-body">
      <p>{t escape=off}Are you sure you want to remove permanently [% template.selected.contents.length %] item(s)?{/t}</p>
      <p class="alert alert-error">{t} You will not be able to restore them back.{/t}</p>
    </div>
    <div class="modal-footer">
      <span class="loading" ng-if="deleting == 1"></span>
      <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Yes, remove them all{/t}</button>
      <button class="btn secondary" ng-click="close()" type="button">{t}No{/t}</button>
    </div>
  </script>
</div>
{/block}
