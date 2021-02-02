<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.{$iName} = !expanded.{$iName}">
  <i class="fa fa-image m-r-10"></i>
  {$iTitle}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.{$iName} }"></i>
  <span class="pull-right" ng-if="!expanded.{$iName}">
    {include file="common/component/icon/status.tpl" iForm="form.$iName" iNgModel=$iName iValidation=true}
  </span>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.{$iName} }">
  <div class="thumbnail-wrapper">
    <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.{$iName} }"></div>
    <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.{$iName} }">
      <p>{t}Are you sure?{/t}</p>
      <div class="confirm-actions">
        <button class="btn btn-link" ng-click="toggleOverlay('{$iName}')" type="button">
          <i class="fa fa-times fa-lg"></i>
          {t}No{/t}
        </button>
        <button class="btn btn-link" ng-click="removeItem('data.{$iName}');removeItem('{$iName}');toggleOverlay('{$iName}')" type="button">
          <i class="fa fa-check fa-lg"></i>
          {t}Yes{/t}
        </button>
      </div>
    </div>
    <div class="thumbnail-placeholder">
      <div class="img-thumbnail" ng-show="!{$iName}">
        <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-ignore="[% related.getIds('featuredFrontpage') %]" media-picker-selection="true" media-picker-max-size="1" media-picker-target="target.{$iName}">
          <i class="fa fa-picture-o fa-2x"></i>
          <h5>{t}Pick an image{/t}</h5>
        </div>
      </div>
      <div class="dynamic-image-placeholder" ng-show="{$iName}">
        <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="data.extra.related_contents[{$iName}.target_id]">
          <div class="thumbnail-actions">
            <div class="thumbnail-action remove-action" ng-click="toggleOverlay('{$iName}')">
              <i class="fa fa-trash-o fa-2x"></i>
            </div>
            <div class="thumbnail-action" media-picker media-picker-ignore="[% related.getIds('featuredFrontpage') %]" media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="target.{$iName}" media-picker-types="photo">
              <i class="fa fa-camera fa-2x"></i>
            </div>
          </div>
        </dynamic-image>
      </div>
    </div>
    <input name="{$iName}" ng-model="data.{$iName}" {if $iRequired}required{/if} type="hidden">
    <div class="form-group ng-cloak m-t-15" ng-show="{$iName}">
      <label class="form-label" for="{$iName}">
        {t}Caption{/t}
      </label>
      <div class="controls">
        <textarea class="form-control" id="{$iName}" ng-model="{$iName}.caption"></textarea>
      </div>
    </div>
  </div>
</div>
