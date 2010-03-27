{*
    OpenNeMas project

    @theme      Lucidity
*}
    
<div class="flickr-highlighter clearfix">
    <div class="flickr-highlighter-header">
       <b>Fotogalerias</b>
    </div>
    <div class="flickr-highlighter-big clearfix">
          {section name=i loop=$lastAlbum }
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
    </div>
    <div class="flickr-highlighter-footer">
        <a href="{$lastAlbum[i]->permalink}" title="{$lastAlbum[i]->title|clearslash|escape:'html'}" >
            <img alt="{$lastAlbum[0]->title|clearslash|escape:'html'}" title="{$lastAlbum[0]->title|clearslash|escape:'html'}" src="{$smarty.const.MEDIA_IMG_PATH_WEB}album/crops/{$lastAlbum[0]->id}.jpg" />
        </a>
    </div>
</div>