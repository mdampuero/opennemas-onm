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
                        <h2>Predicción meteorol&oacute;gica para hoy.</h2>
                        <div class="apertura_nota">
                            <div class="firma_nota firma_tiempo">
                                <div class="firma_nombre">Informaci&oacute;n extra&iacute;da de Meteogalicia (Conseller&iacute;a de Medio Ambiente, Territorio e Infraestruturas) e Agencia Estatal de Meteorolog&iacute;a</div>
                                <div class="separadorFirma"></div>
                                <div class="firma_fecha">{$smarty.now|date_format:"%d / %m / %Y"}</div>
                                <div class="separadorFirma"></div>
                                <div class="firma_hora">{$smarty.now|date_format:"%H:%M"} h.</div>
                            </div>
                        </div>
                        {*<img src='{$img_playa}' alt='Playas' title='Playas'><br/>*}
                        <img src='{$img_tiempo}' alt='Tiempo' title='Tiempo'>
                    </div>
                </div>
                {include file="weather_column3.tpl"}
            </div>
            <div class="separadorHorizontal"></div>
            {include file="modulo_separadorbanners2.tpl"}
        </div>
        {include file="modulo_footer.tpl"}
    </div>
  </div>
</div>
{include file="modulo_analytics.tpl"}
</body>
</html>