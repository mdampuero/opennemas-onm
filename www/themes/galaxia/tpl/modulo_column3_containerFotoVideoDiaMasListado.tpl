{if isset($graficasconecta) && count($graficasconecta)}
<div class="enlaceFotoVideoDia">
    <div class="enlaceFotoVideoDia">
        <a href="/conecta/enquisa/"><img alt="Encuestas Conecta" src="{$params.IMAGE_DIR}fotoVideoDia/encuestasXornal.gif" style="margin-bottom: 5px;"/></a>
        {section name=enqs loop=$graficasconecta}
        <p> <a class="no_underline" href="/conecta/enquisa/{$graficasconecta[enqs]->id}.html" title="{$graficasconecta[enqs]->title|clearslash}"><b>{$graficasconecta[enqs]->title|clearslash}</b></a></p>
        {* "h" é de tipo horizontal *}
        <a class="no_underline" href="/conecta/enquisa/{$graficasconecta[enqs]->id}.html" title="{$graficasconecta[enqs]->title|clearslash}">
            <img src="/conecta/enquisa/h{$graficasconecta[enqs]->id}.png" border="0" alt="{$graficasconecta[enqs]->title|clearslash}" /></a>
        <br />
        {/section}
    </div>
    <div class="separadorHorizontal"></div>
</div>
{/if}

{* INDEX CONECTA *}
<div class="containerFotoVideoDiaMasListado">
    {if !(preg_match('/planconecta\.php/',$smarty.server.SCRIPT_NAME))}
        <div class="containerFotoVideoDia">
            <div class="logoFotoVideoDia"><a href="/conecta/"><img src="{$params.IMAGE_DIR}fotoVideoDia/logoPlanConecta.gif" alt="Logo Conecta"/></a></div>
            <div class="zonaPestanyasFotoVideoDia">
                <div class="pestanyaFotoDia">
                    <div class="textoPestanyaMediaDia"><a style="cursor:pointer;" onclick="$('videodia').hide();$('photodia').show();$('textvideodia').hide();$('textphotodia').show();">Foto del d&iacute;a</a></div>
                </div>
                <div class="pestanyaVideoDia">
                    <div class="textoPestanyaMediaDia"><a style="cursor:pointer;" onclick="$('videodia').show();$('photodia').hide();$('textvideodia').show();$('textphotodia').hide();">Video del d&iacute;a</a></div>
                </div>
            </div>
            <div class="zonaVisualizacionFotoVideoDia" id="photodia"  style="display:inline;">
                <a style="cursor:pointer;" href="/conecta/foto-dia/"><img src="{$MEDIA_CONECTA_WEB}{$photodia->path_file}" width="180" height="115" alt="Foto del dia"/> </a>
            </div>
            <div class="zonaVisualizacionFotoVideoDia" id="videodia" style="display:none;">
                <a style="cursor:pointer;" href="/conecta/video-dia/"><img src="http://i4.ytimg.com/vi/{$videodia->code}/default.jpg"  width="180" height="115" alt="Video del dia"/> </a>
            </div>
            <div class="franjaAzulFotoVideoDia"><div class="flechitaBlancaFotoVideoDia"></div>
                <div id="textphotodia" class="textoFranjaAzulFotoVideoDia" style="display:inline;">{$photodia->title|clearslash}</div>
                <div id="textvideodia" class="textoFranjaAzulFotoVideoDia" style="display:none;">{$videodia->title|clearslash}</div>
            </div>
        </div>
    {else}
        <div class="containerFotoVideoDia">
            <div class="logoFotoVideoDia"><a href="/conecta/"><img src="{$params.IMAGE_DIR}fotoVideoDia/logoPlanConecta.gif" alt="Logo Conecta"/></a></div>
        </div>
    {/if}
    {section loop=$allcategorys name=t}
        {assign var=categorys value=$allcategorys[t]}
        {section loop=$categorys name=c}
           <div class="enlaceFotoVideoDia">
               <div class="enlaceFotoVideoDia">
                   <img alt="Flechita azul" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
                   <a href="/conecta/{typepccontent content_type=$categorys[c]->fk_content_type}/{$categorys[c]->name}/">{$categorys[c]->title}</a>
               </div>
               <div class="fileteFotoVideoDia"></div>
            </div>
        {/section}
    {/section}
    <div class="menuInferiorFotoVideoDia">
        <a href="/conecta/login/">Login</a> | <a href="/conecta/rexistro/">Registrarse</a>
    </div>
</div>