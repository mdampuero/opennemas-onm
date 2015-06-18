{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ContentListCtrl" ng-init="init('photo', { content_status: -1, title_like: '', category_name: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-picture-o"></i>
              {t}Images{/t}
            </h4>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks dropdown ng-cloak">
              <div data-toggle="dropdown">
                <span ng-if="!mode || mode == 'list'">{t}List{/t}</span>
                <span ng-if="mode == 'grid'">{t}Mosaic{/t}</span>
                <span class="caret"></span>
              </div>
              <ul class="dropdown-menu">
                <li ng-click="setMode('list')">
                  <a href="#">
                    <i class="fa fa-lg fa-list"></i>
                    {t}List{/t}
                  </a>
                </li>
                <li ng-click="setMode('grid')">
                  <a href="#">
                    <i class="fa fa-lg fa-th"></i>
                    {t}Mosaic{/t}
                  </a>
                </li>
              </ul>
            </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="PHOTO_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-primary" media-picker media-picker-mode="explore,upload" media-picker-mode-active="upload">
                <span class="fa fa-cloud-upload"></span> {t}Upload{/t}
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
            <button class="btn btn-link" ng-click="deselectAll()" tooltip="{t}Clear selection{/t}" tooltip-placement="right"type="button">
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
          {acl isAllowed="PHOTO_DELETE"}
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
  <div class="page-navbar filters-navbar" ng-if="mode !== 'grid'">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="no-boarder" name="title" ng-model="criteria.title_like" placeholder="{t}Search by title{/t}" type="text"/>
            <input type="hidden" name="in_home" ng-model="criteria.in_home">
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
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
  <div class="content clearfix">
    {render_messages}
    <div class="grid simple ng-cloak" ng-if="mode !== 'grid'">
      <div class="grid-body no-padding">
        <div class="spinner-wrapper" ng-if="loading">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
          <div class="center">
            <h4>{t}Unable to find any image that matches your search.{/t}</h4>
            <h6>{t}Maybe changing any filter could help or add one using the "Upload" button above.{/t}</h6>
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
                <th style="width:80px">&nbsp;</th>
                <th class="hidden-xs">{t}Information{/t}</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-if="contents.length == 0">
                <td class="empty" colspan="10">
                  <p>
                    <img src="{$params.IMAGE_DIR}/search/search-images.png">
                  </p>
                  {t escape=off}No available images for this search{/t}
                </td>
              </tr>
              <tr ng-if="contents.length > 0" ng-repeat="content in contents"  ng-class="{ row_selected: isSelected(content.id) }" data-id="[% content.id %]">
                <td class="checkbox-cell">
                  <div class="checkbox check-default">
                    <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td class="hidden-xs">
                  <div ng-click="open('modal-image', content)" style="height: 120px; width: 120px; margin-bottom: 15px;">
                    <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="content" transform="thumbnail,220,220"></dynamic-image>
                  </div>
                </td>
                <td>
                  <div ng-click="open('modal-image', content)" class="visible-xs center" style="height: 200px; width: 200px; margin: 0 auto 15px;">
                    <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="content" transform="thumbnail,220,220"></dynamic-image>
                  </div>
                  <div class="description">
                    <span ng-if="content.description != ''">[% content.description %]</span>
                    <span ng-if="content.description == ''">{t}No available description{/t}</span>
                  </div>
                  <div class="small-text">
                    <strong>{t}Created{/t}:</strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                  </div>
                  <div>
                    <div class="listing-inline-actions">
                      {acl isAllowed="PHOTO_UPDATE"}
                      <a class="link" href="[% edit(content.id, 'admin_photo_show') %]">
                        <i class="fa fa-pencil"></i> {t}Edit{/t}
                      </a>
                      {/acl}
                      {acl isAllowed="PHOTO_DELETE"}
                      <button class="del link link-danger" ng-click="sendToTrash(content)" type="button">
                        <i class="fa fa-trash-o"></i> {t}Remove{/t}
                      </button>
                      {/acl}
                      <a class="link" href="{$MEDIA_IMG_URL}[% content.path_file + '/' + content.name %]" target="_blank">
                        <i class="fa fa-external-link"></i> {t}Link{/t}
                      </a>
                    </div>
                  </div>
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
    <div class="clearfix infinite-row ng-cloak" ng-if="mode == 'grid'">
      <div class="col-md-2 col-sm-4 m-b-15 infinite-col" ng-repeat="content in contents">
        <div class="dynamic-image-placeholder">
          <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="content" transform="zoomcrop,400,400">
            <div class="thumbnail-actions ng-cloak">
              {acl isAllowed="PHOTO_DELETE"}
                <div class="thumbnail-action remove-action" ng-click="sendToTrash(content)">
                  <i class="fa fa-trash-o fa-2x"></i>
                </div>
              {/acl}
              {acl isAllowed="PHOTO_UPDATE"}
                <a class="thumbnail-action" href="[% edit(content.id, 'admin_photo_show') %]">
                  <i class="fa fa-pencil fa-2x"></i>
                </a>
              {/acl}
            </div>
          </dynamic-image>
        </div>
      </div>
    </div>
    <div class="ng-cloak p-t-15 p-b-15 pointer text-center" ng-click="scroll('backend_ws_contents_list')" ng-if="!searchLoading && mode == 'grid' && pagination.total != contents.length">
      <h5>
        <i class="fa fa-circle-o-notch fa-spin fa-lg" ng-if="loadingMore"></i>
        <span ng-if="!loadingMore">{t}Load more{/t}</span>
        <span ng-if="loadingMore">{t}Loading{/t}</span>
      </h5>
    </div>
    <div class="infinite-row master-row ng-cloak">
      <div class="col-md-2 col-sm-4 m-b-15 infinite-col">
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
  <script type="text/ng-template" id="modal-image">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
      <h4 class="modal-title">{t}Image preview{/t}</h4>
    </div>
    <div class="modal-body">
      <div class="resource">
        <span ng-if="template.selected.type_img == 'swf'">
          <object ng-data="'{$MEDIA_IMG_URL}[% template.selected.path_file %][% template.selected.name %]'" ng-param="{ 'vmode': 'opaque' }"></object>
        </span>
        <span ng-if="template.selected.type_img !== 'swf'">
          <img class="img-responsive" ng-src="{$MEDIA_IMG_URL}[% template.selected.path_file + template.selected.name %]"/>
        </span>
      </div>
      <div class="details">
        <h4 class="description">
          <span ng-if="template.selected.description != ''">[% template.selected.description %]</span>
          <span ng-if="template.selected.description == ''">{t}No available description{/t}</span>
        </h4>
        <div><strong>{t}Filename{/t}</strong> [% template.selected.title %]</div>
        <div class="tags">
          <img src="{$params.IMAGE_DIR}tag_red.png" />
          <span ng-if="template.selected.metadata != ''">[% template.selected.metadata %]</span>
          <span ng-if="template.selected.metadata == ''">{t}No tags{/t}</span>
        </div>
        <span class="author" ng-if="template.selected.author != ''">
          <strong>{t}Author:{/t}</strong> {$photo->author_name|clearslash|default:""}
        </span>
        <div><strong>{t}Created on{/t}</strong> [% template.selected.created %]</div>

        <div><strong>{t}Resolution:{/t}</strong> [% template.selected.width %] x [% template.selected.height %] (px)</div>
        <div><strong>{t}Size:{/t}</strong> [% template.selected.size %] Kb</div>
      </div>
    </div>
  </script>
</div>
{/block}
