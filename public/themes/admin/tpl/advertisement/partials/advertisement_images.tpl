<div class="row thumbnail-wrapper ng-cloak">
  <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.photo1 }"></div>
  <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.photo1 }">
    <p>{t}Are you sure?{/t}</p>
    <div class="confirm-actions">
      <button class="btn btn-link" ng-click="toggleOverlay('photo1')" type="button">
        <i class="fa fa-times fa-lg"></i>
        {t}No{/t}
      </button>
      <button class="btn btn-link" ng-click="removeImage('photo1');toggleOverlay('photo1')" type="button">
        <i class="fa fa-check fa-lg"></i>
        {t}Yes{/t}
      </button>
    </div>
  </div>
  <div class="col-md-6">
    <div class="thumbnail-placeholder" {if isset($photo1)}ng-init="photo1 = {json_encode($photo1)|clear_json};loaded=true"{/if}>
      <div class="img-thumbnail" ng-if="!photo1 || !loaded">
        <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1">
          <i class="fa fa-picture-o fa-2x"></i>
          <h5>Pick an image</h5>
        </div>
      </div>
      <div class="dynamic-image-placeholder" ng-if="photo1 && loaded">
        <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo1" transform="thumbnail,220,220">
          <div class="thumbnail-actions">
            <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo1')">
              <i class="fa fa-trash-o fa-2x"></i>
            </div>
            <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1">
              <i class="fa fa-camera fa-2x"></i>
            </div>
          </div>
          <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1" media-picker-type="photo"></div>
        </dynamic-image>
      </div>
    </div>
  </div>
  <div class="col-md-6" ng-if="photo1">
    <div class="image-information">
      <div class="image_title"> [% photo1.path %]</div>
        <div class="info">
          <div class="image_size">[% photo1.width %] x [% photo1.height %]</div>
          <div class="file_size">[% photo1.size %] Kb</div>
          <div class="created_time">[% photo1.created %]</div>
        </div>
    </div>
    <div class="article-resource-footer">
      <input name="path" type="hidden" ng-value="photo1.pk_content"/>
    </div>
  </div>
</div>
