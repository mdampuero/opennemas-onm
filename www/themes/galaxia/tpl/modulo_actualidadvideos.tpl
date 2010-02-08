<div class="actualidadVideos">
    <div class="cabeceraActualidadVideos"><a href="/video/"><img src="{$params.IMAGE_DIR}actualidadVideosFotos/logoActualidadVideos.gif" alt="Actualidad Videos" /></a></div>
    <div class="zonaVisualizacionVideos">
        <div class="CZonaVisorVideos" id="videoactual">
            <object width="250" height="250">
                <param value="http://www.youtube.com/v/{$videos[0]->videoid}" name="movie" />
                <param value="true" name="allowFullScreen" />
                <param value="always" name="allowscriptaccess">
                <embed width="250" height="250" src="http://www.youtube.com/v/{$videos[0]->videoid}" />
            </object>
        </div>
        <div class="CZonaThumbsVideos">
            {section name=i loop=$videos start=0}
            <div class="CThumbVideo">
              <div class="CHolderThumbVideo">
                    <span class="CEdgeThumbVideo" />
                    <span onclick="cambiavideo('{$videos[i]->videoid}','{$videos[i]->title|clearslash|regex_replace:"/'/":"\'"|escape:'html'}');" class="CContainerThumbVideo"><img width="70" alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg"  onmouseout="UnTip()" onmouseover="Tip('<b>{$videos[i]->title|clearslash|regex_replace:"/'/":"\'"|escape:'html'}</b><br />{$videos[i]->description|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', ABOVE, false, OFFSETY, 0, BGCOLOR, '#E4DDC9', BORDERCOLOR, '#CFBA81', WIDTH, 300)" /></span>
              </div>
            </div>
            {/section}
        </div>
        <div class="CContainerTituloVideo">
            <div class="CPieFotoPiezaVideoXornal" id="videotitle"><div class="CFlechaGrisPieGenteXornal"></div>{$videos[0]->title|clearslash|escape:'html'}</div>
        </div>
    </div>
    <div class="linkMasMedia"><a href="/video">+ Videos</a></div>
</div>
