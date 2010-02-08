<div class="CZonaVisionadoMedia">
    {assign var=id_author value=$photoID[0]->fk_user}
    <div class="CVisorMedia">        
         <a href="{$MEDIA_CONECTA_WEB}{$photoID[0]->path_file}" class="lightwindow" rel='xornal[album]' title="{$photoID[0]->title|clearslash|escape:'html'}" caption="{$accion|upper}" author="{$conecta_users[$id_author]->nick}">
            <img style="max-width:434px;max-height:320px;" src="{$MEDIA_CONECTA_WEB}{$photoID[0]->path_file}">
         </a>
    </div>
    <div class="CMarcoVisorInfoMedia">
        <div class="CVisorInfoMedia">
            <div class="CContainerInfoMedia">
                <div class="CContainerSeccionFechaInfoMedia">
                    <div class="CSeccionInfoMedia">{$accion}</div>
                </div>
                <div class="CTitularInfoMedia">{$photoID[0]->title|clearslash}</div>
                <div class="separadorHorizontal"></div>
                <div class="CFirmaInfoMedia">                                       
                     <div class="CTextoEnviadaPor">Enviada por <b>{$conecta_users[$id_author]->nick} </b> </div>
                     <div class="CNombreInfoMedia"> {humandate article=$photoID[0] created=$photoID[0]->created}</div>
                </div>
                <br />
                <div class="CTextoInfoMedia">
                    <div class="CFlechitaTexto"></div>
                    {$photoID[0]->description|clearslash}
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
                <div class="textoPestanyaSelecList">
                   FOTOGRAFÍAS PASADAS
                </div>
            </div>
            <div class="cierrePestanyaSelecList"></div>
        </div>
    </div>
    <div class="listadoMedia" id="div_pc_{$accion}">
         {include file="conecta_Fotos_listado.tpl"}
    </div>
   
</div>