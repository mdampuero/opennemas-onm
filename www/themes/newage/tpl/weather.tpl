{include file="modulo_head.tpl"}
<body>
{* FIXME: revisar e incluir de otra manera *}
<style>
@import url( {$params.CSS_DIR}fotoVideoDia.css );
</style>
<!--[if IE]>
<script language="javascript" src="{$params.JS_DIR}png.js" type="text/javascript"></script>
<![endif]-->
<div class="global_metacontainer">
  <div class="marco_metacontainer">
    <div class="metacontainer">	
{include file="modulo_separadorbanners1.tpl"}
{include file="modulo_header.tpl"}
    	<div class="container">
            <div class="containerNoticias">
                <div class="column12">                    
                    <div class="containerTempo">                        
                        {if !is_null($localidade) }
                        <h2>El tiempo en {$titulo}</h2>
                        <div class="subtitulo_nota">Predicción meteorológica para los próximos 7 días.</div>                        
                        <div class="apertura_nota">
                            <div class="firma_nota firma_tiempo">
                                <div class="firma_nombre">Información extraída del Institulo Nacional de Meteorología</div>
                                <div class="separadorFirma"></div>
                                <div class="firma_fecha">{$smarty.now|date_format:"%d / %m / %Y"}</div>
                                <div class="separadorFirma"></div>
                                <div class="firma_hora">{$smarty.now|date_format:"%H:%M"} h.</div>
                            </div>
                        </div>                        
                        <table class="tabla_datos" summary="Esta tabla muestra la precidión para la localidad de {$titulo}.">
                            {assign var="url" value="http://www.aemet.es/es/eltiempo/prediccion/localidades?$querystring"}
                            {remotecontent url=$url onafter="remotecontent_onafter_aemet"
                                cache="true" cachelife="120" cachename=$cachename}               
                        </table>                        
                        {else} {* Sino => no se seleccionó una localidad *}
                            <h2>El tiempo</h2>
                            <div class="subtitulo_nota">Seleccione una ciudad para ver su predicción.</div>
                            
                            <div class="apertura_nota">
                                <div class="firma_nota firma_tiempo">
                                    <div class="firma_nombre">Información extraída del Institulo Nacional de Meteorología</div>
                                    <div class="separadorFirma"></div>
                                    <div class="firma_fecha">{$smarty.now|date_format:"%d / %m / %Y"}</div>
                                    <div class="separadorFirma"></div>
                                    <div class="firma_hora">{$smarty.now|date_format:"%H:%M"} h.</div>
                                </div>
                            </div>                                                
                        {/if}                        
                    </div> <!-- .containerTempo -->                
                </div> <!-- .column12 -->
                {include file="weather_column3.tpl"}
            </div> <!-- .containerNoticias -->            
            <div class="separadorHorizontal"></div>
            {include file="modulo_separadorbanners2.tpl"}
        </div> <!-- .container-->
        {include file="modulo_footer.tpl"}
    </div>
  </div>
</div>
{include file="modulo_analytics.tpl"}
</body>
</html>