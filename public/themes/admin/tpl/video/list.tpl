{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init('video', { content_status: -1, title_like: '', category_name: -1, in_litter: 0{if $category == 'widget'},in_home: 1{/if} }, {if $category == 'widget'}'position'{else}'created'{/if}, {if $category == 'widget'}'asc'{else}'desc'{/if}, 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-film"></i>
              {t}Videos{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks dropdown hidden-xs">
            <div data-toggle="dropdown">
              {if $category == 'widget'}
              {t}Widget home{/t}
              {elseif $category == 'all'}
              {t}Listing{/t}
              {else}
              {$datos_cat[0]->title}
              {/if}
              <span class="caret"></span>
            </div>
            <ul class="dropdown-menu">
              <li>
                <a href="{url name=admin_videos_widget}" {if $category=='widget'}class="active"{/if}>
                  {t}Widget home{/t}
                </a>
              </li>
              <li>
                <a href="{url name=admin_videos}" {if $category != 'widget'}class="active"{/if}>
                  {t}Listing{/t}
                </a>
              </li>
            </ul>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="VIDEO_SETTINGS"}
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_videos_config}" class="admin_add" title="{t}Config video module{/t}">
                <span class="fa fa-cog fa-lg"></span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            {/acl}
            {acl isAllowed="VIDEO_WIDGET"}
            {if $category eq 'widget'}
            <li class="quicklinks">
              <a class="btn btn-white" href="#" ng-click="savePositions('backend_ws_contents_save_positions')" title="{t}Save positions{/t}">
                {t}Save positions{/t}
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            {/if}
            {/acl}
            {acl isAllowed="VIDEO_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=admin_videos_create category=$category}" accesskey="N" tabindex="1">
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
          {acl isAllowed="VIDEO_AVAILABLE"}
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
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs">
            <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 1, 'home_loading')" tooltip="{t escape="off"}In home{/t}" tooltip="{t escape="off"}In home{/t}" tooltip-placement="bottom">
              <i class="fa fa-home"></i>
            </a>
          </li>
          <li class="quicklinks hidden-xs">
            <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 0, 'home_loading')" tooltip="{t escape="off"}Drop from home{/t}" tooltip="{t escape="off"}Drop from home{/t}" tooltip-placement="bottom">
              <i class="fa fa-home"></i>
              <i class="fa fa-times fa-sub text-danger"></i>
            </a>
          </li>
          {acl isAllowed="VIDEO_DELETE"}
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          {/acl}
          {/acl}
          {acl isAllowed="VIDEO_DELETE"}
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
            <select name="status" ng-model="criteria.content_status" data-label="{t}Status{/t}" class="select2">
              <option value="-1"> {t}-- All --{/t} </option>
              <option value="1"> {t}Published{/t} </option>
              <option value="0"> {t}No published{/t} </option>
            </select>
          </li>
          <input type="hidden" name="in_home" ng-model="criteria.in_home">
          <li class="quicklinks hidden-xs hidden-sm">
            <select class="select2 input-medium" name="status" ng-model="pagination.epp" data-label="{t}View{/t}">
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

    {if $category == 'widget'}
    <div class="messages" ng-if="{$total_elements_widget} > 0 && pagination.total != {$total_elements_widget}">
      <div class="alert alert-info">
        <button class="close" data-dismiss="alert">×</button>
        {t 1=$total_elements_widget}You must put %1 videos in the HOME{/t}<br>
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
            <h4>{t}Unable to find any album that matches your search.{/t}</h4>
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
              <th class="hidden-xs hidden-sm"></th>
              <th>{t}Title{/t}</th>
              <th class="center hidden-xs">{t}Section{/t}</th>
              <th class="center nowrap hidden-xs hidden-sm">{t}Created on{/t}</th>
              {acl isAllowed="VIDEO_AVAILABLE"}
              <th class="center" style="width:35px;">{t}Published{/t}</th>
              {/acl}
              {acl isAllowed="VIDEO_FAVORITE"}
              <th class="center hidden-xs" style="width:35px;">{t}Favorite{/t}</th>
              {/acl}
              {acl isAllowed="VIDEO_HOME"}
              <th class="center hidden-xs" style="width:35px;">{t}Home{/t}</th>
              {/acl}
            </tr>
          </thead>
          <tbody {if $category == 'widget'}ui-sortable ng-model="contents"{/if}>
            <tr ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }" data-id="[% content.id %]">
              <td class="checkbox-cell">
                <div class="checkbox check-default">
                  <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                  <label for="checkbox[%$index%]"></label>
                </div>
              </td>
              <td style="width:15px;" class=" hidden-xs">
                <div class="thumbnail">
                  <img ng-src="[% content.thumb %]" ng-if="content.thumb" alt="" style="max-width:60px">
                </div>
              </td>
              <td>
                <div class="thumbnail visible-xs">
                  <img ng-src="[% content.thumb %]" ng-if="content.thumb" alt="" style="max-width:60px">
                </div>
                <strong ng-if="content.author_name != 'internal'">[% content.author_name %]</strong>  - [% content.title %]
                <div class="small-text visible-sm">
                  {t}Created on{/t}: [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                </div>
                <div class="listing-inline-actions">
                  {acl isAllowed="VIDEO_UPDATE"}
                  <a class="link" href="[% edit(content.id, 'admin_video_show') %]">
                    <i class="fa fa-pencil"></i> {t}Edit{/t}
                  </a>
                  {/acl}

                  {acl isAllowed="VIDEO_DELETE"}
                  <button class="del link link-danger" ng-click="sendToTrash(content)" type="button">
                    <i class="fa fa-trash-o"></i> {t}Remove{/t}
                  </button>
                  {/acl}
                </div>
              </td>
              {if $category=='widget' || $category=='all'}
              <td class="center hidden-xs hidden-sm">
                [% content.category_name %]
              </td>
              {/if}
              <td class="center nowrap hidden-xs hidden-sm">
                [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
              </td>
              {acl isAllowed="VIDEO_AVAILABLE"}
              <td class="center">
                <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading == 1 && content.content_status == 1, 'fa-times text-danger': !content.loading == 1 && content.content_status == 0 }"></i>
                </button>
              </td>
              {/acl}
              {acl isAllowed="VIDEO_FAVORITE"}
              <td class="center hidden-xs">
                <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_favorite', 'favorite', content.favorite != 1 ? 1 : 0, 'favorite_loading')" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.favorite_loading == 1, 'fa-star text-warning': !content.favorite_loading == 1 && content.favorite == 1, 'fa-star-o': !content.favorite_loading == 1 && content.favorite != 1 }"></i>
                </button>
              </td>
              {/acl}
              {acl isAllowed="VIDEO_HOME"}
              <td class="right hidden-xs">
                <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.home_loading == 1, 'fa-home text-success': !content.home_loading == 1 && content.in_home == 1, 'fa-home': !content.home_loading == 1 && content.in_home == 0 }"></i>
                  <i class="fa fa-times fa-sub text-danger" ng-if="!content.favorite_loading == 1 && content.in_home == 0"></i>
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
