{*
    OpenNeMas project

    @theme      Lucidity
*}


 <div class="other-interested-videos border-dotted">
    <h3>Otros vídeos interesantes</h3>
    <hr class="new-separator"/>
    <div class="clean-paginator">1 de 9 | <a href="#" title="Ir al siguiente">Siguiente »</a></div>

    {section name=i loop=$others_videos}
         <div class="interested-video opacity-reduced">
            <div class="capture">
                <img src="{$others_videos[i]->thumbnail_small}" alt="{$others_videos[i]->title}"/>
                <div class="bar-video-tiny-info"></div>
                <div class="bar-video-tiny-info-image-video"><img src="{$params.IMAGE_DIR}video/trailersPlayArrow.gif" /></div>
            </div>
            <div class="info-interested-video">
                <div class="category">{$others_videos[i]->category_name}</div>
                <div class="caption">{$others_videos[i]->title}</div>
            </div>
        </div>
    {/section}
     
</div>
