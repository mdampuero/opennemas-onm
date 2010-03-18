{*
    OpenNeMas project

    @theme      Lucidity
*}
 <div id="tabs" class="inner-video-tabs">
    <ul>
            <li><a href="#tab-related"><span>Relacionados</span></a></li>
            <li><a href="#tab-new"><span>Novos</span></a></li>
    </ul>
    <div id="tab-related">
        {section name=i loop=$videos}
             <div class="tab-thumb-video clearfix">
                <img src="{$others_videos[i]->thumbnail_small}" />
                <div class="tab-thumb-video-shortitle">{$videos[i]->category_name}</div>
                <div class="tab-thumb-video-title">{$videos[i]->title|clearslash|escape:'html'}</div>
            </div>
        {/section}



        </div>
        <div id="tab-new">
        {section name=i loop=$others_videos}
             <div class="tab-thumb-video clearfix">
                <img src="{$others_videos[i]->thumbnail_small}" />
                <div class="tab-thumb-video-shortitle">{$others_videos[i]->category_name}</div>
                <div class="tab-thumb-video-title">{$others_videos[i]->title|clearslash|escape:'html'}</div>
            </div>
        {/section}
    </div>
</div>