{extends file="base/admin.tpl"}

{block name="content"}
<form name="form" ng-controller="AlbumCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-stack-overflow m-r-10"></i>
              <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/745938-opennemas-c%C3%B3mo-crear-%C3%A1lbumes-galer%C3%ADas-de-imagene" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question fa-lg"></i>
              </a>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="{url name=backend_albums_list}">
                {t}Albums{/t}
              </a>
            </h4>
          </li>
          <li class="quicklinks hidden-xs m-l-5 m-r-5">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks visible-xs">
            <a class="help-icon" href="http://help.opennemas.com/knowledgebase/articles/745938-opennemas-c%C3%B3mo-crear-%C3%A1lbumes-galer%C3%ADas-de-imagene" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
              <i class="fa fa-question fa-lg"></i>
            </a>
          </li>
          <li class="quicklinks hidden-xs">
            <h4>{if empty($id)}{t}Create{/t}{else}{t}Edit{/t}{/if}</h4>
          </li>
        <li class="quicklinks m-l-5 m-r-5 ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
          <h4>
            <i class="fa fa-angle-right"></i>
          </h4>
        </li>
        <li class="quicklinks ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
          <translator keys="data.extra.keys" ng-model="config.locale.selected" options="data.extra.locale"></translator>
        </li>
        </ul>
        <div class="pull-right">
          <ul class="quick-section">
            <li class="quicklinks">
              <button class="btn btn-loading btn-success text-uppercase" ng-click="submit($event)" data-text="{t}Saving{/t}..." type="submit">
                <i class="fa fa-save m-r-5"></i>
                {t}Save{/t}
              </button>
            </li>
          </ul>
        </div>
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
    <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && item === null">
      <div class="text-center p-b-15 p-t-15">
        <a href="[% routing.generate('backend_videos_list') %]">
          <i class="fa fa-4x fa-warning text-warning"></i>
          <h3>{t}Unable to find the item{/t}</h3>
          <h4>{t}Click here to return to the list{/t}</h4>
        </a>
      </div>
    </div>
    <div class="row ng-cloak" ng-show="!flags.http.loading && item">
      <div class="col-md-4 col-md-push-8">
        <div class="grid simple">
          <div class="grid-body no-padding">
            <div class="grid-collapse-title">
              {acl isAllowed="VIDEO_AVAILABLE"}
                {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Published{/t}" field="content_status"}
              {/acl}
              {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
            </div>

            {include file="ui/component/content-editor/accordion/author.tpl"}
            {include file="ui/component/content-editor/accordion/category.tpl" field="item.category"}
            {include file="ui/component/content-editor/accordion/tags.tpl"}
            {include file="ui/component/content-editor/accordion/slug.tpl" route="[% getFrontendUrl(item) %]"}
            {include file="ui/component/content-editor/accordion/scheduling.tpl"}

          </div>
        </div>

        <div class="grid simple">
          <div class="grid-body no-padding">
            <div class="grid-collapse-title">
              <i class="fa fa-cog m-r-10"></i> {t}Parameters{/t}
            </div>

            {include file="ui/component/content-editor/accordion/image.tpl" title="{t}Cover image{/t}" field="cover"}
            {include file="ui/component/content-editor/accordion/input-text.tpl" title="{t}Agency{/t}" field="agency"}

            {* <div class="grid-collapse-body expanded">
              <div class="form-group">
                <label class="form-label" for="agency">
                  {t}Agency{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="agency" name="agency" type="text" value="{$album->agency|clearslash|escape:"html"}"/>
                </div>
              </div>
            </div> *}
          </div>
        </div>
      </div>
      <div class="col-md-8 col-md-pull-4">
        <div class="grid simple">
          <div class="grid-body">
            {include file="ui/component/content-editor/input-text.tpl" title="{t}Title{/t}" field="title" required=true counter=true}
            {include file="ui/component/content-editor/textarea.tpl" title="{t}Summary{/t}" field="description" rows=5 imagepicker=true}
          </div>
        </div>
      </div>
    </div>
    <div class="row ng-cloak" ng-show="!flags.http.loading && item">
      <div class="col-md-8" {if !empty($photos)}ng-init="parsePhotos({json_encode($photos)|clear_json})"{/if}>
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Album images{/t}</h4>
          </div>
          <div class="grid-body no-padding">
            <div ui-sortable="{ axis: 'x,y', placeholder: 'album-thumbnail-sortable' }" ng-model="photos">
              <div class="album-thumbnail-sortable" ng-repeat="photo in photos">
                <input type="hidden" name="album_photos_id[]" ng-value="photo.id"/>
                <input type="hidden" name="album_photos_footer[]" ng-value="photo.footer"/>
                <div class="thumbnail-wrapper">
                  <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay['photo_'+ $index] }"></div>
                  <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay['photo_'+ $index] }">
                    <p>{t}Are you sure?{/t}</p>
                    <div class="confirm-actions">
                      <button class="btn btn-link" ng-click="toggleOverlay('photo_'+ $index)" type="button">
                        <i class="fa fa-times fa-lg"></i>
                        {t}No{/t}
                      </button>
                      <button class="btn btn-link" ng-click="removeItem('photos', $index);toggleOverlay('photo_'+ $index)" type="button">
                        <i class="fa fa-check fa-lg"></i>
                        {t}Yes{/t}
                      </button>
                    </div>
                  </div>
                  <div class="dynamic-image-placeholder">
                    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo" transform="zoomcrop,500,500">
                      <div class="thumbnail-actions">
                        <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo_'+ $index)">
                          <i class="fa fa-trash-o fa-2x"></i>
                        </div>
                      </div>
                    </dynamic-image>
                  </div>
                  <div class="form-group">
                    <textarea class="album-thumbnail-description form-control" ng-model="photo.footer"></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="album-thumbnail-placeholder">
              <div class="img-thumbnail">
                <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="150" media-picker-target="photos">
                  <i class="fa fa-plus fa-3x"></i>
                  <h4>{t}Add images{/t}<h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4" {if isset($album->cover_image) && $album->cover_image->id}ng-init="cover = {json_encode($album->cover_image)|clear_json}"{/if}>
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Cover image{/t}</h4>
          </div>
          <div class="grid-body">
            <div class="thumbnail-wrapper">
              <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.cover }"></div>
              <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.cover }">
                <p>Are you sure?</p>
                <div class="confirm-actions">
                  <button class="btn btn-link" ng-click="toggleOverlay('cover')" type="button">
                    <i class="fa fa-times fa-lg"></i>
                    {t}No{/t}
                  </button>
                  <button class="btn btn-link" ng-click="removeImage('cover');toggleOverlay('cover')" type="button">
                    <i class="fa fa-check fa-lg"></i>
                    {t}Yes{/t}
                  </button>
                </div>
              </div>
              <div class="thumbnail-placeholder">
                <div class="img-thumbnail" ng-if="!cover">
                  <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="cover">
                    <i class="fa fa-picture-o fa-2x"></i>
                    <h5>Pick an image</h5>
                  </div>
                </div>
                <div class="dynamic-image-placeholder" ng-if="cover">
                  <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="cover">
                    <div class="thumbnail-actions">
                      <div class="thumbnail-action remove-action" ng-click="toggleOverlay('cover')">
                        <i class="fa fa-trash-o fa-2x"></i>
                      </div>
                      <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="cover" media-picker-types="photo">
                        <i class="fa fa-camera fa-2x"></i>
                      </div>
                    </div>
                  </dynamic-image>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="modal-edit-album-error">
    {include file="album/modals/_edit_album_error.tpl"}
  </script>
</form>
{/block}
