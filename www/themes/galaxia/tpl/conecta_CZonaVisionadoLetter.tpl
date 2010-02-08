<div style="float:left;width:760px;margin-bottom:6px;">
    <div class="textoConectaXornal">Carta al director:
    <span style="font-size: 18px; font-weight: normal;">{$letterID[0]->title|clearslash}</span>
    </div>
</div>
<div class="texto2FAQ">
    {assign var=id_author value=$letterID[0]->fk_user}
    <div class="CFirmaInfoMedia" style="color:#004B8D;">
        <div class="CTextoEnviadaPor"><img alt="imagen" src="{$params.IMAGE_DIR}noticia/flecha_destacado.gif"/>
            Enviada por <b>{$conecta_users[$id_author]->nick} </b> </div>
        <div class="CNombreInfoMedia">{humandate article=$letterID[0] created=$letterID[0]->created}</div>
    </div>

    <p style="clear:both; margin-top:20px;">
 	 {$letterID[0]->body}
    </p>
</div>

<div class="zonaClasificacionVideos">
    <div class="zonaPestanyasMedia">
        <!-- PESTANYA -->
        <div class="pestanyaSelecList">
            <div class="contInfoPestanyaGrande">
                <div class="flechaPestanyaSelecList"></div>
                <div class="textoPestanyaSelecList">                	
                    OTRAS CARTAS
                </div>
            </div>
            <div class="cierrePestanyaSelecList"></div>
        </div>
    </div>
    <div class="listadoMedia" id="div_pc_{$accion}">
         {include file="conecta_Textos_listado.tpl" arraytextos=$arrayletters}
    </div>
   
</div>