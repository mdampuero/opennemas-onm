{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts src="@AdminTheme/js/onm/video.js" filters="uglifyjs" output="video"}
    <script>
      var video_manager_url = {
        get_information: '{url name=admin_videos_get_info}'
      }

      var localeAux = '{$smarty.const.CURRENT_LANGUAGE_SHORT|default:"en"}';
        localeAux = moment.locales().includes(localeAux) ?
          localeAux :
          'en';

      $('#starttime, #endtime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        useCurrent: false,
        minDate: '{$video->created|default:$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}',
        locale: localeAux
      });

      $("#starttime").on("dp.change",function (e) {
        $('#endtime').data("DateTimePicker").minDate(e.date);
      });
      $("#endtime").on("dp.change",function (e) {
        $('#starttime').data("DateTimePicker").maxDate(e.date);
      });

    </script>
  {/javascripts}
{/block}

{block name="content"}
<form action="{if isset($video)}{url name=admin_videos_update id=$video->id}{else}{url name=admin_videos_create}{/if}" method="POST" class="video-form" enctype="multipart/form-data" ng-controller="VideoCtrl" ng-init="init({json_encode($video)|clear_json}, {json_encode($locale)|clear_json}, {json_encode($tags)|clear_json})" id="formulario">
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
              <a href="{url name=admin_videos}" class="btn btn-link" title="{t}Go Back{/t}">
                <span class="fa fa-reply"></span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              {if isset($video->id)}
              {acl isAllowed="VIDEO_UPDATE"}
                <button class="btn btn-primary" data-text="{t}Updating{/t}..." type="submit" id="update-button">
                  <i class="fa fa-save"></i>
                  <span class="text">{t}Update{/t}</span>
                </button>
              {/acl}
              {else}
              {acl isAllowed="VIDEO_CREATE"}
                <button class="btn btn-primary" data-text="{t}Saving{/t}..." type="submit" id="save-button">
                  <i class="fa fa-save"></i>
                  <span class="text">{t}Save{/t}</span>
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
            {acl isAllowed="VIDEO_AVAILABLE"}
            <div class="form-group">
              <div class="checkbox">
                <input id="content_status" name="content_status" {if $video->content_status eq 1}checked="checked"{/if}  value="1" type="checkbox"/>
                <label for="content_status">
                  {t}Published{/t}
                </label>
              </div>
            </div>
            {/acl}
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
                  <select id="fk_author" name="fk_author" required>
                    <option value="" {if empty($opinion->fk_author)}selected{/if}>{t}Select an author...{/t}</option>
                    {foreach from=$authors item=author}
                      <option value="{$author->id}" {if $video->fk_author eq $author->id}selected{/if}>{$author->name}</option>
                    {/foreach}
                  </select>
                {aclelse}
                  {if !isset($video->fk_author) || empty($video->fk_author)}
                    {$app.user->name}
                    <input type="hidden" name="fk_author" value="{$app.user->id}">
                  {else}
                    {$authors[$video->fk_author]->name}
                    <input type="hidden" name="fk_author" value="{$video->fk_author}">
                  {/if}
                {/acl}
              </div>
            </div>
            <div class="form-group">
              <label for="tag_ids" class="form-label">{t}Tags{/t}</label>
              <div class="controls">
                <onm-tag ng-model="tag_ids" locale="locale" tags-list="tags" check-new-tags="newAndExistingTagsFromTagList" get-suggested-tags="getSuggestedTags" load-auto-suggested-tags="loadAutoSuggestedTags" suggested-tags="suggestedTags" placeholder="{t}Write a tag and press Enter...{/t}"/>
              </div>
            </div>
            {if isset($video)}
            <div class="form-group">
              <span class="help">
                {t}URL{/t}: <a href="{$smarty.const.SITE_URL}{$video->uri}" target="_blank">{t}Video{/t} <span class="fa fa-external-link"></span></a>
              </span>
            </div>
            {/if}
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="grid simple">
              <div class="grid-title">
                <h4>{t}Schedule{/t}</h4>
              </div>
              <div class="grid-body">
                <div class="form-group">
                  <label class="form-label" for="starttime">
                    {t}Publication start date{/t}
                  </label>
                  <div class="controls">
                    <div class="input-group">
                      <input class="form-control" id="starttime" name="starttime" type="datetime" value="{if $video->starttime neq '0000-00-00 00:00:00'}{$video->starttime}{/if}">
                      <span class="input-group-addon add-on">
                        <span class="fa fa-calendar"></span>
                      </span>
                    </div>
                    <span class="help-block">
                      {t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="endtime">
                    {t}Publication end date{/t}
                  </label>
                  <div class="controls">
                    <div class="input-group">
                      <input class="form-control" id="endtime" name="endtime" type="datetime" value="{if $video->endtime neq '0000-00-00 00:00:00'}{$video->endtime}{/if}">
                      <span class="input-group-addon add-on">
                        <span class="fa fa-calendar"></span>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        {is_module_activated name="CONTENT_SUBSCRIPTIONS"}
        <div class="row">
          <div class="col-md-12">
            <div class="grid simple">
              <div class="grid-title">
                <h4>{t}Subscription{/t}</h4>
              </div>
              <div class="grid-body">
                <div class="checkbox">
                  <input {if (is_array($video->params) && $video->params["only_registered"] == "1")}checked=checked{/if} id="only_registered" name="params[only_registered]" type="checkbox" value="1">
                  <label for="only_registered">
                    {t}Only available for registered users{/t}
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
        {/is_module_activated}
        {if $type == "script" || $type == "external" || (isset($video) && ($video->author_name == 'script' || $video->author_name == 'external'))}
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Image assigned{/t}</h4>
          </div>
          <div class="grid-body">
            <div class="row">
              <div class="col-md-12" {if isset($video->thumb_image)}ng-init="thumbnail = {json_encode($video->thumb_image)|clear_json}"{/if}>
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
