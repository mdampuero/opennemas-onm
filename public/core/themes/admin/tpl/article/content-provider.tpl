<div id="articles_available" class="content-provider-block">
  {foreach $articles as $content}
    {include file="article/content-provider/article.tpl"}
  {/foreach}
</div>
<div class="pagination-wrapper">
  {$pagination}
</div>
