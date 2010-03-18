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
                    <img src="{$videos[i]->thumbnail_medium}" alt="{$videos[i]->title|clearslash|escape:'html'}"/>
                    <div class="bar-video-tiny-info"></div>
                    <div class="bar-video-tiny-info-image-video"><img src="{$params.IMAGE_DIR}video/trailersPlayArrow.gif" /></div>
                </div>
                <div class="info-interested-video">
                    <div class="category">{$category_data.title}</div>
                    <div class="caption">{$videos[i]->title|clearslash|escape:'html'}</div>
                </div>
            </div>
        {/section}
 
</div>