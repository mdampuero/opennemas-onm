<div class="CZonaVisionadoMedia">
    <div class="CVisorMedia">  
        <object width="425" height="344">
        <param name="movie" value="http://www.youtube.com/v/{$videoID[0]->code}&amp;hl=es&amp;fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
        <embed src="http://www.youtube.com/v/{$videoID[0]->code}&amp;hl=es&amp;fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="344"></embed></object>
    </div>
    <div class="CMarcoVisorInfoMedia">
        <div class="CVisorInfoMedia">
            <div class="CContainerInfoMedia">
                <div class="CContainerSeccionFechaInfoMedia">
                    <div class="CSeccionInfoMedia">{$videoID[0]->category_name}</div>
                    <div class="CFechaInfoMedia">{$videoID[0]->created|date_format:"%d/%m/%y"}</div>
                </div>
                <div class="CTitularInfoMedia">{$videoID[0]->title|clearslash}</div>
                <div class="separadorHorizontal"></div>
                {assign var=id_author value=$videoID[0]->fk_user}
                <div class="CFirmaInfoMedia">
                     <div class="CTextoEnviadaPor">Enviada por <b>{$conecta_users[$id_author]->nick} </b> </div>
                     <div class="CNombreInfoMedia"> {humandate article=$videoID[0] created=$videoID[0]->created}</div>
                </div>
                <div class="CTextoInfoMedia">
                    <div class="CFlechitaTexto"></div>
                    {$videoID[0]->description|clearslash}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="zonaClasificacionVideos">
    <div class="zonaPestanyasMedia">
        <!-- PESTANYA -->
        <div class="pestanyaSelecList">
            <div class="contInfoPestanyaGrande">
                <div class="flechaPestanyaSelecList"></div>
                <div class="textoPestanyaSelecList">VIDEOS PASADOS</div>
            </div>
            <div class="cierrePestanyaSelecList"></div>
        </div>
    </div>
    <div class="listadoMedia" id="div_pc_{$accion}">
          {include file="conecta_Videos_listado.tpl"}
    </div>
    
</div>
