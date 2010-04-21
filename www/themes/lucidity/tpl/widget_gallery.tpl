{*
    OpenNeMas project

    @theme      clarity
*}

     
<div class="layout-column first-column span-12">
    <div class="photos-highlighter clearfix span-12">
        <div class="photos-header"><img src="{$params.IMAGE_DIR}/widgets/photos-highlighter-header.png" alt="Galerias"/></div>
        <div class="photos-highlighter-big clearfix">
            <a href="{$lastAlbum[0]->permalink}" title="{$lastAlbum[0]->title|clearslash|escape:'html'}" >
                <img alt="{$lastAlbum[0]->title|clearslash|escape:'html'}" title="{$lastAlbum[0]->title|clearslash|escape:'html'}" src="{$smarty.const.MEDIA_IMG_PATH_WEB}album/crops/{$lastAlbum[0]->id}.jpg" />                
            </a>
            <div class="info"><a href="{$lastAlbum[0]->permalink}" title="{$lastAlbum[0]->title|clearslash|escape:'html'}" >{$lastAlbum[0]->title|clearslash}</a></div>
        </div>
        <ul class="photos-highligher-little-section-links">
            {section name=i loop=$lastAlbum max=3}
                {if $smarty.section.i.first}
                    <li class="first"><a href="{$lastAlbum[i]->permalink}" title="{$lastAlbum[i]->title|clearslash|escape:'html'}" >
                        <img alt="{$lastAlbum[i]->title|clearslash|escape:'html'}" title="{$lastAlbum[i]->title|clearslash|escape:'html'}" src="{$smarty.const.MEDIA_IMG_PATH_WEB}album/crops/{$lastAlbum[i]->id}.jpg" />
                    </a></li>
                {elseif $smarty.section.i.last}
                    <li class="last"><a href="{$lastAlbum[i]->permalink}" title="{$lastAlbum[i]->title|clearslash|escape:'html'}" >
                        <img alt="{$lastAlbum[i]->title|clearslash|escape:'html'}" title="{$lastAlbum[i]->title|clearslash|escape:'html'}" src="{$smarty.const.MEDIA_IMG_PATH_WEB}album/crops/{$lastAlbum[i]->id}.jpg" />
                    </a></li>
                {else}
                    <li class=""><a href="{$lastAlbum[i]->permalink}" title="{$lastAlbum[i]->title|clearslash|escape:'html'}" >
                        <img alt="{$lastAlbum[i]->title|clearslash|escape:'html'}" title="{$lastAlbum[i]->title|clearslash|escape:'html'}" src="{$smarty.const.MEDIA_IMG_PATH_WEB}album/crops/{$lastAlbum[i]->id}.jpg" />
                    </a></li>
                {/if}

            {/section}
             
        </ul>
    </div>
</div>
