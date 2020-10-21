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
    <div class="thumbnail-placeholder" {if isset($photo1) && $photo1->name}ng-init="photo1 = {json_encode($photo1)|clear_json};loaded=true"{/if}>
      <div class="img-thumbnail" ng-if="!photo1 || !loaded">
        <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1">
          <i class="fa fa-picture-o fa-2x"></i>
          <h5>Pick an image</h5>
        </div>
      </div>
      <div class="dynamic-image-placeholder" ng-if="photo1 && loaded">
        <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo1" flash="" transform="thumbnail,220,220">
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
      <div class="image_title">[% photo1.name %]</div>
        <div class="info">
          <div class="image_size">[% photo1.width %] x [% photo1.height %]</div>
          <div class="file_size">[% photo1.size %] Kb</div>
          <div class="created_time">[% photo1.created %]</div>
          <div ng-if="photo1.type_img ==='swf'">
            <h5>
              <i class="fa fa-warning"></i>
              {t}Flash based{/t}
            </h5>
            <div class="checkbox">
              <input id="overlap" name="overlap" type="checkbox" value="1" {if isset($advertisement->overlap) && $advertisement->overlap == 1}checked="checked"{/if} />
              <label for="overlap" class="overlap-message">
                  {t}Override default click handler{/t} <i class="fa fa-question-circle" title="{t}When you click in some Flash-based advertisements they redirect you to another web site. If you want to overlap that address with that specified by you above you should mark this.{/t}"> </i>
              </label>
            </div>
          </div>
        </div>
    </div>
    <div class="article-resource-footer">
      <input name="path" type="hidden" ng-value="photo1.id"/>
    </div>
  </div>
</div>
