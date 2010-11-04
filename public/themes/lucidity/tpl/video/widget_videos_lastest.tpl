{*
    OpenNeMas project

    @theme      Lucidity
*}
 <div id="tabs" class="inner-video-tabs">
    <ul>
            <li><a href="#tab-viewed"><span> + Vistos</span></a></li>
            <li><a href="#tab-related"><span> + Votados</span></a></li>
            <li><a href="#tab-commented"><span> + Comentados</span></a></li>
    </ul>
    <div id="tab-viewed">
        {section name=i loop=$videos_viewed}
             <div class="tab-thumb-video clearfix">
                <a class="video-link" title="{$videos_viewed[i]->title|clearslash|escape:'html'}" href="{$videos_viewed[i]->permalink}">
                    {if $videos_viewed[i]->author_name eq 'vimeo'}
                        <img src="{$videos_viewed[i]->thumbnail_small}" alt="{$videos_viewed[i]->title|clearslash|escape:'html'}" title="{$videos_viewed[i]->title|clearslash|escape:'html'}" />
                    {else}
                         <img src="http://i4.ytimg.com/vi/{$videos_viewed[i]->videoid}/default.jpg" alt="{$videos_viewed[i]->title|clearslash|escape:'html'}" title="{$videos_viewed[i]->title|clearslash|escape:'html'}"  />
                    {/if}
                </a>
                <div class="tab-thumb-video-shortitle">
                    <a href="/video/{$videos_viewed[i]->category_name}/" title="{$videos_viewed[i]->category_title}">{$videos_viewed[i]->category_title}</a>
                </div>
                <div class="tab-thumb-video-title">
                     <a href="{$videos_viewed[i]->permalink}" title="{$videos_viewed[i]->title|clearslash|escape:'html'}">{$videos_viewed[i]->title|clearslash|escape:'html'}</a>
                 </div>
            </div>
        {/section}
    </div>
    <div id="tab-related">
        {section name=i loop=$videos_voted}
             <div class="tab-thumb-video clearfix">
                <a class="video-link" title="{$videos_voted[i]->title|clearslash|escape:'html'}" href="{$videos_voted[i]->permalink}">
                    {if $videos_voted[i]->author_name eq 'vimeo'}
                        <img src="{$videos_voted[i]->thumbnail_small}" alt="{$videos_voted[i]->title|clearslash|escape:'html'}" title="{$videos_voted[i]->title|clearslash|escape:'html'}" />
                    {else}
                         <img src="http://i4.ytimg.com/vi/{$videos_voted[i]->videoid}/default.jpg" alt="{$videos_voted[i]->title|clearslash|escape:'html'}" title="{$videos_voted[i]->title|clearslash|escape:'html'}"  />
                    {/if}
                </a>
                <div class="tab-thumb-video-shortitle">
                    <a href="/video/{$videos_voted[i]->category_name}/" title="{$videos_voted[i]->category_title}">{$videos_voted[i]->category_title}</a>
                </div>
                <div class="tab-thumb-video-title">
                    <a href="{$videos_voted[i]->permalink}" title="{$videos_voted[i]->title|clearslash|escape:'html'}">{$videos_voted[i]->title|clearslash|escape:'html'}</a>
                </div>
            </div>
        {/section}

    </div>
     <div id="tab-commented">
        {section name=i loop=$videos_comments}
             <div class="tab-thumb-video clearfix">
                <a class="video-link" title="{$videos_comments[i]->title|clearslash|escape:'html'}" href="{$videos_comments[i]->permalink}">
                    {if $videos_comments[i]->author_name eq 'vimeo'}
                        <img src="{$videos_comments[i]->thumbnail_small}" alt="{$videos_comments[i]->title|clearslash|escape:'html'}" title="{$videos_comments[i]->title|clearslash|escape:'html'}" />
                    {else}
                         <img src="http://i4.ytimg.com/vi/{$videos_comments[i]->videoid}/default.jpg" alt="{$videos_comments[i]->title|clearslash|escape:'html'}" title="{$videos_comments[i]->title|clearslash|escape:'html'}"  />
                    {/if}
                </a>
                <div class="tab-thumb-video-shortitle">
                    <a href="/video/{$videos_comments[i]->category_name}/" title="{$videos_comments[i]->category_title}">{$videos_comments[i]->category_title}</a>
                </div>
                <div class="tab-thumb-video-title">
                     <a href="{$videos_comments[i]->permalink}" title="{$videos_comments[i]->title|clearslash|escape:'html'}">{$videos_comments[i]->title|clearslash|escape:'html'}</a>
                 </div>
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