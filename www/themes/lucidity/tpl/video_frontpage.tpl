

<div id="main_content" class="single-article span-24 portada-videos">

    <div class="layout-column first-column span-8 featured-videos">
        <h3>Featured vídeos</h3>
        <hr class="new-separator"/>

            {section name=i loop=$videos}
             
                {include file="widget_video_viewer.tpl" video=$videos[i]}
            {/section}
        



    </div>
    <div class="layout-column last-column last span-16 ">
        <div class="span-16">
           {include file="widget_videos_category.tpl"}

        </div>
        <div class="span-16">
           {include file="widget_other_videos.tpl"}
        </div>
        <div class="span-16">
            <div class="span-8 opacity-reduced">
                <div class="article-comments">
                        <div class="title-comments"><h3><span>Nuevos vídeos</span></h3></div>
                </div>
               {include file="widget_videos_lastest.tpl"}
            </div>
            <div class="span-8 last">
                {include file="module_video_comments.tpl"}
            </div>

        </div>
    </div>
</div><!-- fin #main_content -->
