{*
    OpenNeMas project

    @theme      Lucidity
*}

<div class="layout-column last-column last span-8">
    <div class="border-dotted">

       {include file="widget_ad_column.tpl" type='103'}

       <hr class="new-separator"/>
       
       {include file="widget_column_video_viewer.tpl" video=$videoInt}

        <hr class="new-separator"/>

        <div class="news-highliter">
           <h3>Destacadas en {$category_data.title|capitalize}</h3>
            {include file="frontpage_article.tpl" item=$other_news[0]}
            {include file="frontpage_article.tpl" item=$other_news[1]}
        </div>
    </div>
    {include file="widget_headlines_past.tpl"}
    <hr class="new-separator"/>
    {include file="widget_ad_column.tpl" type='105'}

    <hr class="new-separator" />
</div>