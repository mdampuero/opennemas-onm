{extends file="base/admin.tpl"}

{block name="header-css" append}
{stylesheets src="@AdminTheme/css/parts/specials.css" filters="cssrewrite"}
<link rel="stylesheet" href="{$asset_url}">
{/stylesheets}
<style>
  .thumbnails>li {
    margin:0;
  }
  .thumbnails {
    margin:0;
  }
</style>
{/block}

{block name="footer-js" append}
{javascripts src="@AdminTheme/js/onm/content-provider.js"}
<script type="text/javascript" src="{$asset_url}"></script>
{/javascripts}
<script>
  $(document).ready(function($){
    $('#formulario').on('submit', function(e, ui) {
      var els = [];
      $('#column_right').find('ul.content-receiver li').each(function (index, item) {
        els.push({
          'id' : $(item).data('id'),
          'content_type': $(item).data('type'),
          'position': index
        });
      });

      $('#noticias_right_input').val(JSON.stringify(els));

      els = [];

      $('#column_left').find('ul.content-receiver li').each(function (index, item) {
        els.push({
          'id' : $(item).data('id'),
          'content_type': $(item).data('type'),
          'position': index
        });
      });

      $('#noticias_left_input').val(JSON.stringify(els));
    });

    $('#title').on('change', function(e, ui) {
      fill_tags($('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
    });
  });
</script>

{/block}

{block name="content"}
<form action="{if $special->id}{url name=admin_special_update id=$special->id}{else}{url name=admin_special_create}{/if}" method="post" ng-controller="SpecialCtrl">

  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-star"></i>
              {t}Specials{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs">
            <h5>{if !isset($special->id)}{t}Creating special{/t}{else}{t}Editing special{/t}{/if}</h5>
          </li>
        </ul>
      </div>

      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li>
            <a class="btn btn-link" href="{url name=admin_specials category=$category}">
              <span class="fa fa-reply"></span>
            </a>
          </li>
          <li class="quicklinks"><span class="h-seperate"></span></li>
          <li class="quicklinks">
            {if !is_null($special->id)}
            {acl isAllowed="SPECIAL_UPDATE"}
            <button type="submit" class="btn btn-primary">
              <span class="fa fa-save"></span>
              {t}Update{/t}
            </button>
            {/acl}
            {else}
            {acl isAllowed="SPECIAL_CREATE"}
            <button type="submit" class="btn btn-primary">
              <span class="fa fa-save"></span>
              {t}Save{/t}
            </button>
            {/acl}
            {/if}
          </li>
        </ul>
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
              <label for="title" class="form-label">{t}Title{/t}</label>
              <div class="controls">
                <input type="text" id="title" name="title" required="required" class="form-control"
                value="{$special->title|clearslash|escape:"html"}"/>
              </div>
            </div>

            <div class="form-group">
              <label for="subtitle" class="form-label">{t}Subtitle{/t}</label>
              <div class="controls">
                <input type="text" id="subtitle" name="subtitle" class="form-control" value="{$special->subtitle|clearslash|escape:"html"}" />
              </div>
            </div>
            <div class="form-group">
              <label for="description" class="form-label">{t}Description{/t}</label>
              <div class="controls">
                <textarea name="description" id="description" onm-editor onm-editor-preset="simple">{$special->description|clearslash}</textarea>
              </div>
            </div>

          </div>
        </div>

        {include file="special/partials/_contents_containers.tpl"}
      </div>
      <div class="col-md-4">
        <div class="grid simple">
          <div class="grid-body">
            <div class="form-group">
              <div class="checkbox">
                <input type="checkbox" name="content_status" id="content_status" value="1" {if $special->content_status eq 1} checked="checked"{/if}>
                <label for="content_status" class="form-label">
                  {t}Published{/t}
                </label>
              </div>
            </div>
            <div class="form-group">
              <label for="category" class="form-label">{t}Category{/t}</label>
              <div class="controls">
                {include file="common/selector_categories.tpl" name="category" item=$special}
              </div>
            </div>
            <div class="form-group">
              <label for="metadata" class="form-label">{t}Tags{/t}</label>
              <span class="help">{t}List of words separated by commas.{/t}</span>
              <div class="controls">
                <input  data-role="tagsinput" id="metadata" name="metadata" required="required" type="text" value="{$special->metadata|clearslash|escape:"html"}"/>
              </div>
            </div>
            <div class="form-group">
              <label for="slug" class="form-label">{t}Slug{/t}</label>
              <div class="controls">
                <input  type="text" id="slug" name="slug" class="form-control"
                value="{$special->slug|clearslash|escape:"html"}" />
              </div>
            </div>
          </div>
        </div>

        {acl isAllowed='PHOTO_ADMIN'}
        {is_module_activated name="IMAGE_MANAGER"}
        <div class="grid simple">
          <div class="grid-title">{t}Image for Special{/t}</div>
          <div class="grid-body">

            <div class="col-md-12" {if isset($photo1) && $photo1->name}ng-init="photo1 = {json_encode($photo1)|replace:'"':'\''}"{/if}>
              <div class="form-group">
                <div class="thumbnail-placeholder">
                  <div class="img-thumbnail" ng-if="!photo1">
                    <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1">
                      <i class="fa fa-picture-o fa-2x"></i>
                      <h5>{t}Pick an image{/t}</h5>
                    </div>
                  </div>
                  <div class="dynamic-image-placeholder" ng-if="photo1">
                    <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="photo1" transform="thumbnail,220,220">
                    <div class="thumbnail-actions">
                      <div class="thumbnail-action remove-action" ng-click="removeImage('photo1')">
                        <i class="fa fa-trash-o fa-2x"></i>
                      </div>
                      <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1">
                        <i class="fa fa-camera fa-2x"></i>
                      </div>
                    </div>
                    <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="photo1" media-picker-type="photo"></div>
                  </dynamic-image>
                </div>
              </div>
            </div>
            {*
            <ul class="related-images thumbnails">
              <li class="contentbox frontpage-image {if isset($photo1) && $photo1->name}assigned{/if}">
                <div class="content">
                  <div class="image-data">
                    <a href="#media-uploader" data-toggle="modal" data-position="frontpage-image" class="image thumbnail">
                      <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo1->path_file}{$photo1->name}"/>
                    </a>
                    <input type="hidden" name="img1" value="{$special->img1|default:""}" class="related-element-id" />
                  </div>

                  <div class="not-set">
                    {t}Image not set{/t}
                  </div>

                  <div class="btn-group">
                    <a href="#media-uploader" data-toggle="modal" data-position="frontpage-image" class="btn btn-small">{t}Set image{/t}</a>
                    <a href="#" class="unset btn btn-small btn-danger"><i class="fa fa-trash"></i></a>
                  </div>
                </div>
              </li>
            </ul>
            *}
          </div>
        </div>
        {/is_module_activated}
        {/acl}

      </div>
    </div>

  </div>
  <input type="hidden" id="noticias_right_input" name="noticias_right_input" value="">
  <input type="hidden" id="noticias_left_input" name="noticias_left_input" value="">

</form>
{/block}
