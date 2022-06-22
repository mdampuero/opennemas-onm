  <div class="grid simple" ng-show="item.live_blog_posting">
    <div class="grid-body">
      <div class="row">
        <div class="col-md-8 col-sm-12">
          <h2>
            {t}Live Blog Posting{/t}
          </h2>
        </div>
        <div class="col-md-4 col-sm-12">
          <button class="btn btn-block btn-success btn-loading" ng-click="addBlankUpdate()" type="button" ng-disabled="!canAddUpdate">
            <h5 class="text-uppercase text-white">
              <i class="fa fa-plus"></i>
              {t}Add update{/t}
            </h5>
          </button>
        </div>
      </div>
    </div>
    <div class="grid-body" ng-repeat="updateItem in item.live_blog_updates track by $index" ng-cloak>
        <div class="form-group">
          <div class="row">
            <div class="col-md-8 col-sm-12">
              <label for="title-[% $index %]" class="form-label">
                {t}Title{/t}
              </label>
              <div class="controls">
              </div>
              <input class="form-control" id="title-[% $index %]" name="updateItem.title" ng-class="{ 'input-faded': flags.block.updateItem.title }" ng-model="updateItem.title" type="text">
            </div>
            <div class="col-md-3 col-sm-12">
              <label class="form-label" for="modified-[% $index %]">
                {t}Modified Date{/t}
              </label>
              <div class="controls">
                <div class="input-group">
                  <input class="form-control" datetime-picker datetime-picker-timezone="{$app.locale->getTimeZone()->getName()}" datetime-picker-use-current=true datetime-picker-min="updateItem.created" id="modified-[% $index %]" name="updateItem.modified" ng-model="updateItem.modified" type="datetime">
                  <span class="input-group-addon add-on">
                    <span class="fa fa-calendar"></span>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-1 col-sm-12 update-collapse-icon-container">
              <div class="grid-collapse pointer" ng-click="expanded.live_blog_update[$index] = !expanded.live_blog_update[$index]">
                <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.live_blog_update[$index] }"></i>
              </div>
            </div>
          </div>
        </div>
      <div class="grid-collapse-body no-padding ng-cloak" ng-class="{ 'expanded': expanded.live_blog_update[$index]}">
        <div class="form-group">
          <div class="row">
            <div class="thumbnail-wrapper update-flex-container">
              <div class="col-md-6 col-sm-12">
                  <label class="form-label" for="update-image[% $index %]">
                    {t}Featured image{/t}
                  </label>
                <div class="overlay photo-overlay ng-cloak"  ng-class="{ 'open': overlay['photo_'+ updateItem.created]}"></div>
                <div class="confirm-dialog ng-cloak"  ng-class="{ 'open': overlay['photo_'+ updateItem.created]}">
                  <p>{t}Are you sure?{/t}</p>
                  <div class="confirm-actions">
                    <button class="btn btn-link" ng-click="toggleOverlay('photo_'+ updateItem.created)" type="button">
                      <i class="fa fa-times fa-lg"></i>
                      {t}No{/t}
                    </button>
                    <button class="btn btn-link" ng-click="removeItem('item.live_blog_updates.' + $index + '.image_id');toggleOverlay('photo_'+ updateItem.created)" type="button">
                      <i class="fa fa-check fa-lg"></i>
                      {t}Yes{/t}
                    </button>
                  </div>
                </div>
                <div class="thumbnail-placeholder" id="update-image[% $index %]">
                  <div class="img-thumbnail" ng-show="!updateItem.image_id">
                    <div class="thumbnail-empty" ng-cloak media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-dynamic-target="item.live_blog_updates.[% $index %].image_id" media-picker-types="photo" photo-editor-enabled="true">
                      <i class="fa fa-picture-o fa-2x"></i>
                      <h5>{t}Select an element{/t}</h5>
                    </div>
                  </div>
                  <div class="dynamic-image-placeholder" ng-show="updateItem.image_id">
                    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="updateItem.image_id" reescale="true">
                      <div class="thumbnail-actions">
                        <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo_'+ updateItem.created)">
                          <i class="fa fa-trash-o fa-2x"></i>
                        </div>
                        <div class="thumbnail-action" ng-cloak media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-dynamic-target="item.live_blog_updates.[% $index %].image_id" media-picker-types="photo" photo-editor-enabled="true">
                          <i class="fa fa-camera fa-2x"></i>
                        </div>
                      </div>
                    </dynamic-image>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12 update-caption-container">
                <input name="caption_$index" ng-model="data.updateItem" type="hidden">
                <div class="ng-cloak m-t-15" ng-show="updateItem.image_id">
                  <label class="form-label" for="caption-[%$index%]">
                    {t}Caption{/t}
                  </label>
                  <div class="controls">
                    <input type="text" class="form-control" id="caption-[%$index%]" ng-model="updateItem.caption"></input>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label clearfix" for="live_blog_updates.[% $index %].body">
            <div class="pull-left">{t}Body{/t}</div>
          </label>
          {acl isAllowed='PHOTO_ADMIN'}
            <div class="pull-right">
              <div class="btn btn-mini" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-dynamic-target="editor.live_blog_updates.[% $index %].body" photo-editor-enabled="true">
                <i class="fa fa-plus"></i>
                {t}Insert image{/t}
              </div>
            </div>
          {/acl}
            <div class="pull-right m-r-5">
              <div class="btn btn-mini" content-picker content-picker-dynamic-target="editor.live_blog_updates.[% $index %].body" content-picker-selection="true" content-picker-type="album,article,attachment,opinion,poll,video" content-picker-max-size="10">
                <i class="fa fa-plus"></i>
                {t}Insert related{/t}
              </div>
            </div>
          <div class="controls">
            <textarea name="live_blog_updates.[% $index %].body" id="live_blog_updates.[% $index %].body" incomplete="incomplete" ng-model="updateItem.body" onm-editor onm-editor-preset="standard" class="form-control" rows="15"></textarea>
          </div>
        </div>
        <div class="row article-liveblogupdate-actions">
          <div class="col-md-2 col-md-offset-10">
            <button class="btn btn-danger btn-small" ng-click="removeUpdate($index)" type="button"> <i class="fa fa-trash-o m-r-5"></i>
            {t}Remove update{/t}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
