{*
    OpenNeMas project

    @theme      Lucidity
*}
<div class="main_video">
     


        <div id="video-content" class="clearfix span-8">
             {include file="video/widget_video_window.tpl" width="290" height="180"}
        </div>


     <div class="video-explanation">
        <h1><img src="{$params.IMAGE_DIR}utilities/share-black.png" alt="Share" />
            <a href="{$video->permalink}" title="{$video->title|clearslash|escape:'html'}">
                {$video->title|clearslash|escape:'html'}
            </a>
        </h1>
        <p class="in-subtitle">
            {$video->description|clearslash|escape:'html'}
        </p>
    </div>
</div><!-- .main-video -->