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
                  <div class="thumbnail-placeholder" media-picker media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1">
                    <div class="img-thumbnail" ng-if="!photo1">
                      <div class="thumbnail-empty">
                        <i class="fa fa-picture-o fa-2x"></i>
                        <h5>Pick an image</h5>
                      </div>
                    </div>
                    <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo1" transform="thumbnail,220,220" ng-if="photo1"></dynamic-image>
                  </div>
                </div>
                <div class="form-group" ng-if="photo1">
                  <label class="form-label" for="img1_footer">
                    {t}Footer text{/t}
                  </label>
                  <div class="controls">
                    <textarea class="form-control" name="img1_footer">{$article->img1_footer|clearslash|escape:'html'}</textarea>
                    <input type="hidden" name="img1" value="{$article->img1|default:""}" class="related-element-id"/>
                  </div>
                </div>
              </div>

              <div class="col-md-4" {if isset($photo2) && $photo2->name}ng-init="photo1 = {json_encode($photo2)|replace:'"':'\''}"{/if}>
                <h5>{t}Inner image{/t}</h5>
                <div class="form-group">
                  <div class="thumbnail-placeholder" media-picker media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo2">
                    <div class="img-thumbnail" ng-if="!photo2">
                      <div class="thumbnail-empty">
                        <i class="fa fa-picture-o fa-2x"></i>
                        <h5>Pick an image</h5>
                      </div>
                    </div>
                    <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo2" transform="thumbnail,220,220" ng-if="photo2"></dynamic-image>
                  </div>
                </div>
                <div class="form-group" ng-if="photo2">
                  <label class="form-label" for="title">
                    {t}Footer text{/t}
                  </label>
                  <div class="controls">
                    <textarea class="form-control" name="img2_footer">{$article->img2_footer|clearslash|escape:'html'}</textarea>
                    <input type="hidden" name="img2" value="{$article->img2|default:""}" class="related-element-id"/>
                  </div>
                </div>
              </div>

              {is_module_activated name="CRONICAS_MODULES"}
                <div class="col-md-4" {if isset($photo3) && $photo3->name}ng-init="photo1 = {json_encode($photo3)|replace:'"':'\''}"{/if}>
                  <h5>{t}Home image{/t}</h5>
                  <div class="form-group">
                    <div class="thumbnail-placeholder" media-picker media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo3">
                      <div class="img-thumbnail" ng-if="!photo3">
                        <div class="thumbnail-empty">
                          <i class="fa fa-picture-o fa-2x"></i>
                          <h5>Pick an image</h5>
                        </div>
                      </div>
                      <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo3" transform="thumbnail,220,220" ng-if="photo3"></dynamic-image>
                    </div>
                  </div>
                  <div class="form-group" ng-if="photo3">
                    <label for="params[imageHomeFooter]">
                      {t}Image footer text{/t}
                    </label>
                    <div class="controls">
                      <textarea class="form-control" name="params[imageHomeFooter]">{$article->img2_footer|clearslash|escape:'html'}</textarea>
                      <input type="hidden" name="params[imageHome]" value="{$article->params['imageHome']|default:""}" class="related-element-id"/>
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
                                  <div class="col-md-4">
                                      <h5>{t}Video for frontpage{/t}</h5>
                                      <div>
                                          <a class="delete-button" onclick="javascript:recuperar_eliminar('video1');">
                                              <img src="/themes/admin/images/trash.png" id="remove_video1" alt="Eliminar" title="Eliminar" />
                                          </a>
                                          <div class="clearfix">
                                              <div class="thumbnail article-resource-image">
                                                  {if $video1->pk_video}
                                                      <img src="{$video1->information['thumbnail']}"
                                                           name="{$video1->pk_video}" style="width:120px" />
                                                  {else}
                                                      {if isset($video1) && $video1->pk_video}
                                                          {if $video1->author_name == 'internal'}
                                                          <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}/../{$video1->information['thumbnails']['normal']}" />
                                                          {else}
                                                          <img src="{$video1->information['thumbnail']}" />
                                                          {/if}
                                                      {else}
                                                      <div class="drop-here">
                                                          {t}Drop a video to here{/t}
                                                      </div>
                                                      {/if}
                                                  {/if}
                                              </div>
                                              <div class="article-resource-image-info">
                                                  <div><label>{t}File name{/t}</label>     <span class="filename">{$video1->name|default:'default_img.jpg'}</span></div>
                                                  <div><label>{t}Creation date{/t}</label> <span class="created_time">{$video1->created|default:""}</span></div>
                                                  <div><label>{t}Description{/t}</label>   <span class="description">{$video1->description|escape:'html'}</span></div>
                                                  <div><label>{t}Tags{/t}</label>          <span class="tags">{$video1->metadata|default:""}</span></div>
                                              </div>
                                          </div><!-- / -->
                                          <div class="article-resource-footer">
                                              <!-- <label for="title">{t}Footer text for frontpage image:{/t}</label> -->
                                              <!-- <textarea name="img1_footer" style="width:95%" class="related-element-footer">{$article->img1_footer|clearslash|escape:'html'}</textarea> -->
                                              <input type="hidden" name="fk_video" value="{$article->fk_video|default:""}" class="related-element-id" />
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-4">
                                      <h5>{t}Video for inner article page{/t}</h5>
                                      <div>
                                          <a class="delete-button">
                                              <img src="/themes/admin/images/trash.png" id="remove_video2" alt="Eliminar" title="Eliminar" />
                                          </a>
                                          <div class="clearfix">
                                              <div class="thumbnail article-resource-image">
                                                  {if isset($video2) && $video2->pk_video}
                                                      {if $video2->author_name == 'internal'}
                                                          <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}/../{$video2->information['thumbnails']['normal']}" />
                                                      {else}
                                                          <img src="{$video2->information['thumbnail']}"/>
                                                      {/if}
                                                  {else}
                                                      <div class="drop-here">
                                                          {t}Drop a video to here{/t}
                                                      </div>
                                                  {/if}
                                              </div>
                                              <div class="article-resource-image-info">
                                                  <div><label>{t}File name{/t}</label>     <span class="filename">{$video2->name|default:'default_img.jpg'}</span></div>
                                                  <div><label>{t}Creation date{/t}</label> <span class="created_time">{$video2->created|default:""}</span></div>
                                                  <div><label>{t}Description{/t}</label>   <span class="description">{$video2->description|escape:'html'}</span></div>
                                                  <div><label>{t}Tags{/t}</label>          <span class="tags">{$video2->metadata|default:""}</span></div>
                                              </div>
                                          </div>
                                          <div class="article-resource-footer">
                                              <label for="title">{t}Footer text for inner video:{/t}</label>
                                              <textarea name="footer_video2" style="width:95%" class="related-element-footer">{$article->footer_video2|clearslash|escape:'html'}</textarea>
                                              <input type="hidden" name="fk_video2" value="{$video2->pk_video}" class="related-element-id"/>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-4">
                                      <h5>{t}Available videos{/t}</h5>
                                      <div id="videos-container" class="photos">
                                          <div class="input-append">
                                              <input class="textoABuscar noentersubmit" id="stringVideoSearch" name="stringVideoSearch" type="text"
                                                     placeholder="{t}Search videos by title...{/t}"  style="width: 150px !important;"
                                                     />
                                              <select style="width:140px"  id="category_video" name="category_video">
                                                  <option value="0">GLOBAL</option>
                                                  {section name=as loop=$allcategorys}
                                                      <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if}>{$allcategorys[as]->title}</option>
                                                      {section name=su loop=$subcat[as]}
                                                              <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if}>&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                                      {/section}
                                                  {/section}
                                              </select>
                                          </div>
                                          <div id="videos">
                                              <!-- Ajax -->
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
