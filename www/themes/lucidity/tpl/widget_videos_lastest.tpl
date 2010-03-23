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
                {if $videos[i]->author_name eq 'vimeo'}
                    <img src="{$videos[i]->thumbnail_small}" alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" />
                {else}
                     <img src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg" alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}"  />
                {/if}
                <div class="tab-thumb-video-shortitle">{$videos[i]->category_title}</div>
                <div class="tab-thumb-video-title">
                    <a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">{$videos[i]->title|clearslash|escape:'html'}</a>
                </div>
            </div>
        {/section}



        </div>
        <div id="tab-new">
        {section name=i loop=$others_videos}
             <div class="tab-thumb-video clearfix">
                {if $others_videos[i]->author_name eq 'vimeo'}
                    <img src="{$others_videos[i]->thumbnail_small}" alt="{$others_videos[i]->title|clearslash|escape:'html'}" title="{$others_videos[i]->title|clearslash|escape:'html'}" />
                {else}
                     <img src="http://i4.ytimg.com/vi/{$others_videos[i]->videoid}/default.jpg" alt="{$others_videos[i]->title|clearslash|escape:'html'}" title="{$others_videos[i]->title|clearslash|escape:'html'}"  />
                {/if}
                <div class="tab-thumb-video-shortitle">{$others_videos[i]->category_title}</div>
                <div class="tab-thumb-video-title">{$others_videos[i]->title|clearslash|escape:'html'}</div>
            </div>
        {/section}
    </div>
</div>

{*
<div id="tabs" class="inner-video-tabs">
    <ul>
            <li><a href="#tab-related"><span>Más Vistos</span></a></li>
            <li><a href="#tab-new"><span>Más Comentados</span></a></li>
    </ul>
    <div id="tab-related">
        {section name=i loop=$videos_comments}
             <div class="tab-thumb-video clearfix">
                {if $videos_comments[i]->author_name eq 'vimeo'}
                    <img src="{$videos_comments[i]->thumbnail_small}" alt="{$videos_comments[i]->title|clearslash|escape:'html'}" title="{$videos_comments[i]->title|clearslash|escape:'html'}" />
                {else}
                     <img src="http://i4.ytimg.com/vi/{$videos_comments[i]->videoid}/default.jpg" alt="{$videos_comments[i]->title|clearslash|escape:'html'}" title="{$videos_comments[i]->title|clearslash|escape:'html'}"  />
                {/if}
                <div class="tab-thumb-video-shortitle">{$videos_comments[i]->category_title}</div>
                <div class="tab-thumb-video-title">
                    <a href="{$videos_comments[i]->permalink}" title="{$videos_comments[i]->title|clearslash|escape:'html'}">{$videos_comments[i]->title|clearslash|escape:'html'}</a>
                </div>
            </div>
        {/section}
        </div>
        <div id="tab-new">
        {section name=i loop=$videos_viewed}
             <div class="tab-thumb-video clearfix">
                {if $videos_viewed[i]->author_name eq 'vimeo'}
                    <img src="{$videos_viewed[i]->thumbnail_small}" alt="{$videos_viewed[i]->title|clearslash|escape:'html'}" title="{$videos_viewed[i]->title|clearslash|escape:'html'}" />
                {else}
                     <img src="http://i4.ytimg.com/vi/{$videos_viewed[i]->videoid}/default.jpg" alt="{$videos_viewed[i]->title|clearslash|escape:'html'}" title="{$videos_viewed[i]->title|clearslash|escape:'html'}"  />
                {/if}
                <div class="tab-thumb-video-shortitle">{$videos_viewed[i]->category_title}</div>
                <div class="tab-thumb-video-title">
                    <a href="{$videos_viewed[i]->permalink}" title="{$videos_viewed[i]->title|clearslash|escape:'html'}">{$videos_viewed[i]->title|clearslash|escape:'html'}</a>
                </div>
            </div>
        {/section}
    </div>
</div>
*}