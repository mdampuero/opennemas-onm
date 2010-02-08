<div style="float:left;width:760px;margin-bottom:10px;">
    <div class="textoConectaXornal">Opini&oacute;n del Lector:
    <span style="font-size: 18px; font-weight: normal;">{$opinionID[0]->title|clearslash}</span>
    </div>
</div>
<div class="texto2FAQ">
    {assign var=id_author value=$opinionID[0]->fk_user}
    <div class="CFirmaInfoMedia" style="color:#004B8D;">
         <div class="CTextoEnviadaPor"><img alt="imagen" src="{$params.IMAGE_DIR}noticia/flecha_destacado.gif"/>
            Enviada por <b>{$conecta_users[$id_author]->nick}</b></div>
         <div class="CNombreInfoMedia">{humandate article=$opinionID[0] created=$opinionID[0]->created}</div>
    </div>
    <p style="clear:both; margin-top:20px;">
        {$opinionID[0]->body}
    </p>
    <br/>  
</div>

<div class="zonaClasificacionVideos">
    <div class="zonaPestanyasMedia">
        <!-- PESTANYA -->
        <div class="pestanyaSelecList">
            <div class="contInfoPestanyaGrande">
                <div class="flechaPestanyaSelecList"></div>
                <div class="textoPestanyaSelecList">
                    OTRAS OPINIONES
                </div>
            </div>
            <div class="cierrePestanyaSelecList"></div>
        </div>
    </div>
    <div class="listadoMedia" id="div_pc_{$accion}">
        {include file="conecta_Textos_listado.tpl" arraytextos=$arrayopinions}
    </div>

</div>