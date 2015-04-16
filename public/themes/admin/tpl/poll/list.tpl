{extends file="base/admin.tpl"}

{block name="footer-js" append}
{javascripts src="@AdminTheme/js/onm/jquery-functions.js"}
<script type="text/javascript" src="{$asset_url}"></script>
{/javascripts}
{/block}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init('poll', { content_status: -1, category_name: -1, in_home: {if $category == 'widget'}1{else}-1{/if}, title_like: '', in_litter: 0{if $category == 'widget'}, in_home: 1{/if} }, {if $category == 'widget'}'position'{else}'created'{/if}, {if $category == 'widget'}'asc'{else}'desc'{/if}, 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-pie-chart"></i>
              {t}Polls{/t}
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
                <a href="{url name=admin_polls_widget}">
                  {t}Widget Home{/t}
                </a>
              </li>
              <li>
                <a href="{url name=admin_polls}">
                  {t}Listing{/t}
                </a>
              </li>
            </ul>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="POLL_SETTINGS"}
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_polls_config}" title="{t}Config album module{/t}">
                <i class="fa fa-gear fa-lg"></i>
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            {/acl}
            {acl isAllowed="POLL_WIDGET"}
            {if $category eq 'widget'}
            <li class="quicklinks">
              <a class="btn btn-white" href="#" ng-click="savePositions('backend_ws_contents_save_positions')" title="{t}Save positions{/t}">
                <i class="fa fa-save fa-lg"></i>
                {t}Save positions{/t}
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            {/if}
            {/acl}
            {acl isAllowed="POLL_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=admin_poll_create}" title="{t}New poll{/t}">
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
          {acl isAllowed="POLL_AVAILABLE"}
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
          {acl isAllowed="POLL_DELETE"}
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
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
        <ul class="nav quick-section pull-right ng-cloak" ng-if="contents.length > 0">
          <li class="quicklinks hidden-xs">
            <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="content">
    {render_messages}

    {if $category == 'widget' && $total_elements_widget > 0}
    <div class="messages" ng-if="{$total_elements_widget} > 0 && pagination.total != {$total_elements_widget}">
      <div class="alert alert-info">
        <button class="close" data-dismiss="alert">×</button>
        {t 1=$total_elements_widget}You must put %1 polls in the HOME{/t}<br>
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
            <h4>{t}Unable to find any poll that matches your search.{/t}</h4>
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
              <th>{t}Title{/t}</th>
              <th style="width:65px;" class="center hidden-xs">{t}Section{/t}</th>
              <th class="center hidden-xs hidden-sm" style="width:40px">{t}Votes{/t}</th>
              {acl isAllowed="POLL_AVAILABLE"}
              <th style="width:40px;" class="center">{t}Published{/t}</th>
              {/acl}
              {acl isAllowed="POLL_FAVORITE"}
              <th class="center hidden-xs" style="width:35px;">{t}Favorite{/t}</th>
              {/acl}
              {acl isAllowed="POLL_HOME"}
              <th style="width:40px;" class="center hidden-xs">{t}Home{/t}</th>
              {/acl}
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
                <div class="small-text">
                  <strong>{t}Created{/t}:</strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                </div>
                <div class="listing-inline-actions">
                  {acl isAllowed="POLL_UPDATE"}
                  <a class="link" href="[% edit(content.id, 'admin_poll_show') %]">
                    <i class="fa fa-pencil"></i>
                    {t}Edit{/t}
                  </a>
                  {/acl}
                  {acl isAllowed="POLL_DELETE"}
                  <button class="link link-danger" ng-click="sendToTrash(content)" type="button">
                    <i class="fa fa-trash-o"></i>
                    {t}Delete{/t}
                  </button>
                  {/acl}
                </ul>
              </td>
              <td class="center hidden-xs">
                [% content.category_name %]
              </td>
              <td class="center hidden-xs hidden-sm">
                [% content.total_votes %]
              </td>
              {acl isAllowed="POLL_AVAILABLE"}
              <td class="center">
                <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading && content.content_status == 1, 'fa-times text-danger': !content.loading && content.content_status == 0 }"></i>
                </button>
              </td>
              {/acl}
              {acl isAllowed="POLL_FAVORITE"}
              <td class="center hidden-xs">
                <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_favorite', 'favorite', content.favorite != 1 ? 1 : 0, 'favorite_loading')" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.favorite_loading == 1, 'fa-star text-warning': !content.favorite_loading && content.favorite == 1, 'fa-star-o': !content.favorite_loading && content.favorite != 1 }"></i>
                </button>
              </td>
              {/acl}
              {acl isAllowed="POLL_HOME"}
              <td class="right hidden-xs">
                <button class="btn btn-white" ng-if="content.author.meta.is_blog != 1" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.home_loading == 1, 'fa-home text-info': !content.home_loading && content.in_home == 1, 'fa-home': !content.home_loading && content.in_home == 0 }"></i>
                  <i class="fa fa-times fa-sub text-danger" ng-if="!content.home_loading && content.in_home == 0"></i>
                </button>
              </td>
              {/acl}
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
