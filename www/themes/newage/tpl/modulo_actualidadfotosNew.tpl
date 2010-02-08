
<div class="actualidadFotosNew">
    <a href="{$lastAlbumContent->permalink}"><div class="cabeceraActualidadFotos"></div></a>
    <div class="zonaVisualizacionFotosNew">
        <div class="CPiezaActualidadFotosHomeNew">
            <div class="CCuerpoPiezaFotoXornalNew">
                <div class="CContainerTituloFotoNew">
                    <div class="CPieFotoPiezaFotoXornalNew" id="albumtitle"><div class="CFlechaGrisPieGenteXornal"></div><a title=" {$lastAlbumContent->title|clearslash}" href="{$lastAlbumContent->permalink}">{$lastAlbumContent->title|clearslash}</a></div>
                </div>
                
                <div class="CContainerFotoActualidadFotosNew"  id="albumactual">
                    <a title="{$lastAlbumContent->title|clearslash}" href="{$lastAlbumContent->permalink}">
                        <img style="height: 250px;" alt=" {$lastAlbumContent->title|clearslash}" src="{$MEDIA_IMG_PATH_WEB}album/crops/{$lastAlbumContent->id}.jpg"/>
                    </a>
                </div>
                <div class="CZonaThumbsFotosNew">
                    <div class="CThumbFotoPrimeraNew">
                        <a onclick="cambiaalbum('{$lastAlbumContent->title|clearslash|escape:'html'}','{$lastAlbumContent->permalink}','{$MEDIA_IMG_PATH_WEB}album/crops/{$lastAlbumContent->id}.jpg');" title="{$lastAlbumContent->title|clearslash|escape:'html'}" alt="{$lastAlbumContent->title|clearslash|escape:'html'}" >
                            <img height="72" alt="{$lastAlbumContent->title|clearslash|escape:'html'}" title="{$lastAlbumContent->title|clearslash|escape:'html'}" src="{$MEDIA_IMG_PATH_WEB}album/crops/{$lastAlbumContent->id}.jpg"  onmouseout="UnTip()" onmouseover="Tip('<b>{$lastAlbumContent->title|clearslash|escape:'html'}</b><br />{$lastAlbumContent->description|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', ABOVE, false, OFFSETY, 0, BGCOLOR, '#D9E3ED', BORDERCOLOR, '#004B8D', WIDTH, 300)" alt="{$lastAlbumContent->title|clearslash|escape:'html'}"/>
                        </a>
                    </div>
                    {section name=i loop=$albums start=0}
                        {if ($lastAlbumContent->id!=$albums[i]->id) }
                            <div class="CThumbFotoNew">
                                <a onclick="cambiaalbum('{$albums[i]->title|clearslash|regex_replace:"/'/":"\'"|escape:'html'}','{$albums[i]->permalink}','{$MEDIA_IMG_PATH_WEB}album/crops/{$albums[i]->id}.jpg');" title="{$albums[i]->title|clearslash|regex_replace:"/'/":"\'"|escape:'html'}" alt="{$albums[i]->title|clearslash|regex_replace:"/'/":"\'"|escape:'html'}" >
                                    <img height="72" alt="{$albums[i]->title|clearslash|regex_replace:"/'/":"\'"|escape:'html'}" title="{$albums[i]->title|clearslash|regex_replace:"/'/":"\'"|escape:'html'}" src="{$MEDIA_IMG_PATH_WEB}album/crops/{$albums[i]->id}.jpg"  onmouseout="UnTip()" onmouseover="Tip('<b>{$albums[i]->title|clearslash|regex_replace:"/'/":"\'"|escape:'html'}</b><br />{$albums[i]->description|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', ABOVE, false, OFFSETY, 0, BGCOLOR, '#D9E3ED', BORDERCOLOR, '#004B8D', WIDTH, 300)" alt="{$albums[i]->title|clearslash|regex_replace:"/'/":"\'"|escape:'html'}"/>
                                </a>
                            </div>
                        {/if}
                    {/section}
                </div>
            </div>
        </div>
    </div>
    <div class="linkMasMedia"><a href="{$lastAlbumContent->permalink}">+ Foto galerías</a></div>
</div>