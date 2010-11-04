{*
    OpenNeMas project

    @theme      Lucidity
*}
 
<div class="layout-column last-column span-12 last">
    <div class="photos-highlighter clearfix span-12">
        <div class="photos-header"><a href="/video/" title="Ir a la secci&oacute;n de galer&iacute;s"><img src="{$params.IMAGE_DIR}widgets/videos-highlighter-header.png" alt=""/></a></div>
        <div class="photos-highlighter-big clearfix">
            <div class="video-big">
                <a href="{$videos[0]->permalink}" title="{$videos[0]->title|clearslash|escape:'html'}">
                {if $videos[0]->author_name eq 'youtube'}
                    <img  alt="{$videos[0]->title|clearslash|escape:'html'}" width="300" height="240" title="{$videos[0]->title|clearslash|escape:'html'}" src="http://i4.ytimg.com/vi/{$videos[0]->videoid}/default.jpg" />
                {elseif $videos[0]->author_name eq 'vimeo'}
                    <img  alt="{$videos[0]->title|clearslash|escape:'html'}" width="300" height="240" title="{$videos[0]->title|clearslash|escape:'html'}" src="{$videos[0]->thumbnail_medium}" />
                {else}
                    <img  alt="{$videos[0]->title|clearslash|escape:'html'}" width="300" height="240" title="{$videos[0]->title|clearslash|escape:'html'}" src="{$params.IMAGE_DIR}default_video.jpg" />
                {/if}
                <div class="video-player-marker"><img src="{$params.IMAGE_DIR}/video/video-player.png" /></div>
                </a>
            </div>
            
            <div class="info"><a href="{$videos[0]->permalink}" title="{$videos[0]->title|clearslash|escape:'html'}">
                 {$videos[0]->title|clearslash|escape:'html'} </a>
            </div>
        </div>
        <ul class="photos-highligher-little-section-links">
            {section start=1 name=i loop=$videos max=3}
                {if $smarty.section.i.first}
                    <li  class="first"><a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">
                    {elseif $smarty.section.i.last}
                            <li class="last"><a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">
                    {else}
                             <li class=""><a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">
                    {/if}
                        {if $videos[i]->author_name eq 'youtube'}
                             <img  alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg" />
                        {elseif $videos[i]->author_name eq 'vimeo'}
                              <img  alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="{$videos[i]->thumbnail_small}" />
                        {else}
                              <img  alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="{$params.IMAGE_DIR}default_video.jpg" />

                        {/if}
                    </a></li>
            {/section}
        </ul>
    </div>
</div> 
