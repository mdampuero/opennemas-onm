  <div class="grid simple" ng-show="item.live_blog_posting">
    <div class="grid-body">
      <div class="row">
        <div class="col-md-4 p-b-15 p-t-15 col-md-offset-4">
          <button class="btn btn-block btn-default btn-loading" ng-click="addBlankUpdate()" type="button">
            <h5 class="text-uppercase">
              <i class="fa fa-plus"></i>
              {t}Añadir{/t}
            </h5>
          </button>
        </div>
      </div>
      <div ng-repeat="updateItem in item.live_blog_updates track by $index" ng-cloak>
        <div class="article-liveblogupdate-actions m-t-50">
          <button class="btn btn-danger btn-small" ng-click="removeUpdate($index)" type="button"> <i class="fa fa-trash-o m-r-5"></i>{t}Eliminar elemento{/t}</button>
        </div>
        <hr/>
        <div class="form-group">

          <div class="row">
            <div class="col-lg-5 col-md-5">
              <label for="updateItem.title" class="form-label">
                {t}Title{/t}
              </label>
              <div class="controls">
              </div>
              <input class="form-control" id="updateItem.title" name="updateItem.title" ng-class="{ 'input-faded': flags.block.updateItem.title }" ng-model="updateItem.title" type="text">
            </div>
            <div class="col-lg-7 col-md-7">
              <div class="thumbnail-wrapper">
              <div class="overlay photo-overlay ng-cloak"  ng-class="{ 'open': overlay.photo_860 }"></div>
              <div class="confirm-dialog ng-cloak"  ng-class="{ 'open': overlay.photo_860 }">
                <p>{t}Are you sure?{/t}</p>
                <div class="confirm-actions">

                  <button class="btn btn-link" ng-click="toggleOverlay('photo_'+ updateItem.image_id)" type="button">
                    <i class="fa fa-times fa-lg"></i>
                    {t}No{/t}
                  </button>
                  <button class="btn btn-link" ng-click="removeItem('data.updateItem.image_id');removeItem(updateItem.image_id);toggleOverlay('photo_'+ updateItem.image_id)" type="button">
                    <i class="fa fa-check fa-lg"></i>
                    {t}Yes{/t}
                  </button>
                </div>
              </div>
              <div class="thumbnail-placeholder">
                <div class="img-thumbnail" ng-show="!updateItem.image_id">
                  <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="updateItem.image_id" media-picker-types="photo" photo-editor-enabled="true">
                    <i class="fa fa-picture-o fa-2x"></i>
                    <h5>{t}Select an element{/t}</h5>
                  </div>
                </div>
                <div class="dynamic-image-placeholder" ng-show="updateItem.image_id">
                  <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="updateItem.image_id">
                    <div class="thumbnail-actions">
                      <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo_'+ updateItem.image_id)">
                        <i class="fa fa-trash-o fa-2x"></i>
                      </div>
                      <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="updateItem.image_id" media-picker-types="photo" photo-editor-enabled="true">
                        <i class="fa fa-camera fa-2x"></i>
                      </div>
                    </div>
                  </dynamic-image>
                </div>
              </div>
            </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-5 col-md-5">
              <label class="form-label" for="updateItem.modified">
                {t}Modified Date{/t}
              </label>
              <div class="controls">
                <div class="input-group">
                  <input class="form-control" datetime-picker datetime-picker-timezone="{$app.locale->getTimeZone()->getName()}" datetime-picker-use-current=true datetime-picker-min="updateItem.created" id="updateItem.modified" name="updateItem.modified" ng-model="updateItem.modified" type="datetime">
                  <span class="input-group-addon add-on">
                    <span class="fa fa-calendar"></span>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-lg-7 col-md-7">
              <div class="form-group ng-cloak m-t-15">
                <label class="form-label" for="caption">
                  {t}Caption{/t}
                </label>
                <div class="controls">
                  <textarea class="form-control" id="caption" ng-model="updateItem.caption"></textarea>
                </div>
              </div>
            </div>
          </div>

        </div>
          <div class="form-group">
          <label class="form-label clearfix" for="updateItem.body">
            <div class="pull-left">{t}Body{/t}</div>
          </label>
            {acl isAllowed='PHOTO_ADMIN'}
              <div class="pull-right">
                <div class="btn btn-mini" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="editor.updateItem.body" photo-editor-enabled="true">
                  <i class="fa fa-plus"></i>
                  {t}Insert image{/t}
                </div>
              </div>
          {/acl}
            <div class="pull-right m-r-5">
              <div class="btn btn-mini"  content-picker content-picker-target="editor.updateItem.body" content-picker-selection="true" content-picker-type="album,article,attachment,opinion,poll,video" content-picker-max-size="10">
                <i class="fa fa-plus"></i>
                {t}Insert related{/t}
              </div>
            </div>
          <div class="controls">
            <textarea name="updateItem.body" id="updateItem.body" incomplete="incomplete" ng-model="updateItem.body" onm-editor onm-editor-preset="standard" class="form-control" rows="15"></textarea>
          </div>
        </div>
        {* {include file="common/component/related-contents/_featured-media.tpl" iName="featuredFrontpage" iTitle="{t}Featured in frontpage{/t}" types="photo,video,album"} *}

          <!-- <div class="thumbnail-wrapper">
            <div class="overlay photo-overlay ng-cloak"></div>
            <div class="confirm-dialog ng-cloak">
              <p>{t}Are you sure?{/t}</p>
              <div class="confirm-actions">
                <button class="btn btn-link" ng-click="toggleOverlay('image$index')" type="button">
                  <i class="fa fa-times fa-lg"></i>
                  {t}No{/t}
                </button>
                <button class="btn btn-link" ng-click="removeItem('updateItem.path');toggleOverlay('image$index')" type="button">
                  <i class="fa fa-check fa-lg"></i>
                  {t}Yes{/t}
                </button>
              </div>
            </div>
            <div class="thumbnail-placeholder">
              <div class="img-thumbnail" ng-show="!updateItem.path">
                <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="updateItem.path" media-picker-types="photo" photo-editor-enabled="true">
                  <i class="fa fa-picture-o fa-2x"></i>
                  <h5>{t}Select an element{/t}</h5>
                </div>
              </div>
              <div class="dynamic-image-placeholder" ng-show="updateItem.path">
                <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="636" transform="zoomcrop,220,220">
                  <div class="thumbnail-actions">
                    <div class="thumbnail-action remove-action" ng-click="toggleOverlay('image$index')">
                      <i class="fa fa-trash-o fa-2x"></i>
                    </div>
                    <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="updateItem.path" media-picker-types="photo" photo-editor-enabled="true">
                      <i class="fa fa-camera fa-2x"></i>
                    </div>
                  </div>
                </dynamic-image>
              </div>
            </div>
            <input name="caption" ng-model="updateItem.caption" type="hidden">
            <div class="form-group ng-cloak m-t-15">
              <label class="form-label" for="caption">
                {t}Caption{/t}
              </label>
              <div class="controls">
                <textarea class="form-control" id="caption" ng-model="updateItem.caption"></textarea>
              </div>
            </div>
          </div> -->
      </div>
    </div>
  </div>
