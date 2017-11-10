<div class="page-navbar selected-navbar collapsed" class="hidden" ng-class="{ 'collapsed': selected.contents.length == 0 }">
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
        {acl isAllowed="KIOSKO_AVAILABLE"}
        <li class="quicklinks">
          <button class="btn btn-link" id="batch-publish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" uib-tooltip="{t}Publish{/t}" tooltip-placement="bottom">
            <i class="fa fa-check fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" id="batch-unpublish" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" uib-tooltip="{t}Unpublish{/t}" tooltip-placement="bottom">
            <i class="fa fa-times fa-lg"></i>
          </button>
        </li>
        {/acl}
        {if $category neq 'widget'}
        {acl isAllowed="KIOSKO_HOME"}
        <li class="quicklinks hidden-xs">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks hidden-xs">
          <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 1, 'home_loading')" uib-tooltip="{t escape="off"}In home{/t}" tooltip-placement="bottom">
            <i class="fa fa-home fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks hidden-xs">
          <button class="btn btn-link" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 0, 'home_loading')" uib-tooltip="{t escape="off"}Drop from home{/t}" tooltip-placement="bottom">
            <i class="fa fa-home fa-lg"></i>
            <i class="fa fa-times fa-sub text-danger"></i>
          </button>
        </li>
        {/acl}
        {/if}
        {acl isAllowed="KIOSKO_DELETE"}
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" id="batch-delete" ng-click="sendToTrashSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom">
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
        <li class="quicklinks hidden-xs ng-cloak"  ng-init="categories = {json_encode($categories)|clear_json}">
          <ui-select name="author" theme="select2" ng-model="criteria.pk_fk_content_category">
            <ui-select-match>
              <strong>{t}Category{/t}:</strong> [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="item.value as item in categories | filter: { name: $select.search }">
              <div ng-bind-html="item.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
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
      <input type="hidden" name="in_home" ng-model="criteria.in_home">
    </div>
  </div>
</div>

<div class="content" ng-init="init('kiosko', 'backend_ws_contents_list')">
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="spinner-wrapper" ng-if="loading">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
        <div class="center">
          <h4>{t}Unable to find any cover that matches your search.{/t}</h4>
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
              <th class="center hidden-xs hidden-sm" style="width:10px"></th>
              <th>{t}Title{/t}</th>
              {if $category=='widget' || $category == 'all'}
              <th class="center hidden-xs" width="200">{t}Section{/t}</th>
              {/if}
              <th class="hidden-xs text-center" width="200">{t}Date{/t}</th>
              <th class="hidden-xs text-center" width="200">{t}Price{/t}</th>
              <th class="hidden-xs text-center" width="100">{t}Home{/t}</th>
              <th class="hidden-xs text-center" width="100">{t}Favorite{/t}</th>
              <th class="text-center" width="100">{t}Published{/t}</th>
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
              <td class="center hidden-xs hidden-sm">
                <img ng-src="{$KIOSKO_IMG_URL}[% content.path%][% content.thumb_url %]"
                title="{$cover->title|clearslash}" alt="{$cover->title|clearslash}" style="max-width:80px" class="thumbnail" />
              </td>
              <td>
                <img ng-src="{$KIOSKO_IMG_URL}[% content.path%][% content.thumb_url %]"
                title="{$cover->title|clearslash}" alt="{$cover->title|clearslash}" style="max-width:80px" class="thumbnail visible-xs visible-sm" />
                <span uib-tooltip="{t}Last editor{/t} [% shvs.extra.authors[content.fk_user_last_editor].name %]">[% content.title%]</span>
                <div class="listing-inline-actions">
                  {acl isAllowed="VIDEO_UPDATE"}
                    <a class="link" href="[% edit(content.id, 'admin_kiosko_show') %]">
                      <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                    </a>
                  {/acl}
                  {acl isAllowed="VIDEO_DELETE"}
                    <button class="del link link-danger" ng-click="sendToTrash(content)" type="button">
                      <i class="fa fa-trash-o m-r-5"></i>{t}Remove{/t}
                    </button>
                  {/acl}
                </div>
              </td>
              {if $category == 'widget' || $category == 'all'}
                <td class="hidden-xs text-center">
                  [% content.category_name %]
                </td>
              {/if}
              <td class="hidden-xs text-center">
                [% content.date %]
              </td>
              <td class="hidden-xs text-center">
                [% content.price | number : 2 %] €
              </td>
              {acl isAllowed="KIOSKO_HOME"}
                <td class="hidden-xs text-center">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.home_loading == 1, 'fa-home text-info': ! content.home_loading && content.in_home == 1, 'fa-home': ! content.home_loading &&content.in_home == 0 }"></i>
                    <i class="fa fa-times fa-sub text-danger" ng-if="!content.home_loading && content.in_home != 1"></i>
                  </button>
                </td>
              {/acl}
              {acl isAllowed="KIOSKO_AVAILABLE"}
                <td class="text-center hidden-xs">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_favorite', 'favorite', content.favorite != 1 ? 1 : 0, 'favorite_loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.favorite_loading == 1, 'fa-star text-warning': !content.favorite_loading && content.favorite == 1, 'fa-star-o': !content.favorite_loading && content.favorite != 1 }"></i>
                    <i class="fa fa-times fa-sub text-danger" ng-if="!content.favorite_loading && content.favorite != 1"></i>
                  </button>
                </td>
              {/acl}
              {acl isAllowed="KIOSKO_AVAILABLE"}
                <td class="text-center">
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
      <div class="pull-right">
        <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
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
