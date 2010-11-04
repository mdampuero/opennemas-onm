{*
    OpenNeMas project

    @theme      Lucidity
*}

<div id="videos_more" class="other-interested-videos border-dotted">
   <h3>Otros vídeos interesantes</h3>
   <hr class="new-separator"/>
    <div class="clean-paginator clearfix span-4">{*$page|default:'1'} de {$total_incategory} | *}
        <div class="buttons">
            <a href="#" onClick="videos_more({$actual_category_id},{$page-1});return false;" title="Ver anterior"><img alt="Ver anterior" src="{$params.IMAGE_DIR}/video-arrow-left.png" /></a>
            <a href="#" onClick="videos_more({$actual_category_id},{$page+1});return false;" title="Ver siguiente"><img alt="Ver siguiente" src="{$params.IMAGE_DIR}/video-arrow-right.png" /></a>
        </div>
    </div>
   {section name=i loop=$others_videos}
       
            <div class="interested-video opacity-reduced">
               <div class="capture">
                   <a class="video-link" title="{$others_videos[i]->title|clearslash|escape:'html'}" href="{$others_videos[i]->permalink}">
                       {if $others_videos[i]->author_name eq 'vimeo'}
                            <img class="image" src="{$others_videos[i]->thumbnail_medium}" alt="{$others_videos[i]->title|clearslash|escape:'html'}"  title="{$others_videos[i]->title|clearslash|escape:'html'}" />
                       {else}
                            <img class="image" src="http://i4.ytimg.com/vi/{$others_videos[i]->videoid}/default.jpg" alt="{$others_videos[i]->title|clearslash|escape:'html'}" title="{$others_videos[i]->title|clearslash|escape:'html'}"  />
                       {/if}
                   </a>
                   <div class="bar-video-tiny-info"></div>
                   <div class="bar-video-tiny-info-image-video">
                       <a href="{$others_videos[i]->permalink}" title="{$others_videos[i]->title|clearslash|escape:'html'}"><img src="{$params.IMAGE_DIR}video/trailersPlayArrow.gif" /></a>
                   </div>
               </div>
               <div class="info-interested-video">
                   <div class="category"><a href="/video/{$others_videos[i]->category_name}/" title="{$others_videos[i]->category_title}">{$others_videos[i]->category_title}</a></div>
                   <div class="caption">
                       <a class="video-link" title="{$others_videos[i]->title|clearslash|escape:'html'}" href="{$others_videos[i]->permalink}">{$others_videos[i]->title|clearslash}</a>
                   </div>
               </div>
           </div>

   {/section}

</div>
