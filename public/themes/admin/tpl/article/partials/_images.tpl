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
                      <button class="btn btn-link" ng-click="removeItem('photo1');toggleOverlay('photo1')" type="button">
                        <i class="fa fa-check fa-lg"></i>
                        {t}Yes{/t}
                      </button>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="thumbnail-placeholder">
                      <div class="img-thumbnail" ng-if="!photo1">
                        <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1" ng-click="articleForm.$setDirty(true)">
                          <i class="fa fa-picture-o fa-2x"></i>
                          <h5>{t}Pick an image{/t}</h5>
                        </div>
                      </div>
                      <div class="dynamic-image-placeholder ng-cloak" ng-if="photo1">
                        <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo1" only-image="true">
                          <div class="thumbnail-actions ng-cloak">
                            <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo1')">
                              <i class="fa fa-trash-o fa-2x"></i>
                            </div>
                            <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1" ng-click="articleForm.$setDirty(true)">
                              <i class="fa fa-camera fa-2x"></i>
                            </div>
                          </div>
                          <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1" media-picker-type="photo" ng-click="articleForm.$setDirty(true)"></div>
                        </dynamic-image>
                      </div>
                    </div>
                  </div>
                  <div class="form-group ng-cloak" ng-show="photo1">
                    <label class="form-label" for="img1_footer">
                      {t}Footer text{/t}
                    </label>
                    <div class="controls">
                      <textarea class="form-control" name="img1_footer" ng-model="article.img1_footer"></textarea>
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
                      <button class="btn btn-link" ng-click="removeItem('photo2');toggleOverlay('photo2')" type="button">
                        <i class="fa fa-check fa-lg"></i>
                        {t}Yes{/t}
                      </button>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="thumbnail-placeholder">
                      <div class="img-thumbnail" ng-if="!photo2">
                        <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo2" ng-click="articleForm.$setDirty(true)">
                          <i class="fa fa-picture-o fa-2x"></i>
                          <h5>{t}Pick an image{/t}</h5>
                        </div>
                      </div>
                      <div class="dynamic-image-placeholder ng-cloak" ng-if="photo2">
                        <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo2" ng-if="photo2" only-image="true">
                          <div class="thumbnail-actions ng-cloak">
                            <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo2')">
                              <i class="fa fa-trash-o fa-2x"></i>
                            </div>
                            <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo2" ng-click="articleForm.$setDirty(true)">
                              <i class="fa fa-camera fa-2x"></i>
                            </div>
                          </div>
                          <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo2" media-picker-type="photo" ng-click="articleForm.$setDirty(true)"></div>
                        </dynamic-image>
                      </div>
                    </div>
                  </div>
                  <div class="form-group ng-cloak" ng-show="photo2">
                    <label class="form-label" for="title">
                      {t}Footer text{/t}
                    </label>
                    <div class="controls">
                      <textarea class="form-control" name="img2_footer" ng-model="article.img2_footer"></textarea>
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
                        <button class="btn btn-link" ng-click="removeItem('photo3');toggleOverlay('photo3')" type="button">
                          <i class="fa fa-check fa-lg"></i>
                          {t}Yes{/t}
                        </button>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="thumbnail-placeholder">
                        <div class="img-thumbnail" ng-if="!photo3">
                          <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo3">
                            <i class="fa fa-picture-o fa-2x"></i>
                            <h5>{t}Pick an image{/t}</h5>
                          </div>
                        </div>
                        <div class="dynamic-image-placeholder ng-cloak" ng-if="photo3">
                          <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo3" only-image="true">
                            <div class="thumbnail-actions ng-cloak">
                              <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo3')">
                                <i class="fa fa-trash-o fa-2x"></i>
                              </div>
                               <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo3">
                                <i class="fa fa-camera fa-2x"></i>
                              </div>
                            </div>
                            <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo3" media-picker-type="photo"></div>
                          </dynamic-image>
                        </div>
                      </div>
                    </div>
                    <div class="form-group ng-cloak" ng-show="photo3">
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
                          <button class="btn btn-link" ng-click="removeItem('video1');toggleOverlay('video1')" type="button">
                            <i class="fa fa-check fa-lg"></i>
                            {t}Yes{/t}
                          </button>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="thumbnail-placeholder">
                          <div class="img-thumbnail" ng-if="!video1">
                            <div class="thumbnail-empty" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="video1" media-picker-type="video">
                              <i class="fa fa-film fa-2x"></i>
                              <h5>{t}Pick a video{/t}</h5>
                            </div>
                          </div>
                          <div class="dynamic-image-placeholder ng-cloak" ng-if="video1 && video1.thumb_image">
                            <dynamic-image autoscale="true" class="img-thumbnail" ng-model="video1.thumb_image" instance="{$smarty.const.INSTANCE_MEDIA}">
                              <div class="thumbnail-actions">
                                <div class="thumbnail-action remove-action" ng-click="toggleOverlay('video1')">
                                  <i class="fa fa-trash-o fa-2x"></i>
                                </div>
                                 <div class="thumbnail-action" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="video1" media-picker-type="video">
                                  <i class="fa fa-film fa-2x"></i>
                                </div>
                              </div>
                              <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="video1" media-picker-type="video"></div>
                            </dynamic-image>
                          </div>
                          <div class="dynamic-image-placeholder ng-cloak" ng-if="video1 && !video1.thumb_image">
                            <dynamic-image autoscale="true" class="img-thumbnail" property="thumb" ng-model="video1">
                              <div class="thumbnail-actions">
                                <div class="thumbnail-action remove-action" ng-click="toggleOverlay('video1')">
                                  <i class="fa fa-trash-o fa-2x"></i>
                                </div>
                                 <div class="thumbnail-action" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="video1" media-picker-type="video">
                                  <i class="fa fa-film fa-2x"></i>
                                </div>
                              </div>
                              <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="video1" media-picker-type="video"></div>
                            </dynamic-image>
                          </div>
                        </div>
                      </div>
                      <div class="form-group ng-cloak" ng-if="video1">
                        <ul>
                          <li>{t}File name{/t}: [% video1.title %]</li>
                          <li>{t}Creation date{/t}: [% video1.created %]</li>
                          <li>{t}Description{/t}: [% video1.description %]</li>
                          <li>{t}Tags{/t}: [% video1.metadata %]</li>
                        </ul>
                        <label class="form-label" for="title">
                          {t}Footer text for inner video:{/t}
                        </label>
                        <textarea  class="form-control" name="footer_video" ng-model="article.footer_video"></textarea>
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
                          <button class="btn btn-link" ng-click="removeItem('video2');toggleOverlay('video2')" type="button">
                            <i class="fa fa-check fa-lg"></i>
                            {t}Yes{/t}
                          </button>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="thumbnail-placeholder">
                          <div class="img-thumbnail" ng-if="!video2">
                            <div class="thumbnail-empty" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="video2" media-picker-type="video">
                              <i class="fa fa-film fa-2x"></i>
                              <h5>Pick a video</h5>
                            </div>
                          </div>
                          <div class="dynamic-image-placeholder ng-cloak" ng-if="video2 && video2.thumb_image">
                            <dynamic-image autoscale="true" class="img-thumbnail" ng-model="video2.thumb_image" instance="{$smarty.const.INSTANCE_MEDIA}">
                              <div class="thumbnail-actions">
                                <div class="thumbnail-action remove-action" ng-click="toggleOverlay('video2')">
                                  <i class="fa fa-trash-o fa-2x"></i>
                                </div>
                                 <div class="thumbnail-action" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="video2" media-picker-type="video">
                                  <i class="fa fa-film fa-2x"></i>
                                </div>
                              </div>
                              <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="video2" media-picker-type="video"></div>
                            </dynamic-image>
                          </div>
                          <div class="dynamic-image-placeholder ng-cloak" ng-if="video2 && !video2.thumb_image">
                            <dynamic-image autoscale="true" class="img-thumbnail" property="thumb" ng-model="video2">
                              <div class="thumbnail-actions">
                                <div class="thumbnail-action remove-action" ng-click="toggleOverlay('video2')">
                                  <i class="fa fa-trash-o fa-2x"></i>
                                </div>
                                 <div class="thumbnail-action" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="video2" media-picker-type="video">
                                  <i class="fa fa-film fa-2x"></i>
                                </div>
                              </div>
                              <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore" media-picker-selection="true" media-picker-max-size="1" media-picker-target="video2" media-picker-type="video"></div>
                            </dynamic-image>
                          </div>
                        </div>
                      </div>
                      <div class="form-group ng-cloak" ng-if="video2">
                        <ul>
                          <li>{t}File name{/t}: [% video2.title %]</li>
                          <li>{t}Creation date{/t}: [% video2.created %]</li>
                          <li>{t}Description{/t}: [% video2.description %]</li>
                          <li>{t}Tags{/t}: [% video2.metadata %]</li>
                        </ul>
                        <label class="form-label" for="title">
                          {t}Footer text for inner video:{/t}
                        </label>
                        <textarea  class="form-control" name="footer_video2" ng-model="article.footer_video2"></textarea>
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
