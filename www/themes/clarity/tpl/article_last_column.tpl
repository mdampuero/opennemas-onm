{*
    OpenNeMas project

    @theme      Lucidity
*}

<div class="layout-column last-column last span-8">
    {include file="widget_TA_buttons.tpl"}

    {include file="widget_ad_column.tpl" type='103'}

    <hr class="new-separator"/>
    {include file="widget_column_video.tpl" video=$videoInt}

    <hr class="new-separator"/>

    <div class="inner-news-highligther">
        <h3 class="widget-title">Destacadas en deportes <img src="images/bullets/bars-red.png" /></h3>
        {include file="article_inner_column.tpl" item=$other_news[0]}
        {include file="article_inner_column.tpl" item=$other_news[1]}
    </div>

    {include file="widget_headlines_past.tpl"}

    <hr class="new-separator" />

    {include file="widget_ad_column.tpl" type='105'}
    
 
</div>