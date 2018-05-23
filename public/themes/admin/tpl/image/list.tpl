{extends file="base/admin.tpl"}

{block name="content"}
<div ng-app="BackendApp" ng-controller="ImageListCtrl" ng-init="setMode('grid');init('photo', 'backend_ws_image_list'); years = {json_encode($years)|clear_json}">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-picture-o"></i>
              <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/221735-opennemas-c%C3%B3mo-subir-im%C3%A1genes-para-mis-art%C3%ADculos" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question"></i>
              </a>
              {t}Images{/t}
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            {acl isAllowed="PHOTO_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-primary" media-picker media-picker-mode="explore,upload" media-picker-mode-active="upload" media-picker-type="photo" id="upload-button">
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
          {acl isAllowed="PHOTO_DELETE"}
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
            <input class="no-boarder" name="title" ng-model="criteria.title" placeholder="{t}Search by description or metadata{/t}" type="text"/>
            <input type="hidden" name="in_home" ng-model="criteria.in_home">
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs ng-cloak">
            <select name="month" ng-model="criteria.month">
              <option value="">{t}All months{/t}</option>
              <optgroup label="[% year.name %]" ng-repeat="year in years">
                <option value="[% month.value %]" ng-repeat="month in year.months">
                  [% month.name %] ([% year.name %])
                </option>
              </optgroup>
            </select>
          </li>
          <li class="quicklinks hidden-xs ng-cloak" ng-if="mode === 'list'">
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
        <ul class="nav quick-section pull-right ng-cloak">
          <li class="quicklinks hidden-xs" ng-if="mode === 'list' && contents.length > 0">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content clearfix" ng-class="{ 'content-grid': mode === 'grid' }">
    <div class="grid simple ng-cloak" ng-if="mode === 'list'">
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
                <th class="hidden-xs text-center" width="150">{t}Resolution{/t}</th>
                <th class="hidden-xs text-center" width="150">{t}Size{/t}</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-if="contents.length == 0">
                <td class="empty" colspan="10">
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
                  <div class="dynamic-image-placeholder" ng-click="open('modal-image', content)" style="height: 120px; width: 120px; margin-bottom: 15px;">
                    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="content" only-image="true" transform="zoomcrop,220,220"></dynamic-image>
                  </div>
                </td>
                <td>
                  <div ng-click="open('modal-image', content)" class="visible-xs center" style="height: 200px; width: 200px; margin: 0 auto 15px;">
                    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="content" only-image="true" transform="zoomcrop,220,220"></dynamic-image>
                  </div>
                  <div class="description">
                    <span ng-if="content.description != ''">[% content.description %]</span>
                    <span ng-if="content.description == ''">{t}No available description{/t}</span>
                  </div>
                  <div class="small-text">
                    <span ng-if="content.metadata != ''"><i class="fa fa-tags"></i> [% content.metadata %]</span>
                    <span ng-if="content.metadata == ''"><i class="fa fa-tags"></i> {t}No tags{/t}</span>
                  </div>
                  <div class="small-text">
                    <strong>{t}Created{/t}:</strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                  </div>
                  <div class="small-text">
                    <strong>{t}Resolution{/t}:</strong>
                    <span>[% content.width %]x[% content.height %]</span>
                  </div>
                  <div class="small-text">
                    <strong>{t}Size{/t}:</strong>
                    <span>[% content.size %] KB</span>
                  </div>
                  <div>
                    <div class="listing-inline-actions">
                      {acl isAllowed="PHOTO_UPDATE"}
                      <a class="link" href="[% edit(content.id, 'admin_photo_show') %]">
                        <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                      </a>
                      {/acl}
                      {acl isAllowed="PHOTO_CREATE"}
                      <a class="btn btn-link" ng-click="launchPhotoEditor(content)" type="button">
                          <i class="fa fa-sliders m-r-5"></i>{t}Enhance{/t}
                      </a>
                      {/acl}
                      {acl isAllowed="PHOTO_DELETE"}
                      <button class="del link link-danger" ng-click="sendToTrash(content)" type="button">
                        <i class="fa fa-trash-o m-r-5"></i>{t}Remove{/t}
                      </button>
                      {/acl}
                      <a class="link" href="{$smarty.const.INSTANCE_MAIN_DOMAIN}{$MEDIA_IMG_URL}[% content.path_file + '/' + content.name %]">
                        <i class="fa fa-external-link m-r-5"></i>{t}Link{/t}
                      </a>
                    </div>
                  </div>
                </td>
                <td class="hidden-xs text-center">[% content.width %]x[% content.height %]</td>
                <td class="hidden-xs text-center">[% content.size %] KB</td>
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
    <div class="content-wrapper">
      <div class="ng-cloak spinner-wrapper" ng-if="(!mode || mode === 'grid') && loading && contents.length < total">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
      <div class="clearfix row ng-cloak" ng-if="!mode || mode === 'grid'">
        <div class="listing-no-contents ng-cloak" ng-if="!loading && !loadingMore && contents.length == 0">
          <div class="center">
            <h4>{t}Unable to find any image that matches your search.{/t}</h4>
            <h6>{t}Maybe changing any filter could help or add one using the "Upload" button above.{/t}</h6>
          </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-15 infinite-col media-item selectable" ng-class="{ 'selected': isSelected(content.id) }" ng-repeat="content in contents">
          <div class="dynamic-image-placeholder no-margin" ng-click="select(content);xsOnly($event, toggle, content)">
            <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="content" only-image="true" transform="zoomcrop,400,400">
              <div class="hidden-select" ng-click="toggle(content)"></div>
              <div class="thumbnail-actions ng-cloak">
                {acl isAllowed="PHOTO_DELETE"}
                  <div class="thumbnail-action remove-action" ng-click="sendToTrash(content);$event.stopPropagation()">
                    <i class="fa fa-trash-o fa-2x"></i>
                  </div>
                {/acl}
                {acl isAllowed="PHOTO_UPDATE"}
                  <a class="thumbnail-action" href="[% edit(content.id, 'admin_photo_show') %]" ng-click="$event.stopPropagation()">
                    <i class="fa fa-pencil fa-2x"></i>
                  </a>
                  <a class="thumbnail-action" ng-click="launchPhotoEditor(content)" >
                    <i class="fa fa-sliders fa-2x"></i>
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
      <h4 class="ng-cloak" ng-show="selected.lastSelected">{t}Image details{/t}</h4>
      <div ng-if="selected.lastSelected">
        <div class="pointer thumbnail-wrapper" ng-click="open('modal-image', selected.lastSelected)" ng-if="selected.lastSelected.content_type_name == 'photo' && !isFlash(selected.lastSelected)">
          <dynamic-image autoscale="true" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="selected.lastSelected" only-image="true" transform="thumbnail,220,220"></dynamic-image>
        </div>
        <div class="pointer thumbnail-wrapper" ng-click="open('modal-image', selected.lastSelected)" ng-if="selected.lastSelected.content_type_name == 'video' && !selected.lastSelected.thumb_image">
          <dynamic-image autoscale="true" ng-model="selected.lastSelected" only-image="true" property="thumb"></dynamic-image>
        </div>
        <div class="pointer thumbnail-wrapper" ng-click="open('modal-image', selected.lastSelected)" ng-if="isFlash(selected.lastSelected)">
          <dynamic-image autoscale="true" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="selected.lastSelected" only-image="true"></dynamic-image>
        </div>
        <ul class="media-information">
          <li>
            <strong>[% selected.lastSelected.name %]</strong>
          </li>
          <li>
            <a class="btn btn-primary ng-isolate-scope" ng-href="[% routing.generate('admin_photo_show', { id: selected.lastSelected.id}) %]">
                <i class="fa fa-edit ng-isolate-scope"></i>
                {t}Edit{/t}
            </a>
          </li>
          <li>
            <a class="btn btn-primary ng-isolate-scope" ng-click="launchPhotoEditor(selected.lastSelected)">
                <i class="fa fa-sliders"></i>
                {t}Enhance{/t}
            </a>
          </li>
          <li>[% selected.lastSelected.created | moment %]</li>
          <li><strong>{t}Size:{/t}</strong> [% selected.lastSelected.width %] x [% selected.lastSelected.height %] ([% selected.lastSelected.size %] KB)</li>
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
            <swf-object swf-params="{ wmode: 'opaque' }" swf-url="{$MEDIA_IMG_URL}[% template.selected.path_file %][% template.selected.name %]" swf-width="570"></swf-object>
          </span>
          <span ng-if="template.selected.type_img !== 'swf'">
            <img class="img-responsive" ng-src="{$MEDIA_IMG_URL}[% template.selected.path_file + template.selected.name %]"/>
          </span>
        </div>
      </div>
    </script>
  </div>
{/block}
