{*
    OpenNeMas project

    @theme      Lucidity
*}
<div class="other-interested-videos border-dotted">
    <h3>Otros vídeos interesantes</h3>
    <hr class="new-separator"/>
    {section name=i loop=$others_videos max=3}
         <div class="interested-video opacity-reduced">
            <div class="capture">
                <a class="video-link" title="{$others_videos[i]->title|clearslash|escape:'html'}" href="{$others_videos[i]->permalink}">
                    {if $others_videos[i]->author_name eq 'vimeo'}
                         <img src="{$others_videos[i]->thumbnail_medium}" alt="{$others_videos[i]->title|clearslash|escape:'html'}"  title="{$others_videos[i]->title|clearslash|escape:'html'}" />
                    {else}
                         <img src="http://i4.ytimg.com/vi/{$others_videos[i]->videoid}/default.jpg" alt="{$others_videos[i]->title|clearslash|escape:'html'}" title="{$others_videos[i]->title|clearslash|escape:'html'}"  />
                    {/if}
                </a>
                <div class="bar-video-tiny-info"></div>
                <div class="bar-video-tiny-info-image-video">
                    <a href="{$others_videos[i]->permalink}" title="{$others_videos[i]->title|clearslash|escape:'html'}"><img src="{$params.IMAGE_DIR}video/trailersPlayArrow.gif" /></a>
                </div>
            </div>
            <div class="info-interested-video">
                <div class="category">{$others_videos[i]->category_title}</div>
                <div class="caption">
                    <a class="video-link" title="{$others_videos[i]->title|clearslash|escape:'html'}" href="{$videos[i]->permalink}">{$others_videos[i]->title|clearslash}</a>
                </div>
            </div>
        </div>
    {/section}
</div>