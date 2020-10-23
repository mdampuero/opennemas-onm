<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.{$field} = !expanded.{$field}">
  <i class="fa fa-image m-r-10"></i> {$title}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.{$field} }"></i>
  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.{$field} && {$field}" ng-class="{ 'badge-danger' : item.{$field} == 0 }">
    <span ng-show="{$field}">
      <i class="fa fa-image"></i>
    </span>
  </span>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.{$field} }">
  <div class="thumbnail-wrapper">
    <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.{$field} }"></div>
    <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.{$field} }">
      <p>{t}Are you sure?{/t}</p>
      <div class="confirm-actions">
        <button class="btn btn-link" ng-click="toggleOverlay('{$field}')" type="button">
          <i class="fa fa-times fa-lg"></i>
          {t}No{/t}
        </button>
        <button class="btn btn-link" ng-click="removeImage('{$field}');toggleOverlay('{$field}')" type="button">
          <i class="fa fa-check fa-lg"></i>
          {t}Yes{/t}
        </button>
      </div>
    </div>
    <div class="thumbnail-placeholder">
      <div class="img-thumbnail" ng-show="!{$field}">
        <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="{$field}">
          <i class="fa fa-picture-o fa-2x"></i>
          <h5>{t}Pick an image{/t}</h5>
        </div>
      </div>
      <div class="dynamic-image-placeholder" ng-show="{$field}">
        <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="{$field}">
          <div class="thumbnail-actions">
            <div class="thumbnail-action remove-action" ng-click="toggleOverlay('{$field}')">
              <i class="fa fa-trash-o fa-2x"></i>
            </div>
            <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="{$field}" media-picker-types="photo">
              <i class="fa fa-camera fa-2x"></i>
            </div>
          </div>
        </dynamic-image>
      </div>
    </div>
    {if $footer}
      <div class="form-group ng-cloak m-t-15" ng-show="{$field}">
        <label class="form-label" for="{$footer}">
          {t}Caption{/t}
        </label>
        <div class="controls">
          <textarea class="form-control" name="{$footer}" ng-model="{$footer}"></textarea>
        </div>
      </div>
    {/if}
  </div>
</div>
