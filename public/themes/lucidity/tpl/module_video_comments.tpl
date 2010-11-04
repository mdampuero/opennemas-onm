{*
    OpenNeMas project

    @theme      Lucidity
*}
<div class="list-comments span-8 opacity-reduced">
    <div class="article-comments">
        <div class="title-comments"><h3><span>Comentarios</span></h3></div>
    </div>
    {foreach name=a item=content from=$lasts_comments}
        <div class="comment-wrapper clearfix">
            <div class="comment-number">{$smarty.foreach.a.iteration} </div>
            <div class="comment-content span-7 prepend-1">
                <a class="comment" href="{$content.permalink}#module_comments"> {$content.comment|clearslash}</a>       
                <a href="{$content.permalink}"> {$content.title|clearslash}</a>
            </div>
             
        </div>
    {/foreach}
    
</div>