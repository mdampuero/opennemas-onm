{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init('article', { content_status: -1, category_name: -1, title_like: '', in_litter: 0, fk_author: -1 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-file-text-o"></i>
              {t}Articles{/t}
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              {acl isAllowed="ARTICLE_CREATE"}
              <a class="btn btn-primary" href="{url name=admin_article_create category=$category}">
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
          {acl isAllowed="ARTICLE_AVAILABLE"}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-times fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-check fa-lg"></i>
            </button>
          </li>
          {/acl}
          {acl isAllowed="ARTICLE_DELETE"}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="sendToTrashSelected()" tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
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
          <li class="quicklinks dropdown hidden-xs">
            <span class="btn btn-none dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
              <span class="dropdown-current">
                <strong>{t}Category{/t}:</strong>
                <span ng-if="criteria.category_name == -1">{t}All{/t}</span>
                <span ng-if="criteria.category_name != -1">[% criteria.category_name %]</span>
              </span>
              <span class="caret"></span>
            </span>
            <ul class="dropdown-menu">
              <li ng-click="criteria.category_name = -1">
                <span class="a">{t}All{/t}</span>
              </li>
              {section name=as loop=$allcategorys}
              {assign var=ca value=$allcategorys[as]->pk_content_category}
              <li ng-click="criteria.category_name = '{$allcategorys[as]->name}'">
                <span class="a">
                  {$allcategorys[as]->title}
                  {if $allcategorys[as]->inmenu eq 0}
                  {t}(inactive){/t}
                  {/if}
                </span>
              </li>
              {section name=su loop=$subcat[as]}
              {assign var=subca value=$subcat[as][su]->pk_content_category}
              {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
              {assign var=subca value=$subcat[as][su]->pk_content_category}
              <li ng-click="criteria.category_name = '{$subcat[as][su]->name}'">
                <span class="a">
                  &rarr;
                  {$subcat[as][su]->title}
                  {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}
                  {t}(inactive){/t}
                  {/if}
                </span>
              </li>
              {/acl}
              {/section}
              {/section}
            </ul>
          </li>
          <li class="quicklinks dropdown hidden-xs">
            <button class="btn btn-none dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
              <span class="dropdown-current">
                {t}Status{/t}:
                <span ng-if="criteria.content_status == null || criteria.content_status == -1">{t}All{/t}</span>
                <span ng-if="criteria.content_status == 0">{t}No published{/t}</span>
                <span ng-if="criteria.content_status == 1">{t}Published{/t}</span>
              </span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <li ng-click="criteria.content_status = -1">
                <span class="a">{t}All{/t}</span>
              </li>
              <li ng-click="criteria.content_status = 1">
                <span class="a">{t}Published{/t}</span>
              </li>
              <li ng-click="criteria.content_status = 0">
                <span class="a">{t}No Published{/t}</span>
              </li>
            </ul>
          </li>
          <li class="quicklinks dropdown hidden-xs">
            <button class="btn btn-none dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
              <span class="dropdown-current">
                {t}Author{/t}: [% extra.authors[content.fk_author].name %]
                <span ng-if="criteria.fk_author == -1">{t}All{/t}</span>
                <span ng-if="criteria.fk_author != -1">[% criteria.category_name %]</span>
              </span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <li ng-click="criteria.fk_author = -1">
                <span class="a">{t}All{/t}</span>
              </li>
              {foreach $authors as $author}
              <li ng-click="criteria.fk_author = {$author->id}">
                <span class="a">{$author->name}</span>
              </li>
              {/foreach}
            </ul>
          </li>
          <li class="quicklinks hidden-sm hidden-xs">
            <select name="status" ng-model="pagination.epp" data-label="{t}View:{/t}" class="select2">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
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
        <div class="table-wrapper ng-cloak">
          <table class="table table-hover no-margin" ng-if="!loading">
            <thead>
              <th class="checkbox-cell">
                <div class="checkbox checkbox-default">
                  <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                  <label for="select-all"></label>
                </div>
              </th>
              <th class="left" >{t}Title{/t}</th>
              {if $category eq 'all' || $category == 0}
              <th class="left hidden-xs">{t}Section{/t}</th>
              {/if}
              <th class="center hidden-xs" style="width:130px;">{t}Created{/t}</th>
              <th class="center hidden-xs" style="width:10px;">{t}Published{/t}</th>
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
                  <span tooltip="{t}Last editor{/t} [% extra.authors[content.fk_user_last_editor].name %]">[% content.title %]</span>
                  <div>
                    <small ng-if="content.fk_author != 0 || content.agency != ''">
                      <strong>{t}Author{/t}:</strong>
                      <span ng-if="content.fk_author != 0">
                        [% extra.authors[content.fk_author].name %]
                      </span>
                      <span ng-if="content.fk_author == 0 && content.agency != ''">
                        [% content.agency %]
                      </span>
                    </small>
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
                {if $category eq 'all' || $category == 0}
                <td class="left hidden-xs">
                  <span ng-if="content.category_name == 'unknown'">
                    {t}Unasigned{/t}
                  </span>
                  <span ng-if="content.category_name != 'unknown'">
                    [% content.category_name %]
                  </span>
                </td>
                {/if}
                <td class="center nowrap hidden-xs">
                  [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                </td>
                <td class="right hidden-xs">
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
        <div class="grid-footer clearfix ng-cloak" ng-if="!loading">
          <div class="pagination-info pull-left" ng-if="contents.length > 0">
            {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
          </div>
          <div class="pull-right pagination-wrapper" ng-if="contents.length > 0">
            <pagination class="no-margin" max-size="5" direction-links="true" ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total" num-pages="pagination.pages"></pagination>
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
</div>
{/block}
