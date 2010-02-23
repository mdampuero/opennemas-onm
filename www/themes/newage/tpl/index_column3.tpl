<div class="column3">
    <img src="{$params.IMAGE_DIR}logos/facebook-logo.png" alt="Contexto" style="text-align:center;margin:5px;"/>
    {if isset($frontpage_newspaper_img) AND preg_match('/\.jpg$/', $frontpage_newspaper_img) }
    <div style="margin-right:5px;text-align: center;">
        <a href="{$smarty.const.SITE_URL}/portadas/#xornal" title="Primera pagina de la version impresa">
            <img src="/media/images/kiosko/{$frontpage_newspaper_img}" border="0" alt="Xornal Frontpage newspaper" /></a>
    </div>
    {/if}

    <iframe scrolling="no" frameborder="0" src="http://www.facebook.com/connect/connect.php?id=282535299100&connections=10"
      allowtransparency="true" style="border: none; width: 250px; height: 250px;"></iframe>
      
    {*include file="modulo_column3_containerFotoVideoDiaMasListado.tpl"*}

    <div class="contBannerYTextoPublicidadCol3">
        {insert name="renderbanner" type=6 cssclass="contBannerPublicidadCol3" width="255" height="*"
            beforeHTML='<div class="textoBannerPublicidad">publicidad</div>'}
    </div>
    
    <div class="separadorHorizontal"></div>
    {*include file="container_bolsa.tpl"*}
    <!--DO NOT EDIT BELOW!- WIDGETS: http://www.sanebull.com/widgets -->
    <iframe scrolling="no" width="210" height="410" frameborder="0" marginheight="0" marginwidth="0" src="http://www.sanebull.com/widget_world_watch.jsp?market=all" id="display-widget"></iframe>
    <!--DO NOT EDIT ABOVE!-->

    <div class="separadorHorizontal"></div>    
    {if !preg_match('/weather\.php/', $smarty.server.SCRIPT_NAME) }
        {include file="modulo_weather.tpl"}
    {/if}
    {include file="container_suplementos.tpl"}
    <br /> <br />
    {include file="container_extras.tpl"}
    <div class="separadorHorizontal"></div>
    <div class="contBannerYTextoPublicidadCol3">
        {insert name="renderbanner" type=15 cssclass="contBannerPublicidadCol3" width="255" height="*"
            beforeHTML='<div class="textoBannerPublicidad">publicidad</div>'}
    </div>
    <div class="separadorHorizontal"></div>
    {include file="modulo_actualidadfotos.tpl"}
</div>