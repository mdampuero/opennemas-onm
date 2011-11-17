<div id="suggested_articles_homepage" class="content-provider-block">
    {foreach from=$suggestedArticles item=content name=content_loop}
        {include file="frontpagemanager/content-types/article.tpl"}    
    {/foreach}
</div>