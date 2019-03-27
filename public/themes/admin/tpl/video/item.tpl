{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts src="@AdminTheme/js/onm/video.js" filters="uglifyjs" output="video"}
    <script>
      var video_manager_url = {
        get_information: '{url name=admin_videos_get_info}'
      }
    </script>
  {/javascripts}
{/block}

{block name="content"}
<form name="form" ng-controller="VideoCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="{url name=backend_videos_list}">
                <i class="fa fa-quote-right"></i>
                  {t}Videos{/t}
              </a>
            </h4>
          </li>
          <li class="quicklinks m-l-5 m-r-5 ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
            <translator keys="data.extra.keys" ng-model="config.locale.selected" options="data.extra.locale"></translator>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <button class="btn btn-success text-uppercase" data-text="{t}Saving{/t}..." type="submit">
                <i class="fa fa-save"></i>
                <span class="text">{t}Save{/t}</span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="listing-no-contents" ng-hide="!flags.http.loading">
      <div class="text-center p-b-15 p-t-15">
        <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
        <h3 class="spinner-text">{t}Loading{/t}...</h3>
      </div>
    </div>
    <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && item === null">
      <div class="text-center p-b-15 p-t-15">
        <a href="[% routing.generate('backend_users_list') %]">
          <i class="fa fa-4x fa-warning text-warning"></i>
          <h3>{t}Unable to find the item{/t}</h3>
          <h4>{t}Click here to return to the list{/t}</h4>
        </a>
      </div>
    </div>

    <div class="row ng-cloak" ng-show="!flags.http.loading && !type">
      <h5>{t}Pick the method to add the video{/t}</h5>

      <ul class="video-type-selector">
          <li class="web">
            <button ng-click="type='web-source'" class="clearfix btn btn-white">
              <i class="fa fa-vimeo fa-lg"></i>
              <i class="fa fa-youtube fa-lg"></i>
              <div class="p-t-10">
                {t}Link video from other web video services{/t}
              </div>
            </button>
          </li>
          <li class="web">
            <button ng-click="type='script'" class="clearfix btn btn-white">
              <div class="p-t-10">
                <i class="fa fa-file-code-o fa-3x"></i>
                {t}Use HTML code{/t}
              </div>
            </button>
          </li>
          <li class="web">
            <button ng-click="type='external'" class="clearfix btn btn-white">
              <i class="fa fa-film fa-3x"></i>
              <div class="p-t-10">
                {t}Use file video URLs (External HTML5/FLV){/t}
              </div>
            </button>
          </li>
      </ul>
    </div>

    <div class="row ng-cloak" ng-show="!flags.http.loading && type && item">
      <div class="col-md-4 col-md-push-8">
        <div class="grid simple">
          <div class="grid-body no-padding">
            <div class="grid-collapse-title">
              {acl isAllowed="VIDEO_AVAILABLE"}
                {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Published{/t}" field="content_status"}
              {/acl}
              {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
            </div>

            {include file="ui/component/content-editor/accordion/author.tpl" required=true}
            {include file="ui/component/content-editor/accordion/category.tpl"}
            {include file="ui/component/content-editor/accordion/tags.tpl"}
            {include file="ui/component/content-editor/accordion/slug.tpl" route="[% getL10nUrl(routing.generate('frontend_video_show', { slug: item.slug, category_name: 'category' })) %]"}
            {include file="ui/component/content-editor/accordion/scheduling.tpl"}
          </div>
        </div>
        <div class="grid simple" ng-if="type == 'script' || type == 'external'">
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
                      <input type="hidden" name="information[thumbnail]" ng-value="thumbnail.pk_photo"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-8 col-md-pull-4">
        <div class="grid simple">
          <div class="grid-body">
            <span ng-show="type == 'external'">
              {include file="video/partials/_form_video_external.tpl"}
            </span>
            <span ng-show="type == 'script'">
              {include file="video/partials/_form_video_script.tpl"}
            </span>
            <span ng-show="type !== 'script' && type !== 'external'">
              {include file="video/partials/_form_video_panorama.tpl"}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

</form>
{/block}
