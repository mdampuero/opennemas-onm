{extends file="base/admin.tpl"}

{block name="footer-js" append}
  <script>
    $(document).ready(function($){
      $("#formulario").on("submit", function(event) {
        if (!$('.album-thumbnail-sortable').length) {
          $("#modal-edit-album-errors").modal('show');
          return false;
        }

        return true;
      }).on('click', '.cover-image .unset', function (e, ui) {
          e.preventDefault();

          var parent = $(this).closest('.contentbox');

          parent.find('.related-element-id').val('');
          parent.find('.related-element-footer').val('');
          parent.find('.image').html('');

          parent.removeClass('assigned');
      });

      $('#title').on('change', function(e, ui) {
        var metaTags = $('#metadata');

        // Fill tags from title and category
        if (!metaTags.val()) {
          var tags = $('#title').val();
          fill_tags(tags, '#metadata', '{url name=admin_utils_calculate_tags}');
        }
      });
    });
  </script>
{/block}

{block name="content"}
  <form action="{if isset($album->id)}{url name=admin_album_update id=$album->id}{else}{url name=admin_album_create}{/if}" method="POST" id="formulario" ng-controller="AlbumCtrl">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-stack-overflow"></i>
                {t}Albums{/t}
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs">
              <h5>
                {if isset($album->id)}
                  {t}Editing album{/t}
                {else}
                  {t}Creating Album{/t}
                {/if}
              </h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_albums category=$category}" title="{t}Go back{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                {if isset($album->id)}
                {acl isAllowed="ALBUM_UPDATE"}
                <button class="btn btn-primary" type="submit">
                  <i class="fa fa-save"></i>
                  {t}Save{/t}
                </button>
                {/acl}
                {else}
                {acl isAllowed="ALBUM_CREATE"}
                <button class="btn btn-primary" type="submit">
                  <i class="fa fa-save"></i>
                  {t}Save{/t}
                </button>
                {/acl}
                {/if}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      {render_messages}
      <div class="row">
        <div class="col-md-8">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <label class="form-label" for="title">
                  {t}Title{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="title" name="title" required="required" type="text" value="{$album->title|default:""}"/>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="description">
                  {t}Description{/t}
                </label>
                <div class="controls">
                  <textarea class="form-control" id="description" name="description" onm-editor onm-editor-preset="simple">{$album->description|clearslash}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <div class="checkbox">
                  <input type="checkbox" value="1" id="content_status" name="content_status" {if $album->content_status eq 1}checked="checked"{/if}>
                  <label for="content_status">{t}Published{/t}</label>
                </div>
              </div>
              {is_module_activated name="COMMENT_MANAGER"}
              <div class="form-group">
                <div class="checkbox">
                  <input id="with_comment" name="with_comment" type="checkbox" {if (!isset($album) && (!isset($commentsConfig['with_comments']) || $commentsConfig['with_comments']) eq 1) || (isset($album) && $album->with_comment eq 1)}checked{/if} value="1" />
                  <label for="with_comment">{t}Allow comments{/t}</label>
                </div>
              </div>
              {/is_module_activated}
              <div class="form-group">
                <label class="form-label">
                  {t}Category{/t}
                </label>
                <div class="controls">
                  {include file="common/selector_categories.tpl" name="category" item=$album}
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="agency">
                  {t}Agency{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="agency" name="agency" type="text" value="{$album->agency|clearslash|escape:"html"}"/>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">
                  {t}Author{/t}
                </label>
                <div class="controls">
                  {acl isAllowed="CONTENT_OTHER_UPDATE"}
                    <select name="fk_author" id="fk_author">
                      {html_options options=$authors selected=$album->fk_author}
                    </select>
                  {aclelse}
                    {if !isset($album->fk_author)}
                      {$smarty.session.realname}
                      <input type="hidden" name="fk_author" value="{$smarty.session.userid}">
                    {else}
                      {$authors[$album->fk_author]}
                      <input type="hidden" name="fk_author" value="{$album->fk_author}">
                    {/if}
                  {/acl}
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">
                  {t}Tags{/t}
                </label>
                <div class="controls">
                  <input data-role="tagsinput" id="metadata" name="metadata" required="required" type="text" value="{$album->metadata}"/>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-8" {if !empty($photos)}ng-init="parsePhotos({json_encode($photos)|replace:'"':'\''})"{/if}>
          <div class="grid simple">
            <div class="grid-title">
              <h4>{t}Album images{/t}</h4>
            </div>
            <div class="grid-body no-padding">
              <div ui-sortable="{ axis: 'x,y', placeholder: 'album-thumbnail-sortable' }" ng-model="photos">
                <div class="album-thumbnail-sortable" ng-repeat="photo in photos">
                  <input type="hidden" name="album_photos_id[]" ng-value="ids[$index]"/>
                  <input type="hidden" name="album_photos_footer[]" ng-value="footers[$index]"/>
                  <div class="dynamic-image-placeholder">
                    <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo" transform="zoomcrop,220,220">
                      <div class="thumbnail-actions">
                        <div class="thumbnail-action remove-action" ng-click="removeItem('photos', $index)">
                          <i class="fa fa-trash-o fa-2x"></i>
                        </div>
                      </div>
                    </dynamic-image>
                  </div>
                  <div class="form-group">
                    <textarea class="album-thumbnail-description form-control" ng-model="footers[$index]"></textarea>
                  </div>
                </div>
              </div>
              <div class="album-thumbnail-placeholder">
                <div class="img-thumbnail">
                  <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="100" media-picker-target="photos">
                    <i class="fa fa-plus fa-2x"></i>
                    <h5>{t}Add images{/t}</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4" {if isset($album->cover_image) && $album->cover_image->id}ng-init="cover = {json_encode($album->cover_image)|replace:'"':'\''}"{/if}>
          <div class="grid simple">
            <div class="grid-title">
              <h4>{t}Cover image{/t}</h4>
            </div>
            <div class="grid-body">
              <div class="thumbnail-placeholder">
                <div class="img-thumbnail" ng-if="!cover">
                  <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="cover">
                    <i class="fa fa-picture-o fa-2x"></i>
                    <h5>Pick an image</h5>
                  </div>
                </div>
                <div class="dynamic-image-placeholder" ng-if="cover">
                  <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="cover" transform="thumbnail,220,220">
                    <div class="thumbnail-actions">
                      <div class="thumbnail-action remove-action" ng-click="removeImage('cover')">
                        <i class="fa fa-trash-o fa-2x"></i>
                      </div>
                      <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="cover" media-picker-types="photo">
                        <i class="fa fa-camera fa-2x"></i>
                      </div>
                    </div>
                  </dynamic-image>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <input type="hidden" name="album_frontpage_image" id="album_frontpage_image" ng-value="cover.id" />
      <input type="hidden" name="id" id="id" value="{$album->id|default:""}" />
    </div>
  </form>
  {include file="album/modals/_edit_album_error.tpl"}
{/block}
