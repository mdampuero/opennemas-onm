<div id="articles_suggested_available" class="content-provider-block">
  {foreach from=$articles item=content name=article_loop}
    {include file="article/content-provider/article.tpl"}
  {/foreach}
</div>
<div class="pagination-wrapper">
  {$pagination}
</div>
