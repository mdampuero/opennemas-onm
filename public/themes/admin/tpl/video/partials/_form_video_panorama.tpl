<div class="form-group no-margin">
  <label for="video_url" class="form-label">{t}Video URL{/t}</label>
  <div class="controls">
    <div class="input-group">
      <input type="text" id="video_url" name="video_url" ng-model="item.video_url" required class="form-control" />
      <span class="input-group-btn">
        <button class="btn btn-primary" id="video_url_button" type="button" ng-click="getVideoData()" ng-disabled="!item.video_url">
          <span class="fa fa-refresh"></span>
          <span class="hidden-xs">{t}Get information{/t}</span>
        </button>
      </span>
    </div>
    <div class="input-append"></div>
      {javascripts}
        <script defer="defer">
          jQuery(document).ready(function($) {
            $('#video_url').popover({
              placement: 'bottom',
              trigger: 'hover',
              animation: true,
              delay:0,
              html : true,
              title: '{t}Allowed video sources:{/t}',
              content: '{include file="video/partials/_sourceinfo.tpl"}'
            });
          });
        </script>
      {/javascripts}
      {stylesheets}
        <style type="text/css">
          .popover { width:500px; }
        </style>
      {/stylesheets}
  </div>
</div>
<div id="video-information">
  <div class="listing-no-contents" ng-show="flags.http.fetch_video_info">
    <div class="text-center p-b-15 p-t-15">
      <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
      <h3 class="spinner-text">{t}Loading{/t}...</h3>
    </div>
  </div>
  <div class="ng-cloak" ng-show="item.information.service && !flags.http.fetch_video_info">
    {include file="ui/component/content-editor/input-text.tpl" title="{t}Title{/t}" field="title" required=true counter=true}
    {include file="ui/component/content-editor/textarea.tpl" title="{t}description{/t}" field="description" rows=5 imagepicker=true}

    <div class="form-group">
      <label for="preview" class="form-label">{t}Video preview{/t}</label>
      <div class="controls">
        <div class="thumbnail center">
          <div ng-bind-html="item.information.embedHTML" style="max-width:600px; overflow:hidden; margin:0 auto"></div>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label for="other_info" class="form-label">{t}Other information{/t}</label>
      <div class="controls">
        <table style="width:80%; margin:20xp;">
          <tr>
            <td width="200"><strong>{t}Original Title{/t}</strong></td>
            <td>[% item.information.title %]</td>
          </tr>
          <tr>
            <td><strong>{t}Service{/t}</strong></td>
            <td>[% item.information.service %]</td>
          </tr>
          <tr>
            <td><strong>{t}Duration{/t}</strong></td>
            <td>[% item.information.duration %]</td>
          </tr>
          <tr>
            <td><strong>{t}Embed Url{/t}</strong></td>
            <td><a href="[% item.information.embedUrl %]">[% item.information.embedUrl %]</a></td>
          </tr>
          <tr>
            <td><strong>{t}Thumbnail URL{/t}</strong></td>
            <td><a href="item.information.thumbnail" ng-if="item.information.thumbnail">[% item.information.thumbnail %]</a></td>
          </tr>
          <tr>
            <td><strong>{t}Thumbnail image{/t}</strong></td>
            <td><img ng-src="[% item.information.thumbnail %]" ng-if="item.information.thumbnail" width="100"></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
