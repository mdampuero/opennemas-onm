

<div id="main_content" class="single-article span-24 portada-videos">

    <div class="layout-column first-column span-8 featured-videos">
        <h3>{if !empty($subcategory_real_name)} {$subcategory_real_name}{else} {$category_real_name}{/if}:: vídeos </h3>
        <hr class="new-separator"/>
            {* step para que no coincidad con los del widget incategory*}
            {section name=i loop=$videos step=2}
                {include file="video/widget_video_viewer.tpl" video=$videos[i]}
            {/section}
        



    </div>
    <div class="layout-column last-column last span-16 ">
        <div class="span-16" id="videos_incategory">
           {include file="video/widget_video_incategory.tpl"}

        </div>
        <div class="span-16" id="videos_more">
           {include file="video/widget_video_more.tpl"}
        </div>
        <div class="span-16">
            <div class="span-8 opacity-reduced">
                <div class="article-comments">
                        <div class="title-comments"><h3><span>Más vídeos</span></h3></div>
                </div>
               {include file="video/widget_videos_lastest.tpl"}
            </div>
            <div class="span-8 last">
                {include file="module_video_comments.tpl"}
            </div>

        </div>
    </div>
</div><!-- fin #main_content -->
