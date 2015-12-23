<div id="albums_available" class="content-provider-block">
  {foreach from=$albums item=content name=album_loop}
    {include file="album/content-provider/album.tpl"}
  {/foreach}
</div>
<div class="pagination-wrapper">
  {$pagination}
</div>
