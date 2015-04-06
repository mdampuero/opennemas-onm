{extends file="base/admin.tpl"}

{block name="content"}
<div action="{url name=admin_specials}" method="get" name="formulario" id="formulario" ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init('special', { content_status: -1, category_name: -1, in_home: {if $category == 'widget'}1{else}-1{/if}, title_like: '', in_litter: 0 }, {if $category == 'widget'}'position', 'asc'{else}'created', 'desc'{/if}, 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-star"></i>
              {t}Specials{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks dropdown hidden-xs">
            <div data-toggle="dropdown">
              {if $category == 'widget'}
              {t}Widget Home{/t}
              {else}
              {t}Listing{/t}
              {/if}
              <span class="caret"></span>
            </div>
            <ul class="dropdown-menu">
              <li>
                <a href="{url name=admin_specials_widget}" {if $category =='widget'}class="active"{/if}>
                  {t}Widget Home{/t}
                </a>
              </li>
              <li>
                <a href="{url name=admin_specials}" {if $category !=='widget'}class="active"{/if}>
                  {t}Listing{/t}
                </a>
              </li>
            </ul>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="SPECIAL_SETTINGS"}
            <li class="quicklinks">
              <a class="btn btn-link"  href="{url name=admin_specials_config}" class="admin_add" title="{t}Config special module{/t}">
                <span class="fa fa-cog fa-lg"></span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            {/acl}

            {acl isAllowed="SPECIAL_WIDGET"}
            {if $category eq 'widget'}
            <li class="quicklinks">
              <a class="btn btn-white"  href="#" ng-click="savePositions('backend_ws_contents_save_positions')" title="{t}Save positions{/t}">
                <span class="fa fa-save"></span>
                {t}Save positions{/t}
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            {/if}
            {/acl}
            {acl isAllowed="SPECIAL_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=admin_special_create}">
                <span class="fa fa-plus"></span>
                {t}Create{/t}
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
            <button class="btn btn-link" ng-click="deselectAll()" tooltip="Clear selection" tooltip-placement="right" type="button">
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
          {acl isAllowed="SPECIAL_AVAILABLE"}
          <li class="quicklinks">
            <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" tooltip="{t}Publish{/t}" tooltip-placement="bottom">
              <i class="fa fa-check fa-lg"></i>
            </a>
          </li>
          <li class="quicklinks">
            <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" tooltip="{t}Unpublish{/t}" tooltip-placement="bottom">
              <i class="fa fa-times fa-lg"></i>
            </a>
          </li>
          <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs">
            <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 1, 'home_loading')" tooltip="{t escape="off"}In home{/t}" tooltip-placement="bottom">
              <i class="fa fa-home fa-lg"></i>
            </a>
          </li>
          <li class="quicklinks hidden-xs">
            <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 0, 'home_loading')" tooltip="{t escape="off"}Drop from home{/t}" tooltip-placement="bottom">
              <i class="fa fa-home fa-lg"></i>
              <i class="fa fa-times fa-sub text-danger"></i>
            </a>
          </li>
          {/acl}
          {acl isAllowed="SPECIAL_DELETE"}
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
          <li class="quicklinks hidden-xs ng-cloak"  ng-init="categories = {json_encode($categories)|replace:'"':'\''}">
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
              <input type="number" min="1" max="[% getNumberOfPages() %]" ng-model="pagination.page" class="btn page-selector">
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
    {if $category == 'widget'}
    <div class="messages" ng-if="{$total_elements_widget} > 0 && pagination.total != {$total_elements_widget}">
      <div class="alert alert-info">
        <button class="close" data-dismiss="alert">×</button>
        {t 1=$total_elements_widget}You must put %1 specials in the HOME{/t}<br>
      </div>
    </div>
    {/if}
    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="spinner-wrapper" ng-if="loading">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
          <div class="center">
            <h4>{t}Unable to find any special that matches your search.{/t}</h4>
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
                <th class="title">{t}Title{/t}</th>
                <th style="width:65px;" class="center hidden-xs">{t}Section{/t}</th>
                <th class="center hidden-xs hidden-sm" style="width:100px;">Created</th>
                {acl isAllowed="SPECIAL_FAVORITE"}{if $category!='widget'}<th class="center hidden-xs" style="width:35px;">{t}Favorite{/t}</th>{/if}{/acl}
                {acl isAllowed="SPECIAL_HOME"}<th class="center hidden-xs" style="width:35px;">{t}Home{/t}</th>{/acl}
                {acl isAllowed="SPECIAL_AVAILABLE"}<th class="center" style="width:35px;">{t}Published{/t}</th>{/acl}
              </tr>
            </thead>
            <tbody {if $category == 'widget'}ui-sortable ng-model="contents"{/if}>
              <tr ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                <td class="checkbox-cell">
                  <div class="checkbox check-default">
                    <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td>
                  [% content.title %]
                  <div class="visible-sm visible-xs small-text">
                    <strong>{t}Created{/t}:</strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                  </div>
                  <div class="listing-inline-actions">
                    {acl isAllowed="SPECIAL_UPDATE"}
                    <a class="link" href="[% edit(content.id, 'admin_special_show') %]">
                      <i class="fa fa-pencil"></i> {t}Edit{/t}
                    </a>
                    {/acl}
                    {acl isAllowed="SPECIAL_DELETE"}
                    <button class="del link link-danger" ng-click="sendToTrash(content)" type="button">
                      <i class="fa fa-trash-o"></i> {t}Remove{/t}
                    </button>
                    {/acl}
                  </div>
                </td>
                <td class="center hidden-xs">
                  [% content.category_name %]
                </td>
                <td class="center nowrap hidden-xs hidden-sm">
                  [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                </td>
                {if $category!='widget'}
                {acl isAllowed="SPECIAL_FAVORITE"}
                <td class="center hidden-xs">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_favorite', 'favorite', content.favorite != 1 ? 1 : 0, 'favorite_loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.favorite_loading == 1, 'fa-star text-warning': !content.favorite_loading && content.favorite == 1, 'fa-star-o': !content.favorite_loading && content.favorite == 0 }"></i>
                  </button>
                </td>
                {/acl}
                {/if}
                {acl isAllowed="SPECIAL_HOME"}
                <td class="right hidden-xs">
                  <button class="btn btn-white" ng-if="content.author.meta.is_blog != 1" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                    <i class="fa fa-home" ng-class="{ 'fa-circle-o-notch fa-spin': content.home_loading == 1, 'text-info': content.in_home == 1 }"></i>
                    <i class="fa fa-times fa-sub" ng-if="content.in_home == 0"></i>
                  </button>
                </td>
                {/acl}
                {acl isAllowed="SPECIAL_AVAILABLE"}
                <td class="center">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading && content.content_status == 1, 'fa-times text-danger': !content.loading && content.content_status == 0 }"></i>
                  </button>
                </td>
                {/acl}
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="grid-footer clearfix ng-cloak" ng-if="!loading && contents.length > 0">
        <div class="pagination-info pull-left">
          {t}Showing{/t} [% ((pagination.page - 1) > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% pagination.page * pagination.epp %] {t}of{/t} [% pagination.total %]
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
  <script type="text/ng-template" id="modal-update-selected">
    {include file="common/modals/_modalBatchUpdate.tpl"}
  </script>
</div>
{/block}
