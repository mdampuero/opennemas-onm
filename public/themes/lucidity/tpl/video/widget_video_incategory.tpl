{*
    OpenNeMas project

    @theme      Lucidity
*}

<div id="videos_incategory" class="other-interested-videos border-dotted">
    <h3>Otros vídeos en esta categoría</h3>
    <hr class="new-separator"/>
    <div class="clean-paginator clearfix span-4">{*$page|default:'1'} de {$total_incategory} | *}
        <div class="buttons">
            <a href="#" onClick="videos_incategory({$actual_category_id},{$page-1});return false;" title="Ver anterior"><img alt="Ver anterior" src="{$params.IMAGE_DIR}/video-arrow-left.png" /></a>
            <a href="#" onClick="videos_incategory({$actual_category_id},{$page+1});return false;" title="Ver siguiente"><img alt="Ver siguiente" src="{$params.IMAGE_DIR}/video-arrow-right.png" /></a>
        </div>
    </div>

    {section name=i loop=$videos}
        {if $smarty.section.i.first eq 1}
                  <div class="clearfix">
        {/if}
             <div class="interested-video opacity-reduced">
                <div class="capture">
                    <a class="video-link" title="{$videos[i]->title|clearslash|escape:'html'}" href="{$videos[i]->permalink}">
                        {if $videos[i]->author_name eq 'vimeo'}
                             <img class="image" src="{$videos[i]->thumbnail_medium}" alt="{$videos[i]->title|clearslash|escape:'html'}"  title="{$videos[i]->title|clearslash|escape:'html'}" />
                        {else}
                             <img class="image" src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg" alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}"  />
                        {/if}
                    </a>
                    <div class="bar-video-tiny-info"></div>
                    <div class="bar-video-tiny-info-image-video">
                        <a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}"><img src="{$params.IMAGE_DIR}video/trailersPlayArrow.gif" /></a>
                    </div>
                </div>
                <div class="info-interested-video">
                    <div class="category"><a href="/video/{$videos[i]->category_name}" title="{$videos[i]->category_title}">{$videos[i]->category_title}</a></div>
                    <div class="caption">
                        <a class="video-link" title="{$videos[i]->title|clearslash|escape:'html'}" href="{$videos[i]->permalink}">{$videos[i]->title|clearslash|escape:'html'}</a>
                    </div>
                </div>
            </div>

          {if (($smarty.section.i.iteration % 3) eq 0) && !($smarty.section.i.first eq 1)}
             </div>
             <div class="clearfix">
          {/if}
          {if ($smarty.section.i.last eq 1)}
             </div>
          {/if}
    {/section}
 
</div>