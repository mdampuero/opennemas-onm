{*
    OpenNeMas project

    @theme      Lucidity
*}
    
<div class="flickr-highlighter clearfix">
    <div class="flickr-highlighter-header"><img src="{$smarty.const.MEDIA_PATH_URL}/sections/flickr.png" alt=""/></div>
    <div class="flickr-highlighter-big clearfix">
          {section name=i loop=$lastAlbum }
                {if $smarty.section.i.first}
                    <li class="first"><a href="#">
                        <img alt="{$lastAlbum[i]->title|clearslash|escape:'html'}" title="{$lastAlbum[i]->title|clearslash|escape:'html'}" src="{$smarty.const.MEDIA_IMG_PATH_WEB}album/crops/{$lastAlbum[i]->id}.jpg" />
                    </a></li>
                {elseif $smarty.section.i.last}
                    <li class="last"><a href="#">
                        <img alt="{$lastAlbum[i]->title|clearslash|escape:'html'}" title="{$lastAlbum[i]->title|clearslash|escape:'html'}" src="{$smarty.const.MEDIA_IMG_PATH_WEB}album/crops/{$lastAlbum[i]->id}.jpg" />
                    </a></li>
                {else}
                    <li class=""><a href="#">
                        <img alt="{$lastAlbum[i]->title|clearslash|escape:'html'}" title="{$lastAlbum[i]->title|clearslash|escape:'html'}" src="{$smarty.const.MEDIA_IMG_PATH_WEB}album/crops/{$lastAlbum[i]->id}.jpg" />
                    </a></li>
                {/if}

            {/section}
        <a href="#">Envíanos tu careto, y te pondremos aquí...</a>
    </div>
    <div class="flickr-highlighter-footer">&nbsp;</div>
</div>