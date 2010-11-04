{*
    OpenNeMas project
    @theme    Lucidity
*}
<div class="layout-column last-column last span-8">
    <div class="border-dotted">
       {include file="ads/widget_ad_column.tpl" type='103'}
       {if !empty($videoInt)}
          <hr class="new-separator"/>
          {include file="video/widget_column_video_viewer.tpl" video=$videoInt}
       {/if}
        
    </div>
    {include file="widget_facebook_iframe.tpl"}
    <hr class="new-separator" />
    {include file="widget_headlines_past.tpl"}
    <hr class="new-separator"/>
    {include file="ads/widget_ad_column.tpl" type='105'}
    <hr class="new-separator" />
</div>