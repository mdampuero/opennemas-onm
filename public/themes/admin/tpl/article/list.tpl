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
            <select id="category" ng-model="criteria.category_name" data-label="{t}Category{/t}" class="select2">
              <option value="-1">{t}-- All --{/t}</option>
              {section name=as loop=$allcategorys}
              {assign var=ca value=$allcategorys[as]->pk_content_category}
              <option value="{$allcategorys[as]->name}">
                {$allcategorys[as]->title}
                {if $allcategorys[as]->inmenu eq 0}
                <span class="inactive">{t}(inactive){/t}</span>
                {/if}
              </option>
              {section name=su loop=$subcat[as]}
              {assign var=subca value=$subcat[as][su]->pk_content_category}
              {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
              {assign var=subca value=$subcat[as][su]->pk_content_category}
              <option value="{$subcat[as][su]->name}">
                &rarr;
                {$subcat[as][su]->title}
                {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}
                <span class="inactive">{t}(inactive){/t}</span>
                {/if}
              </option>
              {/acl}
              {/section}
              {/section}
            </select>
          </li>
          <li class="quicklinks hidden-xs">
            <select class="select2" ng-model="criteria.content_status" data-label="{t}Status{/t}">
              <option value="-1">{t}-- All --{/t}</option>
              <option value="1">{t}Published{/t}</option>
              <option value="0">{t}No published{/t}</option>
            </select>
          </li>
          <li class="quicklinks hidden-xs hidden-sm">
            <select class="select2" ng-model="criteria.author" data-label="{t}Author{/t}">
              <option value="-1">{t}-- All --{/t}</option>
              <option value="-2">{t}Director{/t}</option>
              <option value="-3">{t}Editorial{/t}</option>
              {section name=as loop=$autores}
              <option value="{$autores[as]->id}" {if isset($author) && $author == $autores[as]->id} selected {/if}>{$autores[as]->name} {if $autores[as]->meta['is_blog'] eq 1} (Blogger) {/if}</option>
              {/section}
            </select>
          </li>
          <li class="quicklinks hidden-sm hidden-xs">
            <select name="status" ng-model="pagination.epp" data-label="{t}View{/t}" class="select2">
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
