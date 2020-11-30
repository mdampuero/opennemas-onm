<div id="videos_available" class="content-provider-block">
  {foreach from=$videos item=content name=video_loop}
    {include file="video/content-provider/video.tpl"}
  {/foreach}
</div>
<div class="pagination-wrapper">
  {$pagination}
</div>
