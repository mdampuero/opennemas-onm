<h3>Not implemented yet</h3>

<p>
    <form action="/admin/controllers/search_advanced/search_advanced.php" method="get" accept-charset="utf-8">

        <label for="stringSearch">Write a text for search</label>
        <input type="text" name="stringSearch" id="stringSearch" value="" placeholder="{t}Latest news in Spain...{/t}">

        <button type="submit" class="btn">{t}Search{/t}</button>

    </form>
</p>
{if $dataSent}
<div id="videos_available" class="content-provider-block">
    {foreach from=$contents item=content name=content_loop}
        {include file="video/content-provider/video.tpl"}
    {/foreach}
</div>
<div class="pagination clearfix">
    {$pager->links}
</div><!-- / -->
{/if}
