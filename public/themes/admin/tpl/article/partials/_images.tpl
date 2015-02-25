{acl isAllowed='PHOTO_ADMIN'}
  {is_module_activated name="IMAGE_MANAGER,VIDEO_MANAGER"}
    <div class="row">
      <div class="col-md-12">
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Image{/t}</h4>
          </div>
          <div class="grid-body">
            <div class="row">
              <div class="col-md-4" {if isset($photo1) && $photo1->name}ng-init="photo1 = {json_encode($photo1)|replace:'"':'\''}"{/if}>
                <h5>{t}Frontpage image{/t}</h5>
                <div class="form-group">
                  <div class="thumbnail-placeholder" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1">
                    <div class="img-thumbnail" ng-if="!photo1">
                      <div class="thumbnail-empty">
                        <i class="fa fa-picture-o fa-2x"></i>
                        <h5>Pick an image</h5>
                      </div>
                    </div>
                    <div class="dynamic-image-placeholder" ng-if="photo1">
                      <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo1" transform="thumbnail,220,220"></dynamic-image>
                    </div>
                  </div>
                  <div class="thumbnail-actions" ng-if="photo1" style="text-align: center; margin-top: 10px;">
                    <button class="btn btn-default" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1">
                      <i class="fa fa-camera"></i>
                      {t}Change image{/t}
                    </button>
                    <button class="btn btn-danger" ng-click="removeImage('photo1')" type="button">
                      <i class="fa fa-trash-o"></i>
                      {t}Remove{/t}
                    </button>
                  </div>
                </div>
                <div class="form-group" ng-if="photo1">
                  <label class="form-label" for="img1_footer">
                    {t}Footer text{/t}
                  </label>
                  <div class="controls">
                    <textarea class="form-control" name="img1_footer" ng-model="img1_footer">{$article->img1_footer|clearslash|escape:'html'}</textarea>
                    <input type="hidden" name="img1" ng-value="[% img1 %]"/>
                  </div>
                </div>
              </div>
              <div class="col-md-4" {if isset($photo2) && $photo2->name}ng-init="photo2 = {json_encode($photo2)|replace:'"':'\''}"{/if}>
                <h5>{t}Inner image{/t}</h5>
                <div class="form-group">
                  <div class="thumbnail-placeholder">
                    <div class="img-thumbnail" ng-if="!photo2">
                      <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo2">
                        <i class="fa fa-picture-o fa-2x"></i>
                        <h5>Pick an image</h5>
                      </div>
                    </div>
                    <div class="dynamic-image-placeholder" ng-if="photo2">
                      <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo2" transform="thumbnail,220,220" ng-if="photo2">
                        <div class="thumbnail-actions">
                          <div class="thumbnail-action remove-action" ng-click="removeImage('photo2')">
                            <i class="fa fa-trash-o fa-2x"></i>
                          </div>
                          <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo2">
                            <i class="fa fa-camera fa-2x"></i>
                          </div>
                        </div>
                      </dynamic-image>
                    </div>
                  </div>
                </div>
                <div class="form-group" ng-if="photo2">
                  <label class="form-label" for="title">
                    {t}Footer text{/t}
                  </label>
                  <div class="controls">
                    <textarea class="form-control" name="img2_footer" ng-model="img2_footer">{$article->img2_footer|clearslash|escape:'html'}</textarea>
                    <input type="hidden" name="img2" ng-value="[% img2 %]"/>
                  </div>
                </div>
              </div>
              {is_module_activated name="CRONICAS_MODULES"}
                <div class="col-md-4" {if isset($photo3) && $photo3->name}ng-init="photo3 = {json_encode($photo3)|replace:'"':'\''}"{/if}>
                  <h5>{t}Home image{/t}</h5>
                  <div class="form-group">
                    <div class="thumbnail-placeholder">
                      <div class="img-thumbnail" ng-if="!photo3">
                        <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo3">
                          <i class="fa fa-picture-o fa-2x"></i>
                          <h5>Pick an image</h5>
                        </div>
                      </div>
                      <div class="dynamic-image-placeholder" ng-if="photo3">
                        <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo3" transform="thumbnail,220,220">
                          <div class="thumbnail-actions">
                            <div class="thumbnail-action remove-action" ng-click="removeImage('photo3')">
                              <i class="fa fa-trash-o fa-2x"></i>
                            </div>
                          </div>
                          <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo3" media-picker-types="photo">
                          </div>
                        </dynamic-image>
                      </div>
                    </div>
                  </div>
                  <div class="form-group" ng-if="photo3">
                    <label class="form-label" for="params[imageHomeFooter]">
                      {t}Image footer text{/t}
                    </label>
                    <div class="controls">
                      <textarea class="form-control" name="params[imageHomeFooter]" ng-model="imageHomeFooter">{$article->params['imageHomeFooter']|clearslash|escape:'html'}</textarea>
                      <input type="hidden" name="params[imageHome]" ng-value="imageHome" class="related-element-id"/>
                    </div>
                  </div>
                </div>
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
                <h4>{t}Video{/t}</h4>
              </div>
              <div class="grid-body">
                <div class="row">
                  <div class="col-md-4" {if isset($video1) && $video1->title}ng-init="video1 = { id: '{$video1->id}', fk_video: '{$video1->id}', description: '{$video1->description}', thumb: '{$video1->thumb}', created: '{$video1->created}', metadata: '{$video1->metadata}' }"{/if}>
                    <h5>{t}Video for frontpage{/t}</h5>
                    <div class="form-group">
                      <div class="thumbnail-placeholder">
                        <div class="img-thumbnail" ng-if="!video1">
                          <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="video1" media-picker-type="video">
                            <i class="fa fa-film fa-2x"></i>
                            <h5>Pick a video</h5>
                          </div>
                        </div>
                        <div class="dynamic-image-placeholder" ng-if="video1">
                          <dynamic-image autoscale="true" class="img-thumbnail" dynamic-image-property="thumb" ng-model="video1">
                          </dynamic-image>
                        </div>
                      </div>
                      <div class="thumbnail-actions" ng-if="video1" style="text-align: center; margin-top: 10px;position: relative;">
                        <button class="btn btn-link" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1" media-picker-type="video" type="button">
                          <i class="fa fa-film"></i>
                          {t}Change video{/t}
                        </button>
                        <button class="btn btn-link" ng-click="removeImage('video1')" type="button" style="color: #f35958;">
                          <i class="fa fa-times"></i>
                          {t}Remove{/t}
                        </button>
                      </div>
                    </div>
                    <div class="form-group" ng-if="video1">
                        <ul>
                          <li>{t}File name{/t}: [% video1.title %]</li>
                          <li>{t}Creation date{/t}: [% video1.created %]</li>
                          <li>{t}Description{/t}: [% video1.description %]</li>
                          <li>{t}Tags{/t}: [% video1.metadata %]</li>
                        </ul>
                        <label class="form-label" for="title">
                          {t}Footer text for inner video:{/t}
                        </label>
                        <textarea  class="form-control" name="footer_video" ng-model="footer_video" ng-value="footer_video">{$article->footer_video1|clearslash|escape:'html'}</textarea>
                        <input type="hidden" name="fk_video" ng-value="fk_video" class="related-element-id"/>
                      </div>
                  </div>
                  <div class="col-md-4" {if isset($video2) && $video2->title}ng-init="video2 = { id: '{$video2->id}', fk_video: '{$video2->id}', description: '{$video2->description}', thumb: '{$video2->thumb}', created: '{$video2->created}', metadata: '{$video2->metadata}' }"{/if}>
                    <h5>{t}Video for inner article page{/t}</h5>
                    <div class="form-group">
                      <div class="thumbnail-placeholder">
                        <div class="img-thumbnail" ng-if="!video2">
                          <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="video2" media-picker-type="video">
                            <i class="fa fa-film fa-2x"></i>
                            <h5>Pick a video</h5>
                          </div>
                        </div>
                        <div class="dynamic-image-placeholder" ng-if="video2">
                          <dynamic-image autoscale="true" class="img-thumbnail" dynamic-image-property="thumb" ng-model="video2">
                            <div class="thumbnail-actions" style="left: 5px; background: rgba(0,0,0,.75); right: 5px; bottom: 5px; padding: 0;">
                              <div ng-click="removeImage('video2')" style="padding: 8px; color: #fff; cursor: pointer; display: inline-block;">
                                <i class="fa fa-trash-o fa-lg"></i>
                                {t}Remove{/t}
                              </div>
                            </div>
                            <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="video2" media-picker-type="video"></div>
                          </dynamic-image>
                        </div>
                      </div>
                    </div>
                    <div class="form-group" ng-if="video2">
                      <ul>
                        <li>{t}File name{/t}: [% video2.title %]</li>
                        <li>{t}Creation date{/t}: [% video2.created %]</li>
                        <li>{t}Description{/t}: [% video2.description %]</li>
                        <li>{t}Tags{/t}: [% video2.metadata %]</li>
                      </ul>
                      <label class="form-label" for="title">
                        {t}Footer text for inner video:{/t}
                      </label>
                      <textarea  class="form-control" name="footer_video2" ng-model="footer_video2" ng-value="footer_video2">{$article->footer_video2|clearslash|escape:'html'}</textarea>
                      <input type="hidden" name="fk_video2" ng-value="fk_video2" class="related-element-id"/>
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

    {is_module_activated name="IMAGE_MANAGER"}
    <script>
    jQuery(document).ready(function($){
        $('#related_media .unset').on('click', function (e, ui) {
            e.preventDefault();

            var parent = jQuery(this).closest('.contentbox');

            parent.find('.related-element-id').val('');
            parent.find('.related-element-footer').val('');
            parent.find('.image').html('');

            parent.removeClass('assigned');
        });
    });
    </script>
    {/is_module_activated}

    {is_module_activated name="VIDEO_MANAGER"}
    <script>
    jQuery(document).ready(function($){
        $('#related-videos').tabs();
        jQuery('#related-videos .delete-button').on('click', function () {
            var parent = jQuery(this).parent();
            var elementID = parent.find('.related-element-id');

            if (elementID.val() > 0) {
                elementID.data('id', elementID.val());
                elementID.val(null);
                parent.fadeTo('slow', 0.5);
            } else {
                elementID.val(elementID.data('id'));
                parent.fadeTo('slow', 1);
            };
        });

        load_ajax_in_container('{url name=admin_videos_content_provider_gallery category=$category}', $('#videos'));

        function load_video_results () {
            var category = $('#category_video option:selected').val();
            var text = $('#stringVideoSearch').val();
            var url = '{url name=admin_videos_content_provider_gallery}?'+'category='+category+'&metadatas='+encodeURIComponent(text);
            load_ajax_in_container(
                url,
                $('#videos')
            );
        }
        $('#stringVideoSearch, #category_video').on('change', function(e, ui) {
            return load_video_results();
        });
        $('#stringVideoSearch').keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return load_video_results();
            }
        });

        $('#videos').on('click', '.pager a', function(e, ui) {
            e.preventDefault();
            var link = $(this);
            load_ajax_in_container(link.attr('href'), $('#videos'));
        });
    });
    </script>
    {/is_module_activated}
{/acl}
