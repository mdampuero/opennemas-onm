<div class="column3">
    <img src="{$params.IMAGE_DIR}logos/portada_papel_title.gif" alt="Contexto" style="margin-left:4px;"/>
    {if isset($frontpage_newspaper_img) AND preg_match('/\.jpg$/', $frontpage_newspaper_img) }
    <div style="margin-right:5px;text-align: center;">
        <a href="{$smarty.const.SITE_URL}/portadas/#xornal" title="Primera pagina de la version impresa">
            <img src="/media/images/kiosko/{$frontpage_newspaper_img}" border="0" alt="Xornal Frontpage newspaper" /></a>
    </div>
    {/if}

<script type="text/javascript" src="http://cdn.widgetserver.com/syndication/subscriber/InsertWidget.js"></script><script>if (WIDGETBOX) WIDGETBOX.renderWidget('1dfcceaf-6acd-4a13-8218-dcc4d55fe567');</script><noscript>Get the <a href="http://www.widgetbox.com/widget/dralzheimer">Desktop Wallpapers</a> widget and many other <a href="http://www.widgetbox.com/">great free widgets</a> at <a href="http://www.widgetbox.com">Widgetbox</a>! Not seeing a widget? (<a href="http://docs.widgetbox.com/using-widgets/installing-widgets/why-cant-i-see-my-widget/">More info</a>)</noscript>
<script type="text/javascript" src="http://cdn.widgetserver.com/syndication/subscriber/InsertWidget.js"></script><script>if (WIDGETBOX) WIDGETBOX.renderWidget('a757d348-7ce0-4a8f-80e9-df9503b3944b');</script><noscript>Get the <a href="http://www.widgetbox.com/widget/facebookminisite">Facebook Minisite</a> widget and many other <a href="http://www.widgetbox.com/">great free widgets</a> at <a href="http://www.widgetbox.com">Widgetbox</a>! Not seeing a widget? (<a href="http://docs.widgetbox.com/using-widgets/installing-widgets/why-cant-i-see-my-widget/">More info</a>)</noscript>


    {*include file="modulo_column3_containerFotoVideoDiaMasListado.tpl"*}

    <div class="contBannerYTextoPublicidadCol3">
        {insert name="renderbanner" type=6 cssclass="contBannerPublicidadCol3" width="255" height="*"
            beforeHTML='<div class="textoBannerPublicidad">publicidad</div>'}
    </div>
    <div class="separadorHorizontal"></div>
    {include file="container_bolsa.tpl"}
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