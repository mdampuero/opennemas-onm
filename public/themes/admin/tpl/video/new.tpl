{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
  .utilities-conf {
    position:absolute;
    top:0;
    right:0;
  }
</style>
{/block}

{block name="footer-js" append}
<script type="text/javascript">
  var video_manager_url = {
    get_information: '{url name=admin_videos_get_info}',
    fill_tags: '{url name=admin_utils_calculate_tags}'
  }

  $('#title').on('change', function(e, ui) {
    fill_tags($('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
  });
</script>
{javascripts src="@AdminTheme/js/onm/video.js"}
<script type="text/javascript" src="{$asset_url}"></script>
{/javascripts}
{/block}

{block name="content"}
<form action="{if isset($video)}{url name=admin_videos_update id=$video->id}{else}{url name=admin_videos_create}{/if}" method="POST" class="video-form" enctype="multipart/form-data" ng-controller="InnerCtrl">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-home fa-lg"></i>
              {t}Videos{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs">
            <h5>{if !isset($video)}{t}Creating video{/t}{else}{t}Editing video{/t}{/if}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a href="{url name=admin_videos category=$category|default:""}" class="btn btn-link" title="{t}Go Back{/t}">
                <span class="fa fa-reply"></span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              {if isset($video->id)}
              {acl isAllowed="VIDEO_UPDATE"}
              <button class="btn btn-primary" type="submit">
                <span class="fa fa-save"></span>
                {t}Save{/t}
              </button>
              {/acl}
              {else}
              {acl isAllowed="VIDEO_CREATE"}
              <button class="btn btn-primary" type="submit">
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
  </div>

  <div class="content">

    {render_messages}
    <div class="row">
      <div class="col-md-8">
        <div class="grid simple">
          <div class="grid-body">
            {if $type == "external" || (isset($video) && $video->author_name == 'external')}
              {include file="video/partials/_form_video_external.tpl"}
            {elseif $type == "script" || (isset($video) && $video->author_name == 'script')}
              {include file="video/partials/_form_video_script.tpl"}
            {else}
              {include file="video/partials/_form_video_panorama.tpl"}
            {/if}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="grid simple">
          <div class="grid-title">{t}Attributes{/t}</div>
          <div class="grid-body">

            <div class="form-group">
              <div class="checkbox">
                <input id="content_status" name="content_status" {if $video->content_status eq 1}checked="checked"{/if}  value="1" type="checkbox"/>
                <label for="content_status">
                  {t}Published{/t}
                </label>
              </div>
            </div>

            {is_module_activated name="COMMENT_MANAGER"}
            <div class="form-group">
              <div class="checkbox">
                  <input id="with_comment" name="with_comment" type="checkbox" {if (!isset($video) && (!isset($commentsConfig['with_comments']) || $commentsConfig['with_comments']) eq 1) || (isset($video) && $video->with_comment eq 1)}checked{/if} value="1" />
                <label for="with_comment">
                  {t}Allow comments{/t}
                </label>
              </div>
            </div>
            {/is_module_activated}

            <div class="form-group">
              <label for="category" class="form-label">{t}Category{/t}</label>
              <div class="controls">
                {include file="common/selector_categories.tpl" name="category" item=$video}
              </div>
            </div>

            <div class="form-group">
              <label for="fk_author" class="form-label">{t}Author{/t}</label>
              <div class="controls">
                {acl isAllowed="CONTENT_OTHER_UPDATE"}
                <select name="fk_author" id="fk_author">
                  {html_options options=$authors selected=$video->fk_author}
                </select>
                {aclelse}
                {if !isset($video->fk_author)}
                {$smarty.session.realname}
                <input type="hidden" name="fk_author" value="{$smarty.session.userid}">
                {else}
                {$authors[$video->fk_author]}
                <input type="hidden" name="fk_author" value="{$video->fk_author}">
                {/if}
                {/acl}
              </div>
            </div>
            <div class="form-group">
              <label for="metadata" class="form-label">{t}Tags{/t}</label>
              <div class="controls">
                <input data-role="tagsinput" type="text" id="metadata" name="metadata" placeholder="{t}Write a tag and press Enter...{/t}" required="required" value="{$video->metadata}" class="form-control" />
              </div>
            </div>
          </div>
        </div>

        {if $type == "script" || $type == "external" || (isset($video) && ($video->author_name == 'script' || $video->author_name == 'external'))}
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Image assigned{/t}</h4>
          </div>
          <div class="grid-body">
            <div class="row">
              <div class="col-md-12" {if isset($video->thumb_image)}ng-init="thumbnail = {json_encode($video->thumb_image)|replace:'"':'\''}"{/if}>
                <div class="form-group">
                  <div class="thumbnail-placeholder ng-cloak">
                    <div class="img-thumbnail" ng-if="!thumbnail">
                      <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="thumbnail" media-picker-type="photo">
                        <i class="fa fa-picture-o fa-2x"></i>
                        <h5>{t}Pick an image{/t}</h5>
                      </div>
                    </div>
                    <div class="dynamic-image-placeholder ng-cloak" ng-if="thumbnail">
                      <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="thumbnail" transform="thumbnail,220,220">
                      <div class="thumbnail-actions">
                        <div class="thumbnail-action remove-action" ng-click="removeImage('thumbnail')">
                          <i class="fa fa-trash-o fa-2x"></i>
                        </div>
                        <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="thumbnail" media-picker-type="photo">
                          <i class="fa fa-camera fa-2x"></i>
                        </div>
                      </div>
                      <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="thumbnail" media-picker-type="photo"></div>
                    </dynamic-image>
                    <input type="hidden" name="video_image" ng-value="thumbnail.id"/>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        {/if}
      </div>
    </div>
    <div class="form-vertical video-edit-form">

      <input type="hidden" name="type" value="{$smarty.get.type}">
      <input type="hidden" name="id" id="id" value="{$video->id|default:""}" />
    </div>
  </div>
</form>
{/block}
