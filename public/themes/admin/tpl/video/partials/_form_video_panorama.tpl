<div class="form-group no-margin">
  <label for="video_url" class="form-label">{t}Video URL{/t}</label>
  <div class="controls">
    <div class="input-group">
      <input type="text" id="video_url" name="video_url" ng-model="item.path" required class="form-control" />
      <span class="input-group-btn">
        <button class="btn btn-primary" id="video_url_button" type="button" ng-click="getVideoData()" ng-disabled="!item.path">
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
      <i class="fa fa-3x fa-circle-o-notch fa-spin text-info"></i>
      <h5 class="spinner-text">{t}Loading video information{/t}...</h5>
    </div>
  </div>
  <div class="ng-cloak" ng-show="item.information.service && !flags.http.fetch_video_info">
    {include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
    {include file="ui/component/content-editor/textarea.tpl" title="{t}Description{/t}" field="description" rows=5 imagepicker=true}
    <label for="preview" class="form-label">
      {t}Video preview{/t}
    </label>
    <div class="row">
      <div class="col-md-6">
        <div class="thumbnail center" ng-if="item">
          <div class="video-preview" ng-bind-html="trustHTML(item.information.embedHTML)"></div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <div class="form-label">
            <strong>
              {t}Original Title{/t}
            </strong>
          </div>
          <div class="controls">
            [% item.information.title %]
          </div>
        </div>
        <div class="form-group">
          <div class="form-label">
            <strong>
              {t}Service{/t}
            </strong>
          </div>
          <div class="controls">
            [% item.information.service %]
          </div>
        </div>
        <div class="form-group">
          <div class="form-label">
            <strong>
              {t}Embed Url{/t}
            </strong>
          </div>
          <div class="controls">
            <a href="[% item.information.embedUrl %]">
              [% item.information.embedUrl %]
            </a>
          </div>
        </div>
        <div class="form-group">
          <div class="form-label">
            <strong>
              {t}Thumbnail{/t}
            </strong>
          </div>
          <div class="controls">
            <img ng-src="[% item.information.thumbnail %]" ng-if="item.information.thumbnail" width="100">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
