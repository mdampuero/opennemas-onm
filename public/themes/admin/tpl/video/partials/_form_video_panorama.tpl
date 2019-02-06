<div class="contentform-main">
  <div class="form-group">
    <label for="video_url" class="form-label">{t}Video URL{/t}</label>
    <div class="controls">
      <div class="input-group">
        <input type="text" id="video_url" name="video_url" ng-model="video_url" value="{$video->video_url|default:""}" required class="form-control" />
        <span class="input-group-btn">
          <button class="btn btn-primary" id="video_url_button" type="button" ng-click="getVideoData()" ng-disabled="!video_url">
            <span class="fa fa-refresh"></span>
            <span class="hidden-xs">{t}Get information{/t}</span>
          </button>
        </span>
      </div>
      <div class="input-append"></div>
      {if !$video}
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
      {/if}
    </div>
  </div>
  <div id="video-information">
    <div class="listing-no-contents" ng-if="loading_data">
      <div class="text-center p-b-15 p-t-15">
        <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
        <h3 class="spinner-text">{t}Loading{/t}...</h3>
      </div>
    </div>
    <div class="ng-cloak" ng-if="information && !loading_data">
      {include file="video/partials/_video_information.tpl"}
    </div>
  </div>
</div>
