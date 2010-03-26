{*
    OpenNeMas project

    @theme      Lucidity
*}

<div class="other-interested-videos border-dotted">
    <h3>Otros vídeos en esta categoría</h3>
    <hr class="new-separator"/>
    <div class="clean-paginator">1 de 2 | <a href="#" title="Ir al siguiente">Siguiente »</a></div>

        {section name=i loop=$videos}             
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
                    <div class="category">{$videos[i]->category_title}</div>
                    <div class="caption">
                        <a class="video-link" title="{$videos[i]->title|clearslash|escape:'html'}" href="{$videos[i]->permalink}">{$videos[i]->title|clearslash|escape:'html'}</a>
                    </div>
                </div>
            </div>
        {/section}
 
</div>