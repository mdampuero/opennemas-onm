<div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.contents.length == 0 }">
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
            [% selected.contents.length %] <span class="hidden-xs">{t}items selected{/t}</span>
          </h4>
        </li>
      </ul>
      <ul class="nav quick-section pull-right">
        {acl isAllowed="VIDEO_AVAILABLE"}
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
        <li class="quicklinks hidden-xs">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks hidden-xs">
          <button class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 1, 'home_loading')" uib-tooltip="{t escape="off"}In home{/t}" uib-tooltip="{t escape="off"}In home{/t}" tooltip-placement="bottom">
            <i class="fa fa-home"></i>
          </button>
        </li>
        <li class="quicklinks hidden-xs">
          <button class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 0, 'home_loading')" uib-tooltip="{t escape="off"}Drop from home{/t}" uib-tooltip="{t escape="off"}Drop from home{/t}" tooltip-placement="bottom">
            <i class="fa fa-home"></i>
            <i class="fa fa-times fa-sub text-danger"></i>
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
          <li class="quicklinks ng-cloak" ng-if="!mode || mode === 'grid'" uib-tooltip="{t}List{/t}" tooltip-placement="bottom">
            <button class="btn btn-link" ng-click="setMode('list')">
              <i class="fa fa-lg fa-list"></i>
            </button>
          </li>
          <li class="quicklinks ng-cloak" ng-if="mode === 'list'" uib-tooltip="{t}Mosaic{/t}" tooltip-placement="bottom">
            <button class="btn btn-link" ng-click="setMode('grid')">
              <i class="fa fa-lg fa-th"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
        <li class="m-r-10 input-prepend inside search-input no-boarder">
          <span class="add-on">
            <span class="fa fa-search fa-lg"></span>
          </span>
          <input class="no-boarder" name="title" ng-model="criteria.title_like" placeholder="{t}Search by title{/t}" type="text"/>
        </li>
        <li class="quicklinks hidden-xs">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks hidden-xs ng-cloak"  ng-init="categories = {json_encode($categories)|clear_json}">
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
        <li class="quicklinks hidden-xs ng-cloak" ng-if="mode === 'list'">
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
      <ul class="nav quick-section pull-right ng-cloak" ng-if="mode === 'list' && contents.length > 0">
        <li class="quicklinks hidden-xs">
          <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="content" ng-init="setMode('grid');init('video', { content_status: -1, title_like: '', category_name: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
  {if $category == 'widget'}
  <div class="messages" ng-if="{$total_elements_widget} > 0 && pagination.total != {$total_elements_widget}">
    <div class="alert alert-info">
      <button class="close" data-dismiss="alert">×</button>
      {t 1=$total_elements_widget}You must put %1 videos in the HOME{/t}<br>
    </div>
  </div>
  {/if}
  <div class="grid simple ng-cloak" ng-if="mode === 'list'">
    <div class="grid-body no-padding">
      <div class="spinner-wrapper" ng-if="loading">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
        <div class="center">
          <h4>{t}Unable to find any video that matches your search.{/t}</h4>
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
              <th class="hidden-xs hidden-sm" style="width: 150px;"></th>
              <th>{t}Title{/t}</th>
              <th class="center hidden-xs">{t}Section{/t}</th>
              <th class="center nowrap hidden-xs hidden-sm">{t}Author{/t}</th>
              {acl isAllowed="VIDEO_HOME"}
                <th class="hidden-xs text-center" width="100">{t}Home{/t}</th>
              {/acl}
              {acl isAllowed="VIDEO_FAVORITE"}
                <th class="hidden-xs text-center" width="100">{t}Favorite{/t}</th>
              {/acl}
              {acl isAllowed="VIDEO_AVAILABLE"}
                <th class="text-center" width="100">{t}Published{/t}</th>
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
              <td class="hidden-sm hidden-xs">
                <div style="height: 120px; width: 120px;">
                  <dynamic-image ng-if="content.thumb_image" autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="content.thumb_image"></dynamic-image>
                  <dynamic-image ng-if="!content.thumb_image" autoscale="true" class="img-thumbnail" ng-model="content.thumb"></dynamic-image>
                </div>
              </td>
              <td>
                <div class="visible-xs visible-sm" style="height: 150px; width: 150px; margin: 0 auto 15px;">
                  <dynamic-image ng-if="content.thumb_image" autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="content.thumb_image"></dynamic-image>
                  <dynamic-image ng-if="!content.thumb_image" autoscale="true" class="img-thumbnail" ng-model="content.thumb"></dynamic-image>
                </div>
                [% content.title %]
                <div class="small-text">
                  <strong>{t}Created{/t}:</strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
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
                [% extra.categories[content.category] %]
              </td>
              {/if}
              <td class="center nowrap hidden-xs hidden-sm">
                [% content.author_name %]
              </td>
              {acl isAllowed="VIDEO_HOME"}
                <td class="hidden-xs text-center">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.home_loading == 1, 'fa-home text-info': !content.home_loading == 1 && content.in_home == 1, 'fa-home': !content.home_loading == 1 && content.in_home == 0 }"></i>
                    <i class="fa fa-times fa-sub text-danger" ng-if="!content.home_loading == 1 && content.in_home == 0"></i>
                  </button>
                </td>
              {/acl}
              {acl isAllowed="VIDEO_FAVORITE"}
                <td class="hidden-xs text-center">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_favorite', 'favorite', content.favorite != 1 ? 1 : 0, 'favorite_loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.favorite_loading == 1, 'fa-star text-warning': !content.favorite_loading == 1 && content.favorite == 1, 'fa-star-o': !content.favorite_loading == 1 && content.favorite != 1 }"></i>
                  </button>
                </td>
              {/acl}
              {acl isAllowed="VIDEO_AVAILABLE"}
                <td class="text-center">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading == 1 && content.content_status == 1, 'fa-times text-danger': !content.loading == 1 && content.content_status == 0 }"></i>
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
  <div class="content-wrapper">
    <div class="ng-cloak spinner-wrapper" ng-if="(!mode || mode === 'grid') && loading">
      <div class="loading-spinner"></div>
      <div class="spinner-text">{t}Loading{/t}...</div>
    </div>
    <div class="clearfix row ng-cloak" ng-if="!mode || mode == 'grid'">
      <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-15 infinite-col media-item selectable" ng-class="{ 'selected': isSelected(content.id) }" ng-repeat="content in contents">
        <div class="dynamic-image-placeholder" ng-click="select(content); xsOnly($event, toggle, content)">
          <dynamic-image ng-if="content.thumb_image" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="content.thumb_image">
            <div class="hidden-select" ng-click="toggle(content)"></div>
            <div class="thumbnail-actions ng-cloak">
              {acl isAllowed="VIDEO_DELETE"}
                <div class="thumbnail-action remove-action" ng-click="sendToTrash(content);$event.stopPropagation()">
                  <i class="fa fa-trash-o fa-2x"></i>
                </div>
              {/acl}
              {acl isAllowed="VIDEO_UPDATE"}
                <a class="thumbnail-action" href="[% edit(content.id, 'admin_video_show') %]" ng-click="$event.stopPropagation()">
                  <i class="fa fa-pencil fa-2x"></i>
                </a>
              {/acl}
            </div>
          </dynamic-image>
          <dynamic-image ng-if="!content.thumb_image" class="img-thumbnail" ng-model="content.thumb">
            <div class="hidden-select" ng-click="toggle(content)"></div>
             <div class="thumbnail-actions ng-cloak">
              {acl isAllowed="VIDEO_DELETE"}
                <div class="thumbnail-action remove-action" ng-click="sendToTrash(content);$event.stopPropagation()">
                  <i class="fa fa-trash-o fa-2x"></i>
                </div>
              {/acl}
              {acl isAllowed="VIDEO_UPDATE"}
                <a class="thumbnail-action" href="[% edit(content.id, 'admin_video_show') %]" ng-click="$event.stopPropagation()">
                  <i class="fa fa-pencil fa-2x"></i>
                </a>
              {/acl}
            </div>
          </dynamic-image>
        </div>
      </div>
    </div>
    <div class="ng-cloak p-t-15 p-b-15 pointer text-center" ng-click="scroll('backend_ws_contents_list')" ng-if="!loading && mode == 'grid' && pagination.total != contents.length">
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
