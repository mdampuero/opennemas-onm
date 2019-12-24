{extends file="common/extension/list.grid.tpl"}

{block name="begin-wrapper"}
  <div class="content-wrapper">
    <div class="clearfix row ng-scope">
{/block}

{block name="item"}
  <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item" transform="zoomcrop,400,400">
    <div class="hidden-select" ng-click="select(item);toggleItem(item); xsOnly($event, toggle, item)"></div>
    <div class="thumbnail-actions thumbnail-actions-3x ng-cloak">
      {acl isAllowed="PHOTO_UPDATE"}
        <a class="thumbnail-action" href="[% routing.generate('backend_photo_show', { id: getItemId(item) }) %]" uib-tooltip="{t}Edit{/t}">
          <i class="fa fa-pencil fa-2x text-default"></i>
        </a>
      {/acl}
      {acl isAllowed="PHOTO_DELETE"}
        <div class="thumbnail-action" ng-click="delete(item.pk_photo)" uib-tooltip="{t}Delete{/t}" tooltip-class="tooltip-danger">
          <i class="fa fa-trash-o fa-2x text-danger"></i>
        </div>
      {/acl}
      {acl isAllowed="PHOTO_ENHANCE"}
        <a class="thumbnail-action" ng-click="launchPhotoEditor(item)" uib-tooltip="{t}Enhance{/t}" tooltip-class="tooltip-info">
          <i class="fa fa-sliders fa-2x text-info"></i>
        </a>
      {/acl}
    </div>
  </dynamic-image>
{/block}

{block name="end-clearfix"}</div>{/block}
{block name="scroll"}
<div class="p-t-15 p-b-15 pointer text-center ng-scope" ng-click="scroll('backend_ws_contents_list')" ng-if="!flags.http.loading && mode == 'grid' && data.total != items.length"> 
  <h5> 
    <span ng-if="!flags.http.loadingMore" class="ng-scope">{t}Load more{/t}</span>
  </h5> 
</div>
{/block}

{block name="sidebar"}
  <div class="content-sidebar hidden-sm ng-cloak" ng-if="app.mode === 'grid'">
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
          <a class="btn btn-primary ng-isolate-scope" ng-href="[% routing.generate('backend_photo_show', { id: selected.lastSelected.id}) %]">
              <i class="fa fa-edit ng-isolate-scope"></i>
              {t}Edit{/t}
          </a>
        </li>
        {is_module_activated name="es.openhost.module.imageEditor"}
        <li>
          <a class="btn btn-primary ng-isolate-scope" ng-click="launchPhotoEditor(selected.lastSelected)">
              <i class="fa fa-sliders"></i>
              {t}Enhance{/t}
          </a>
        </li>
        {/is_module_activated}
        <li>[% selected.lastSelected.created %]</li>
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
{/block}

{block name="end-wrapper"}
  </div>
{/block}
