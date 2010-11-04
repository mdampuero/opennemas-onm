{*
    OpenNeMas project

    @theme      Lucidity
*}

<div class="widget-video-most">
    <h3>Otros vídeos interesantes</h3>
    <hr class="new-separator"/>
    <div class="row-video">
        {section name=i loop=$others_videos}
             <div class="thumb-video clearfix">
                <a class="video-link" title="{$others_videos[i]->title|clearslash|escape:'html'}" href="{$others_videos[i]->permalink}">
                    {if $others_videos[i]->author_name eq 'vimeo'}
                        <img src="{$others_videos[i]->thumbnail_small}" alt="{$others_videos[i]->title|clearslash|escape:'html'}" title="{$others_videos[i]->title|clearslash|escape:'html'}" />
                    {else}
                         <img src="http://i4.ytimg.com/vi/{$others_videos[i]->videoid}/default.jpg" alt="{$others_videos[i]->title|clearslash|escape:'html'}" title="{$others_videos[i]->title|clearslash|escape:'html'}"  />
                    {/if}
                </a>
                <div class="video-shortitle">
                    <a href="/video/{$others_videos[i]->category_name}/" title="{$others_videos[i]->category_title}">{$others_videos[i]->category_title}</a>
                </div>
                <div class="video-title">
                     <a href="{$others_videos[i]->permalink}" title="{$others_videos[i]->title|clearslash|escape:'html'}">{$others_videos[i]->title|clearslash|escape:'html'}</a>
                 </div>
            </div>
        {/section}
    </div>
</div>
