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
    <div class="row">

      <div class="col-md-4 col-md-push-8">
        <div class="grid simple">
          <div class="grid-body no-padding">
            <div class="grid-collapse-title">
              {acl isAllowed="VIDEO_AVAILABLE"}
                {include file="ui/component/content-editor/accordion/published.tpl"}
              {/acl}
              {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
            </div>

            {include file="ui/component/content-editor/accordion/author.tpl" required=true}
            {include file="ui/component/content-editor/accordion/category.tpl"}
            {include file="ui/component/content-editor/accordion/tags.tpl"}
            {include file="ui/component/content-editor/accordion/slug.tpl" route="[% getL10nUrl(routing.generate('frontend_video_show', { slug: item.slug })) %]"}
            {include file="ui/component/content-editor/accordion/scheduling.tpl"}

          </div>
        </div>
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
                    <input type="hidden" name="information[thumbnail]" ng-value="thumbnail.pk_photo"/>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        {/if}
      </div>
      <div class="col-md-8 col-md-pull-4">
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
    </div>
    {* <div class="form-vertical video-edit-form">
      <input type="hidden" name="type" value="{$smarty.get.type}">
      <input type="hidden" name="id" id="id" value="{$video->id|default:""}" />
    </div> *}
  </div>


  <script type="text/ng-template" id="modal-select-type">
    {include file="video/partials/modal.select_type.tpl"}
  </script>

</form>
{/block}
