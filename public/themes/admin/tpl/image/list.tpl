{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="post" ng-app="BackendApp" ng-controller="ContentListController" ng-init="init('photo', { content_status: -1, title_like: '', category_name: -1, in_litter: 0 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
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
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="PHOTO_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-primary" media-picker media-picker-mode="explore,upload">
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
  <div class="page-navbar filters-navbar">
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
          <li class="quicklinks hidden-xs">
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
    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="spinner-wrapper" ng-if="loading">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div class="table-wrapper ng-cloak">
          <table class="table table-hover no-margin" ng-if="!loading">
            <thead>
              <tr>
                <th class="checkbox-cell">
                  <div class="checkbox checkbox-default">
                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                    <label for="select-all"></label>
                  </div>
                </th>
                <th style="width:80px" class="hidden-xs"></th>
                <th class="hidden-xs">{t}Information{/t}</th>
                <th class="hidden-xs hidden-sm">{t}Created on{/t}</th>
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
                  <div ng-click="open('modal-image', content)" style="width: 120px; height: 120px;">
                    <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="content" transform="thumbnail,220,220"></dynamic-image>
                  </div>
                </td>
                <td>
                  <div ng-click="open('modal-image', content)" class="visible-xs center">
                    <span ng-if="content.type_img == 'swf'" class="thumbnail">
                      <object ng-data="'{$MEDIA_IMG_URL}[% content.path_file %][% content.name %]'" ng-param="{ 'vmode': 'opaque' }"  style="width:100px;height:80px"></object>
                      <img class="image-preview" style="width:16px;height:16px;border:none;"  src="{$params.IMAGE_DIR}flash.gif" />
                    </span>
                    <span ng-if="content.type_img !== 'swf'" class="thumbnail">
                      <dynamic-image instance="{$smarty.const.INSTANCE_MEDIA}" path="[% content.path_file + '/' + content.name %]" width="80" transform="zoomcrop,80,80,center,center" class="image-preview"></dynamic-image>
                    </span>
                  </div>

                  <div class="description">
                    <span ng-if="content.description != ''">[% content.description %]</span>
                    <span ng-if="content.description == ''">{t}No available description{/t}</span>
                  </div>

                  <div class="visible-xs visible-sm">
                    [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
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
                    </div>
                  </div>
                </td>
                <td class="left nowrap hidden-xs hidden-sm">
                  [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
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
          <pagination class="no-margin" max-size="3" direction-links="true" ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total" num-pages="pagination.pages"></pagination>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="modal-delete">
      {include file="common/modals/_modalDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
      {include file="common/modals/_modalBatchDelete.tpl"}
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
</form>
{/block}
