<div id="articles_category_available" class="content-provider-block">
    {foreach from=$articles item=content name=article_loop}
        {include file="article/content-provider/article.tpl"}
    {/foreach}
</div>
<div class="pagination">
    <ul>
        <li><a href="#" title="Go to the next contents">Next »</a></li>
        <li><a href="#" title="Go to the previous contents">« Previous</a></li>
    </ul><!-- / -->
</div><!-- / -->