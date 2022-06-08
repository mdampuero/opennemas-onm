  <div class="grid simple" ng-show="item.live_blog_posting">
    <div class="grid-body">
      <div class="row">
        <div class="col-md-4 p-b-15 p-t-15 col-md-offset-4">
          <button class="btn btn-block btn-default btn-loading" ng-click="addBlankUpdate()" type="button" ng-disabled="!canAddUpdate">
            <h5 class="text-uppercase">
              <i class="fa fa-plus"></i>
              {t}Add{/t}
            </h5>
          </button>
        </div>
      </div>
      <div ng-repeat="updateItem in item.live_blog_updates track by $index" ng-cloak>
        <div class="article-liveblogupdate-actions m-t-50">
          <button class="btn btn-danger btn-small" ng-click="removeUpdate($index)" type="button"> <i class="fa fa-trash-o m-r-5"></i>{t}Remove update{/t}</button>
        </div>
        <hr/>
        <div class="form-group">
          <div class="row">
            <div class="col-md-4">
              <div class="thumbnail-wrapper">
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
              <div class="thumbnail-placeholder">
                <div class="img-thumbnail" ng-show="!updateItem.image_id">
                  <div class="thumbnail-empty" ng-cloak media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-dynamic-target="item.live_blog_updates.[% $index %].image_id" media-picker-types="photo" photo-editor-enabled="true">
                    <i class="fa fa-picture-o fa-2x"></i>
                    <h5>{t}Select an element{/t}</h5>
                  </div>
                </div>
                <div class="dynamic-image-placeholder" ng-show="updateItem.image_id">
                  <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="updateItem.image_id" autoscale="true">
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
                <input name="caption_$index" ng-model="data.updateItem" type="hidden">
                <div class="form-group ng-cloak m-t-15" ng-show="updateItem.image_id">
                  <label class="form-label" for="caption-[%$index%]">
                    {t}Caption{/t}
                  </label>
                  <div class="controls">
                    <textarea class="form-control" id="caption-[%$index%]" ng-model="updateItem.caption"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="row">
            <div class="col-md-6">
              <label for="title-[%$index%]" class="form-label">
                {t}Title{/t}
              </label>
              <div class="controls">
              </div>
              <input class="form-control" id="title-[%$index%]" name="updateItem.title" ng-class="{ 'input-faded': flags.block.updateItem.title }" ng-model="updateItem.title" type="text">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="modified-[%$index%]">
                {t}Modified Date{/t}
              </label>
              <div class="controls">
                <div class="input-group">
                  <input class="form-control" datetime-picker datetime-picker-timezone="{$app.locale->getTimeZone()->getName()}" datetime-picker-use-current=true datetime-picker-min="updateItem.created" id="modified-[%$index%]" name="updateItem.modified" ng-model="updateItem.modified" type="datetime">
                  <span class="input-group-addon add-on">
                    <span class="fa fa-calendar"></span>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label clearfix" for="body-[%$index%]">
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
            <textarea name="live_blog_updates.[% $index %].body" id="body-[%$index%]" incomplete="incomplete" ng-model="updateItem.body" onm-editor onm-editor-preset="standard" class="form-control" rows="15"></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
