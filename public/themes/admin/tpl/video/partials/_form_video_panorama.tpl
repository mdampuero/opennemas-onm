<div class="contentform-main">
  <div class="form-group">
    <label for="video_url" class="form-label">{t}Video URL{/t}</label>
    <div class="controls">
      <div class="input-group">
        <input type="text" id="video_url" name="video_url" value="{$video->video_url|default:""}" required="required" class="form-control" />
        <span class="input-group-btn">
          <button class="btn btn-primary" id="video_url_button" type="button">
            <span class="fa fa-refresh"></span>
            <span class="hidden-xs">{t}Get information{/t}</span>
          </button>
        </span>
      </div>
      <div class="input-append"></div>
      {if !$video}
        {javascripts}
          <script type="text/javascript" defer="defer">
            jQuery(document).ready(function($) {
              jQuery('#video_url').popover({
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
    {if isset($video)}
      {include file="video/partials/_video_information.tpl"}
    {/if}
  </div>
</div>
