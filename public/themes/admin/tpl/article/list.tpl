{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init('article', { content_status: -1, category_name: -1, title_like: '', in_litter: 0, fk_author: -1 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-file-text-o page-navbar-icon"></i>
              <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/220778-primeros-pasos-en-opennemas-c%C3%B3mo-crear-un-art%C3%ADcu" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question"></i>
              </a>
              {t}Articles{/t}
            </h4>
          </li>
          <li class="quicklinks visible-xs">
            <a class="help-icon" href="http://help.opennemas.com/knowledgebase/articles/220778-primeros-pasos-en-opennemas-c%C3%B3mo-crear-un-art%C3%ADcu" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
              <i class="fa fa-question fa-lg"></i>
            </a>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              {acl isAllowed="ARTICLE_CREATE"}
              <a class="btn btn-primary" href="{url name=admin_article_create}" id="create-button">
                <i class="fa fa-plus"></i>
                {t}Create{/t}
              </a>
              {/acl}
            </li>
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
            <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right"type="button">
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
          {acl isAllowed="ARTICLE_AVAILABLE"}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-times fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-check fa-lg"></i>
            </button>
          </li>
          {/acl}
          {acl isAllowed="ARTICLE_DELETE"}
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="sendToTrashSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
        <ul class="nav quick-section filter-components">
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="no-boarder" name="title" ng-model="criteria.title_like" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by title{/t}" type="text"/>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks dropdown hidden-xs ng-cloak" ng-init="categories = {json_encode($categories)|clear_json}">
            <ui-select name="author" theme="select2" ng-model="criteria.category_name">
              <ui-select-match>
                <strong>{t}Category{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.value as item in categories | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="quicklinks hidden-xs ng-cloak" ng-init="status = [ { name: '{t}All{/t}', value: -1 }, { name: '{t}Published{/t}', value: 1 }, { name: '{t}No published{/t}', value: 0 } ]">
            <ui-select name="status" theme="select2" ng-model="criteria.content_status">
              <ui-select-match>
                <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.value as item in status | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="quicklinks hidden-xs hidden-sm ng-cloak" ng-init="authors = {json_encode($authors)|clear_json}">
            <ui-select name="author" theme="select2" ng-model="criteria.fk_author">
              <ui-select-match>
                <strong>{t}Author{/t}:</strong> [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.value as item in authors | filter: { name: $select.search }">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="quicklinks hidden-sm hidden-xs ng-cloak">
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
          <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
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
            <h4>{t}Unable to find any article that matches your search.{/t}</h4>
            <h6>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h6>
          </div>
        </div>
        <div class="table-wrapper ng-cloak" ng-if="!loading && contents.length > 0">
          <table class="table table-hover no-margin">
            <thead>
              <th class="checkbox-cell">
                <div class="checkbox checkbox-default">
                  <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                  <label for="select-all"></label>
                </div>
              </th>
              <th class="left" >{t}Title{/t}</th>
              <th class="left hidden-xs">{t}Author{/t}</th>
              {if $category eq 'all' || $category == 0}
                <th class="left hidden-xs">{t}Section{/t}</th>
              {/if}
              <th class="center" style="width:10px;">{t}Published{/t}</th>
            </thead>
            <tbody>
              <tr ng-if="contents.length == 0">
                <td class="empty" colspan="10">{t}No available articles.{/t}</td>
              </tr>
              <tr ng-if="contents.length >= 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                <td class="checkbox-cell">
                  <div class="checkbox check-default">
                    <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td class="left">
                  <span uib-tooltip="{t}Last editor{/t} [% extra.authors[content.fk_user_last_editor].name %]">[% content.title %]</span>
                  <div class="small-text">
                    <strong>{t}Created{/t}: </strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                  </div>
                  <div class="small-text">
                    <span ng-if="content.starttime && content.starttime != '0000-00-00 00:00:00'">
                      <strong>{t}Available from{/t} </strong>
                      [% content.starttime | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                    </span>
                    <span ng-if="content.endtime && content.endtime != '0000-00-00 00:00:00'">
                      <strong>{t}to{/t} </strong> [% content.endtime | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                    </span>
                  </div>
                  <div class="listing-inline-actions">
                    {acl isAllowed="ARTICLE_UPDATE"}
                    <a class="link" href="[% edit(content.id, 'admin_article_show') %]">
                      <i class="fa fa-pencil"></i>
                      {t}Edit{/t}
                    </a>
                    {/acl}
                    {acl isAllowed="ARTICLE_DELETE"}
                    <button class="link link-danger" ng-click="sendToTrash(content)" type="button">
                      <i class="fa fa-trash-o"></i>
                      {t}Delete{/t}
                    </button>
                    {/acl}
                  </div>
                </td>
                <td class="hidden-xs">
                  <span ng-if="content.fk_author != 0">
                    [% extra.authors[content.fk_author].name %]
                  </span>
                  <span ng-if="content.fk_author == 0 && content.agency != ''">
                    [% content.agency %]
                  </span>
                </td>
                {if $category eq 'all' || $category == 0}
                  <td class="left hidden-xs">
                    <span ng-if="content.category_name == 'unknown'">
                      {t}Unasigned{/t}
                    </span>
                    <span ng-if="content.category_name != 'unknown'">
                      [% extra.categories[content.category_name] %]
                    </span>
                  </td>
                {/if}
                <td class="right">
                  <span ng-if="content.category != 20">
                    {acl isAllowed="ARTICLE_AVAILABLE"}
                    <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading, 'fa-check text-success' : !content.loading && content.content_status == '1', 'fa-times text-error': !content.loading && content.content_status == '0' }"></i>
                    </button>
                    {/acl}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="grid-footer clearfix ng-cloak" ng-if="!loading && contents.length > 0">
          <div class="pull-right">
            <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
          </div>
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
  <script type="text/ng-template" id="modal-update-selected">
    {include file="common/modals/_modalBatchUpdate.tpl"}
  </script>
</div>
{/block}
