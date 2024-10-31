<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.social = !expanded.social">
  <i class="fa fa-list m-r-10"></i>{t}Options for Social Networks{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.social }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded' : expanded.social }">
  <div class="form-group no-margin">
      {include file="ui/component/input/text.tpl" iField="social_title" iRequired=false iTitle="{t}Title for Social Media Preview{/t}" iValidation=false iHelp="{t}Title shown when shared on social media.{/t}"}
      {include file="ui/component/input/text.tpl" iField="social_description" iRequired=false iTitle="{t}Description for Social Media Preview{/t}" iValidation=false iHelp="{t}Description shown when shared on social media.{/t}" }
      <div class="thumbnail-wrapper">
          <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.relatedSocial }"></div>
          <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.relatedSocial }">
            <p>{t}Are you sure?{/t}</p>
            <div class="confirm-actions">
              <button class="btn btn-link" ng-click="toggleOverlay('relatedSocial')" type="button">
                <i class="fa fa-times fa-lg"></i>
                {t}No{/t}
              </button>
              <button class="btn btn-link" ng-click="removeItem('data.relatedSocial');removeItem('relatedSocial');toggleOverlay('relatedSocial')" type="button">
                <i class="fa fa-check fa-lg"></i>
                {t}Yes{/t}
              </button>
            </div>
          </div>
          <div class="thumbnail-placeholder">
            <div class="img-thumbnail" ng-show="!relatedSocial">
              <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-ignore="[% related.getIds('relatedSocial') %]" media-picker-selection="true" media-picker-max-size="1" media-picker-target="target.relatedSocial" media-picker-types="{$types}" photo-editor-enabled="true">
                <i class="fa fa-picture-o fa-2x"></i>
                <h5>{t}Select an element{/t}</h5>
              </div>
            </div>
            <div class="dynamic-image-placeholder" ng-show="relatedSocial">
              <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="data.extra.related_contents[relatedSocial.target_id]">
                <div class="thumbnail-actions">
                  <div class="thumbnail-action remove-action" ng-click="toggleOverlay('relatedSocial')">
                    <i class="fa fa-trash-o fa-2x"></i>
                  </div>
                  <div class="thumbnail-action" media-picker media-picker-ignore="[% related.getIds('relatedSocial') %]" media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="target.relatedSocial" media-picker-types="{$types}" photo-editor-enabled="true">
                    <i class="fa fa-camera fa-2x"></i>
                  </div>
                </div>
              </dynamic-image>
            </div>
          </div>
          <input name="relatedSocial" ng-model="data.relatedSocial" type="hidden">
          <div class="form-group ng-cloak m-t-15" ng-show="relatedSocial">
            <label class="form-label" for="relatedSocial">
              {t}Caption{/t}
            </label>
            <div class="controls">
              <textarea class="form-control" id="relatedSocial" ng-model="relatedSocial.caption"></textarea>
            </div>
          </div>
        </div>
  </div>
</div>
