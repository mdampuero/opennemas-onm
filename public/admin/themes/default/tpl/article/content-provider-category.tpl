<div id="articles_category_available" class="content-provider-block">
    {foreach from=$articles item=content name=article_loop}
        {include file="article/content-provider/article.tpl"}
    {/foreach}
</div>
<div class="pagination clearfix">
    {$pager->links}
</div><!-- / -->