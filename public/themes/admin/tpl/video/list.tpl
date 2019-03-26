{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="VideoListCtrl" ng-init="forcedLocale = '{$locale}'; init()">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="{url name=backend_videos_list}" title="{t}Go back to list{/t}">
                <i class="fa fa-film"></i>
                {t}Videos{/t}
              </a>
            </h4>
          </li>
          <li class="quicklinks m-l-5 m-r-5 ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
            <translator keys="data.extra.keys" ng-model="config.locale.selected" options="data.extra.locale"></translator>
            [% mode %]
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
            {/acl}

            {acl isAllowed="VIDEO_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-success text-uppercase" href="{url name=backend_videos_create}" accesskey="N" tabindex="1" id="create-button">
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

  <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.items.length == 0 }">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section pull-left">
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="Clear selection" tooltip-placement="right"type="button">
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
          <li class="quicklinks hidden-xs">
            <button class="btn btn-link" href="#" ng-click="patchSelected('favorite', 1)" uib-tooltip="{t escape="off"}Favorite{/t}" tooltip-placement="bottom">
              <i class="fa fa-star"></i>
            </button>
          </li>
          <li class="quicklinks hidden-xs">
            <button class="btn btn-link" href="#" ng-click="patchSelected('favorite', 0)" uib-tooltip="{t escape="off"}Unfavorite{/t}" tooltip-placement="bottom">
              <i class="fa fa-star"></i>
              <i class="fa fa-times fa-sub text-danger"></i>
            </button>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          {acl isAllowed="VIDEO_AVAILABLE"}
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
          {acl isAllowed="VIDEO_DELETE"}
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          {/acl}
          {/acl}
          {acl isAllowed="VIDEO_DELETE"}
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
  <div class="page-navbar filters-navbar ng-cloak">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks" ng-if="mode === 'grid'" uib-tooltip="{t}List{/t}" tooltip-placement="bottom">
            <button class="btn btn-link" ng-click="setMode('list')">
              <i class="fa fa-lg fa-list"></i>
            </button>
          </li>
          <li class="quicklinks ng-cloak" ng-if="!mode || mode === 'list'" uib-tooltip="{t}Mosaic{/t}" tooltip-placement="bottom">
            <button class="btn btn-link" ng-click="setMode('grid')">
              <i class="fa fa-lg fa-th"></i>
            </button>
          </li>
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="no-boarder" name="title" ng-model="criteria.title" placeholder="{t}Search by title{/t}" type="text"/>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs">
          {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
          </li>
          <li class="quicklinks hidden-xs ng-cloak"  ng-init="categories = {json_encode($categories)|clear_json}">
            <onm-category-selector ng-model="criteria.pk_fk_content_category" label-text="{t}Category{/t}" default-value-text="{t}Any{/t}" placeholder="{t}Any{/t}" />
          </li>
          <li class="quicklinks hidden-xs ng-cloak" ng-show="mode === 'list'">
            {include file="ui/component/select/epp.tpl" label="true" ngModel="criteria.epp"}
          </li>
        </ul>
        <ul class="nav quick-section pull-right ng-cloak" ng-if="mode === 'list' && items.length > 0">
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
    <div class="listing-no-contents ng-cloak" ng-show="!flags.http.loading && items.length == 0">
      <div class="text-center p-b-15 p-t-15">
        <i class="fa fa-4x fa-warning text-warning"></i>
        <h3>{t}Unable to find any item that matches your search.{/t}</h3>
        <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
      </div>
    </div>
    <div class="grid simple ng-cloak" ng-hide="flags.http.loading">
      <div class="grid-body no-padding">
        <div class="table-wrapper ng-cloak" ng-if="!loading && items.length > 0">
          <table class="table table-hover no-margin">
            <thead>
              <tr>
                <th class="checkbox-cell">
                  <div class="checkbox checkbox-default">
                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                    <label for="select-all"></label>
                  </div>
                </th>
                <th class="hidden-xs hidden-sm"></th>
                <th>{t}Title{/t}</th>
                <th class="center hidden-xs">{t}Section{/t}</th>
                {* <th class="center nowrap hidden-xs hidden-sm">{t}Author{/t}</th> *}
                {* {acl isAllowed="VIDEO_HOME"}
                  <th class="hidden-xs text-center" width="100">{t}Home{/t}</th>
                {/acl} *}
                {acl isAllowed="VIDEO_FAVORITE"}
                  <th class="hidden-xs text-center" width="100">{t}Favorite{/t}</th>
                {/acl}
                {acl isAllowed="VIDEO_AVAILABLE"}
                  <th class="text-center" width="100">{t}Published{/t}</th>
                {/acl}
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }" data-id="[% item.id %]">
                <td class="checkbox-cell">
                  <div class="checkbox check-default">
                    <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td class="hidden-sm hidden-xs">
                  <div style="height: 120px; width: 120px;">
                    <dynamic-image ng-if="item.thumb_image" autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.thumb_image"></dynamic-image>
                    <dynamic-image ng-if="!item.thumb_image" autoscale="true" class="img-thumbnail" ng-model="item.thumb"></dynamic-image>
                  </div>
                </td>
                <td>
                  <div class="visible-xs visible-sm" style="height: 150px; width: 150px; margin: 0 auto 15px;">
                    <dynamic-image ng-if="item.thumb_image" autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.thumb_image"></dynamic-image>
                    <dynamic-image ng-if="!item.thumb_image" autoscale="true" class="img-thumbnail" ng-model="item.thumb"></dynamic-image>
                  </div>
                  [% item.title %]
                  <div class="small-text">
                    <strong>{t}Created{/t}:</strong> [% item.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                  </div>
                  <div class="listing-inline-actions">
                    {acl isAllowed="VIDEO_UPDATE"}
                    <a class="btn btn-default btn-small" href="[% routing.generate('backend_video_show', { id: getId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
                      <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                    </a>
                    <translator item="data.items[$index]" keys="data.extra.keys" link="[% routing.generate('backend_video_show', { id: getId(item) }) %]" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
                    {/acl}

                    {acl isAllowed="VIDEO_DELETE"}
                    <button class="btn btn-danger btn-small" ng-click="sendToTrash(item)" type="button">
                      <i class="fa fa-trash-o m-r-5"></i> {t}Remove{/t}
                    </button>
                    {/acl}
                  </div>
                </td>
                <td class="center hidden-xs hidden-sm">
                  [% extra.categories[item.category] %]
                </td>
                {* <td class="center nowrap hidden-xs hidden-sm">
                  [% item.author_name %]
                </td> *}
                {* {acl isAllowed="VIDEO_HOME"}
                  <td class="hidden-xs text-center">
                    <button class="btn btn-white" ng-click="updateItem($index, item.id, 'backend_ws_content_toggle_in_home', 'in_home', item.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.home_loading == 1, 'fa-home text-info': !item.home_loading == 1 && item.in_home == 1, 'fa-home': !item.home_loading == 1 && item.in_home == 0 }"></i>
                      <i class="fa fa-times fa-sub text-danger" ng-if="!item.home_loading == 1 && item.in_home == 0"></i>
                    </button>
                  </td>
                {/acl} *}
                {acl isAllowed="VIDEO_FAVORITE"}
                  <td class="hidden-xs text-center">
                    <button class="btn btn-white" ng-click="patch(item, 'favorite', item.favorite != 1 ? 1 : 0)" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.favoriteLoading == 1, 'fa-star text-warning': !item.favoritLoading == 1 && item.favorite == 1, 'fa-star-o': !item.favoriteLoading == 1 && item.favorite != 1 }"></i>
                    </button>
                  </td>
                {/acl}
                {acl isAllowed="VIDEO_AVAILABLE"}
                  <td class="text-center">
                    <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading == 1, 'fa-check text-success': !item.content_statusLoading == 1 && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading == 1 && item.content_status == 0 }"></i>
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
    <div class="content-wrapper">
      <div class="ng-cloak spinner-wrapper" ng-if="(!mode || mode === 'grid') && loading">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
      <div class="clearfix row ng-cloak" ng-if="!mode || mode == 'grid'">
        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-15 infinite-col media-item selectable" ng-class="{ 'selected': isSelected(item.id) }" ng-repeat="content in contents">
          <div class="dynamic-image-placeholder" ng-click="select(content); xsOnly($event, toggle, content)">
            <dynamic-image ng-if="item.thumb_image" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.thumb_image">
              <div class="hidden-select" ng-click="toggle(content)"></div>
              <div class="thumbnail-actions ng-cloak">
                {acl isAllowed="VIDEO_DELETE"}
                  <div class="thumbnail-action remove-action" ng-click="sendToTrash(content);$event.stopPropagation()">
                    <i class="fa fa-trash-o fa-2x"></i>
                  </div>
                {/acl}
                {acl isAllowed="VIDEO_UPDATE"}
                  <a class="thumbnail-action" href="[% edit(item.id, 'admin_video_show') %]" ng-click="$event.stopPropagation()">
                    <i class="fa fa-pencil fa-2x"></i>
                  </a>
                {/acl}
              </div>
            </dynamic-image>
            <dynamic-image ng-if="!item.thumb_image" class="img-thumbnail" ng-model="item.thumb">
              <div class="hidden-select" ng-click="toggle(content)"></div>
              <div class="thumbnail-actions ng-cloak">
                {acl isAllowed="VIDEO_DELETE"}
                  <div class="thumbnail-action remove-action" ng-click="sendToTrash(content);$event.stopPropagation()">
                    <i class="fa fa-trash-o fa-2x"></i>
                  </div>
                {/acl}
                {acl isAllowed="VIDEO_UPDATE"}
                  <a class="thumbnail-action" href="[% edit(item.id, 'admin_video_show') %]" ng-click="$event.stopPropagation()">
                    <i class="fa fa-pencil fa-2x"></i>
                  </a>
                {/acl}
              </div>
            </dynamic-image>
          </div>
        </div>
      </div>
      <div class="ng-cloak p-t-15 p-b-15 pointer text-center" ng-click="scroll('backend_ws_contents_list')" ng-if="!loading && mode == 'grid' && total != contents.length">
        <h5>
          <i class="fa fa-circle-o-notch fa-spin fa-lg" ng-if="loadingMore"></i>
          <span ng-if="!loadingMore">{t}Load more{/t}</span>
          <span ng-if="loadingMore">{t}Loading{/t}</span>
        </h5>
      </div>
      <div class="row master-row ng-cloak">
        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-15 infinite-col media-item">
        </div>
      </div>
    </div>
    <div class="content-sidebar hidden-sm ng-cloak" ng-if="mode === 'grid'">
      <div class="center p-t-15" ng-if="!selected.lastSelected">
        <h4>{t}No item selected{/t}</h4>
        <h6>{t}Click in one item to show information about it{/t}</h6>
      </div>
      <h4 class="ng-cloak" ng-show="selected.lastSelected">{t}Video details{/t}</h4>
      <div ng-if="selected.lastSelected">
        <div class="pointer thumbnail-wrapper" ng-click="open('modal-image', selected.lastSelected)" ng-if="selected.lastSelected.video_url != ''">
          <dynamic-image autoscale="true" only-image="true" ng-model="selected.lastSelected.thumb"></dynamic-image>
        </div>
        <div class="pointer thumbnail-wrapper" ng-click="open('modal-image', selected.lastSelected)" ng-if="selected.lastSelected.video_url == ''">
          <dynamic-image autoscale="true" only-image="true" ng-model="selected.lastSelected.thumb_image" instance="{$smarty.const.INSTANCE_MEDIA}"></dynamic-image>
        </div>
        <p></p>
        <ul class="media-information">
          <li>
            <strong>[% selected.lastSelected.name %]</strong>
          </li>
          <li>
            <a class="btn btn-default" ng-href="[% routing.generate('admin_video_show', { id: selected.lastSelected.id}) %]">
              <strong>
                <i class="fa fa-edit"></i>
                {t}Edit{/t}
              </strong>
            </a>
          </li>
          <li>[% selected.lastSelected.created | moment %]</li>
          <li>
            <div class="form-group">
              <label for="description">
                <strong>{t}Description{/t}</strong>
                <div class="pull-right">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': saving, 'fa-check text-success': saved, 'fa-times text-danger': error }"></i>
                </div>
              </label>
              <textarea id="description" ng-blur="saveDescription(selected.lastSelected.id)" ng-model="selected.lastSelected.description" cols="30" rows="2"></textarea>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <script type="text/ng-template" id="modal-delete">
    {include file="common/modals/modal.trash.tpl"}
  </script>

  <script type="text/ng-template" id="modal-delete-selected">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>

  <script type="text/ng-template" id="modal-update-selected">
    {include file="common/modals/_modalBatchUpdate.tpl"}
  </script>
</div>
{/block}
