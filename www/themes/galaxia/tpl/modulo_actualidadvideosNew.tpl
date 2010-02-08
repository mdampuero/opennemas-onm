<div class="actualidadVideosNew">
    <a href="/video"><div class="cabeceraActualidadVideos"></div></a>
    <div class="zonaVisualizacionVideosNew">
        <div class="CContainerTituloVideo">
            <div class="CPieFotoPiezaVideoXornalNew" id="videotitle"><div class="CFlechaGrisPieGenteXornal"></div>{$videos[0]->title|clearslash|escape:'html'}</div>
        </div>
        <div class="CZonaVisorVideos" id="videoactual">
            <object width="250" height="250">
                <param value="http://www.youtube.com/v/{$videos[0]->videoid}" name="movie"></param>
                <param value="true" name="allowFullScreen"></param>
                <param value="always" name="allowscriptaccess"></param>
                <embed width="250" height="250" src="http://www.youtube.com/v/{$videos[0]->videoid}" />
            </object>
        </div>
        <div class="CZonaThumbsVideosNew">
            {section name=i loop=$videos start=0}
            <div class="CThumbVideo">
              <div class="CHolderThumbVideo">
                    <span class="CEdgeThumbVideo"></span><span onclick="cambiavideo('{$videos[i]->videoid}','{$videos[i]->title|clearslash|escape:'html'}');" class="CContainerThumbVideo"><img width="70" alt="{$videos[i]->title|clearslash|regex_replace:"/'/":"\'"|escape:'html'}" title="{$videos[i]->title|clearslash|regex_replace:"/'/":"\'"|escape:'html'}" src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg"  onmouseout="UnTip()" onmouseover="Tip('<b>{$videos[i]->title|clearslash|regex_replace:"/'/":"\'"|escape:'html'}</b><br />{$videos[i]->description|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', ABOVE, false, OFFSETY, 0, BGCOLOR, '#D9E3ED', BORDERCOLOR, '#004B8D', WIDTH, 300)" alt="{$videos[i]->title|clearslash|regex_replace:"/'/":"\'"|escape:'html'}" /></span>
              </div>
            </div>
            {/section}
        </div>
    </div>
    <div class="linkMasMedia"><a href="/video">+ Videos</a></div>
</div>