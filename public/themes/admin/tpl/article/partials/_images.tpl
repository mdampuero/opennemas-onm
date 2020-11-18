{acl isAllowed='PHOTO_ADMIN'}
  {is_module_activated name="IMAGE_MANAGER,VIDEO_MANAGER"}
    <div class="row">
      <div class="col-md-12">
        <div class="grid simple">
          <div class="grid-title">
            <h4>
              <i class="fa fa-picture-o"></i>
              {t}Images assigned{/t}
            </h4>
          </div>
          <div class="grid-body">
            <div class="row">
              <div class="col-md-4">
                <h5>{t}Image to show in frontpages{/t}</h5>
                <div class="thumbnail-wrapper">
                  <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.photo1 }"></div>
                  <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.photo1 }">
                    <p>{t}Are you sure?{/t}</p>
                    <div class="confirm-actions">
                      <button class="btn btn-link" ng-click="toggleOverlay('photo1')" type="button">
                        <i class="fa fa-times fa-lg"></i>
                        {t}No{/t}
                      </button>
                      <button class="btn btn-link" ng-click="removeItem('article.img1');toggleOverlay('photo1')" type="button">
                        <i class="fa fa-check fa-lg"></i>
                        {t}Yes{/t}
                      </button>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="thumbnail-placeholder">
                      <div class="img-thumbnail" ng-if="!article.img1">
                        <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.img1" ng-click="articleForm.$setDirty(true)" {is_module_activated name="es.openhost.module.imageEditor"} photo-editor-enabled="true" {/is_module_activated}>
                          <i class="fa fa-picture-o fa-2x"></i>
                          <h5>{t}Pick an image{/t}</h5>
                        </div>
                      </div>
                      <div class="dynamic-image-placeholder ng-cloak" ng-if="article.img1">
                        <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="article.img1" only-image="true">
                          <div class="thumbnail-actions ng-cloak">
                            <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo1')">
                              <i class="fa fa-trash-o fa-2x"></i>
                            </div>
                            <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.img1" ng-click="articleForm.$setDirty(true)" {is_module_activated name="es.openhost.module.imageEditor"} photo-editor-enabled="true" {/is_module_activated}>
                              <i class="fa fa-camera fa-2x"></i>
                            </div>
                          </div>
                          <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.img1" media-picker-type="photo" ng-click="articleForm.$setDirty(true)" {is_module_activated name="es.openhost.module.imageEditor"} photo-editor-enabled="true" {/is_module_activated}></div>
                        </dynamic-image>
                      </div>
                    </div>
                  </div>
                  <div class="form-group ng-cloak" ng-show="article.img1">
                    <label class="form-label" for="img1_footer">
                      {t}Footer text{/t}
                    </label>
                    <div class="controls">
                      <textarea class="form-control" name="img1_footer" ng-model="article.img1_footer" placeholder="[% data.article.img1_footer[data.extra.options.default] %]" tooltip-enable="config.locale != data.extra.options.default" tooltip-trigger="focus" uib-tooltip="[% data.article.img1_footer[data.extra.options.default] %]"></textarea>
                      <input type="hidden" name="img1" ng-model="article.img1" ng-value="img1"/>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <h5>{t}Image to show in inner{/t}</h5>
                <div class="thumbnail-wrapper">
                  <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.photo2 }"></div>
                  <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.photo2 }">
                    <p>{t}Are you sure?{/t}</p>
                    <div class="confirm-actions">
                      <button class="btn btn-link" ng-click="toggleOverlay('photo2')" type="button">
                        <i class="fa fa-times fa-lg"></i>
                        {t}No{/t}
                      </button>
                      <button class="btn btn-link" ng-click="removeItem('article.img2');toggleOverlay('photo2')" type="button">
                        <i class="fa fa-check fa-lg"></i>
                        {t}Yes{/t}
                      </button>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="thumbnail-placeholder">
                      <div class="img-thumbnail" ng-if="!article.img2">
                        <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.img2" ng-click="articleForm.$setDirty(true)">
                          <i class="fa fa-picture-o fa-2x"></i>
                          <h5>{t}Pick an image{/t}</h5>
                        </div>
                      </div>
                      <div class="dynamic-image-placeholder ng-cloak" ng-if="article.img2">
                        <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="article.img2" ng-if="article.img2" only-image="true">
                          <div class="thumbnail-actions ng-cloak">
                            <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo2')">
                              <i class="fa fa-trash-o fa-2x"></i>
                            </div>
                            <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.img2" ng-click="articleForm.$setDirty(true)">
                              <i class="fa fa-camera fa-2x"></i>
                            </div>
                          </div>
                          <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.img2" media-picker-type="photo" ng-click="articleForm.$setDirty(true)"></div>
                        </dynamic-image>
                      </div>
                    </div>
                  </div>
                  <div class="form-group ng-cloak" ng-show="article.img2">
                    <label class="form-label" for="title">
                      {t}Footer text{/t}
                    </label>
                    <div class="controls">
                      <textarea class="form-control" name="img2_footer" ng-model="article.img2_footer" placeholder="[% data.article.img2_footer[data.extra.options.default] %]" tooltip-enable="config.locale != data.extra.options.default" tooltip-trigger="focus" uib-tooltip="[% data.article.img1_footer[data.extra.options.default] %]"></textarea>
                      <input type="hidden" name="img2" ng-value="img2"/>
                    </div>
                  </div>
                </div>
              </div>
              {is_module_activated name="CRONICAS_MODULES"}
                {if strpos($smarty.server.REQUEST_URI, 'articles') !== false}
                <div class="col-md-4">
                  <h5>{t}Home image{/t}</h5>
                  <div class="thumbnail-wrapper">
                    <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.photo3 }"></div>
                    <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.photo3 }">
                      <p>{t}Are you sure?{/t}</p>
                      <div class="confirm-actions">
                        <button class="btn btn-link" ng-click="toggleOverlay('photo3')" type="button">
                          <i class="fa fa-times fa-lg"></i>
                          {t}No{/t}
                        </button>
                        <button class="btn btn-link" ng-click="removeItem('article.params.imageHome');toggleOverlay('photo3')" type="button">
                          <i class="fa fa-check fa-lg"></i>
                          {t}Yes{/t}
                        </button>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="thumbnail-placeholder">
                        <div class="img-thumbnail" ng-if="!article.params.imageHome">
                          <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.params.imageHome">
                            <i class="fa fa-picture-o fa-2x"></i>
                            <h5>{t}Pick an image{/t}</h5>
                          </div>
                        </div>
                        <div class="dynamic-image-placeholder ng-cloak" ng-if="article.params.imageHome">
                          <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="article.params.imageHome" only-image="true">
                            <div class="thumbnail-actions ng-cloak">
                              <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo3')">
                                <i class="fa fa-trash-o fa-2x"></i>
                              </div>
                               <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.params.imageHome">
                                <i class="fa fa-camera fa-2x"></i>
                              </div>
                            </div>
                            <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.params.imageHome" media-picker-type="photo"></div>
                          </dynamic-image>
                        </div>
                      </div>
                    </div>
                    <div class="form-group ng-cloak" ng-show="article.params.imageHome">
                      <label class="form-label" for="params[imageHomeFooter]">
                        {t}Footer text{/t}
                      </label>
                      <div class="controls">
                        <textarea class="form-control" name="params[imageHomeFooter]" ng-model="article.params.imageHomeFooter"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
                {/if}
              {/is_module_activated}
            </div>
          </div>
        </div>
      </div>
    </div>
    {if !isset($withoutVideo)}
      {acl isAllowed="VIDEO_ADMIN"}
        <div class="row">
          <div class="col-md-12">
            <div class="grid simple">
              <div class="grid-title">
                <h4>
                  <i class="fa fa-film"></i>
                  {t}Video{/t}
                </h4>
              </div>
              <div class="grid-body">
                <div class="row">
                  <div class="col-md-4">
                    <h5>{t}Video for frontpage{/t}</h5>
                    <div class="thumbnail-wrapper">
                      <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.video1 }"></div>
                      <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.video1 }">
                        <p>{t}Are you sure?{/t}</p>
                        <div class="confirm-actions">
                          <button class="btn btn-link" ng-click="toggleOverlay('video1')" type="button">
                            <i class="fa fa-times fa-lg"></i>
                            {t}No{/t}
                          </button>
                          <button class="btn btn-link" ng-click="removeItem('article.fk_video');toggleOverlay('video1')" type="button">
                            <i class="fa fa-check fa-lg"></i>
                            {t}Yes{/t}
                          </button>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="thumbnail-placeholder">
                          <div class="img-thumbnail" ng-if="!article.fk_video">
                            <div class="thumbnail-empty" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.fk_video" media-picker-type="video" ng-click="articleForm.$setDirty(true)">
                              <i class="fa fa-film fa-2x"></i>
                              <h5>{t}Pick a video{/t}</h5>
                            </div>
                          </div>
                          <div class="dynamic-image-placeholder ng-cloak" ng-if="article.fk_video">
                            <dynamic-image autoscale="true" class="img-thumbnail" instance="" ng-model="article.fk_video" property="thumb">
                              <div class="thumbnail-actions">
                                <div class="thumbnail-action remove-action" ng-click="toggleOverlay('video1')">
                                  <i class="fa fa-trash-o fa-2x"></i>
                                </div>
                                 <div class="thumbnail-action" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.fk_video" media-picker-type="video">
                                  <i class="fa fa-film fa-2x"></i>
                                </div>
                              </div>
                              <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.fk_video" media-picker-type="video"></div>
                            </dynamic-image>
                          </div>
                        </div>
                      </div>
                      <div class="form-group ng-cloak" ng-if="article.fk_video">
                        <ul>
                          <li>{t}File name{/t}: [% article.fk_video.title %]</li>
                          <li>{t}Creation date{/t}: [% article.fk_video.created %]</li>
                          <li>{t}Description{/t}: [% article.fk_video.description %]</li>
                          <li>{t}Tags{/t}: [% article.fk_video.metadata %]</li>
                        </ul>
                        <label class="form-label" for="title">
                          {t}Footer text for inner video:{/t}
                        </label>
                        <textarea  class="form-control" name="footer_video1" ng-model="article.footer_video1" placeholder="[% data.article.footer_video1[data.extra.options.default] %]" tooltip-enable="config.locale != data.extra.options.default" tooltip-trigger="focus" uib-tooltip="[% data.article.footer_video[data.extra.options.default] %]"></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <h5>{t}Video for inner article page{/t}</h5>
                    <div class="thumbnail-wrapper">
                      <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.video2 }"></div>
                      <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.video2 }">
                        <p>{t}Are you sure?{/t}</p>
                        <div class="confirm-actions">
                          <button class="btn btn-link" ng-click="toggleOverlay('video2')" type="button">
                            <i class="fa fa-times fa-lg"></i>
                            {t}No{/t}
                          </button>
                          <button class="btn btn-link" ng-click="removeItem('article.fk_video2');toggleOverlay('video2')" type="button">
                            <i class="fa fa-check fa-lg"></i>
                            {t}Yes{/t}
                          </button>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="thumbnail-placeholder">
                          <div class="img-thumbnail" ng-if="!article.fk_video2">
                            <div class="thumbnail-empty" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.fk_video2" media-picker-type="video" ng-click="articleForm.$setDirty(true)">
                              <i class="fa fa-film fa-2x"></i>
                              <h5>Pick a video</h5>
                            </div>
                          </div>
                          <div class="dynamic-image-placeholder ng-cloak" ng-if="article.fk_video2 && article.fk_video2">
                            <dynamic-image autoscale="true" class="img-thumbnail" instance="" ng-model="article.fk_video2" property="thumb">
                              <div class="thumbnail-actions">
                                <div class="thumbnail-action remove-action" ng-click="toggleOverlay('video2')">
                                  <i class="fa fa-trash-o fa-2x"></i>
                                </div>
                                 <div class="thumbnail-action" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.fk_video2" media-picker-type="video" ng-click="articleForm.$setDirty(true)">
                                  <i class="fa fa-film fa-2x"></i>
                                </div>
                              </div>
                              <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="article.fk_video2" media-picker-type="video" ng-click="articleForm.$setDirty(true)"></div>
                            </dynamic-image>
                          </div>
                        </div>
                      </div>
                      <div class="form-group ng-cloak" ng-if="article.fk_video2">
                        <ul>
                          <li>{t}File name{/t}: [% article.fk_video2.title %]</li>
                          <li>{t}Creation date{/t}: [% article.fk_video2.created %]</li>
                          <li>{t}Description{/t}: [% article.fk_video2.description %]</li>
                          <li>{t}Tags{/t}: [% article.fk_video2.metadata %]</li>
                        </ul>
                        <label class="form-label" for="title">
                          {t}Footer text for inner video:{/t}
                        </label>
                        <textarea  class="form-control" name="footer_video2" ng-model="article.footer_video2" placeholder="[% data.article.footer_video2[data.extra.options.default] %]" tooltip-enable="config.locale != data.extra.options.default" tooltip-trigger="focus" uib-tooltip="[% data.article.footer_video[data.extra.options.default] %]"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      {/acl}
    {/if}
  {/is_module_activated}
{/acl}
