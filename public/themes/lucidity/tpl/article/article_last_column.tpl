{*
    OpenNeMas project
    @theme    Lucidity
*}
<div class="layout-column last-column last span-8">
    <div class="border-dotted">
       {include file="ads/widget_ad_column.tpl" type='103' nocache}
       {if !empty($videoInt)}
          <hr class="new-separator"/>
          {include file="video/widget_column_video_viewer.tpl" video=$videoInt}
       {/if}
       <hr class="new-separator"/>
       {if !empty($other_news)}
       <div class="news-highliter">
           <h3>Destacadas en {$actual_category_title|default:'Portada'|capitalize}</h3>
            {include file="frontpage/frontpage_article_head.tpl" item=$other_news[0]}
            {include file="frontpage/frontpage_article_head.tpl" item=$other_news[1]}
        </div>
        {/if}
    </div>
    {include file="widget_facebook_iframe.tpl"}
    <hr class="new-separator" />
    {include file="widget_headlines_past.tpl"}
    <hr class="new-separator"/>
    {include file="ads/widget_ad_column.tpl" type='105' nocache}
    <hr class="new-separator" />
</div>
