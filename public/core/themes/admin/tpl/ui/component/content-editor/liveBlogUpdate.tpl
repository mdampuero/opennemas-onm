  <div class="grid simple" ng-show="item.liveBlogPosting">
    <div class="grid-body">
    <div class="col-xs-6 p-b-15 p-t-15 col-lg-4 col-lg-offset-4">
      <button class="btn btn-block btn-default btn-loading" ng-click="addBlankUpdate()" type="button">
        <h5 class="text-uppercase">
          <i class="fa fa-plus"></i>
          Añadir
        </h5>
      </button>
    </div>
      <hr/>
{include file="common/component/related-contents/_featured-media.tpl" iName="featuredFrontpage" iTitle="{t}Featured in frontpage{/t}" types="photo,video,album"}
      <div ng-repeat="updateItem in item.blogUpdates track by $index" ng-cloak>

        <div class="form-group">
            <label for="updateItem.title" class="form-label">
              {t}Title{/t}
            </label>
          <div class="controls">
          </div>
              <input class="form-control" id="updateItem.title" name="updateItem.title" ng-class="{ 'input-faded': flags.block.updateItem.title }"ng-model="updateItem.title" type="text">
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
      </div>
    </div>
  </div>
