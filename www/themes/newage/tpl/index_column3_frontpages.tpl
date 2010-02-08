<div class="column3" align="center" style="width:300px;">
    <img src="{$params.IMAGE_DIR}logos/portada_papel_title.gif" alt="Contexto" style="margin-left:4px;"/>
    {if isset($frontpage_newspaper_img) AND preg_match('/\.jpg$/', $frontpage_newspaper_img) }
    <div style="margin-right:5px;text-align: center;">
        <a href="{$smarty.const.SITE_URL}media/files/portada/{$frontpage_newspaper_pdf->path}" target="_blank" title="Primera pagina de la version impresa">
            <img src="/media/images/portada/{$frontpage_newspaper_img}" border="0" /></a>
    </div>
    {/if}

    {include file="modulo_column3_containerFotoVideoDiaMasListado.tpl"}

    <div class="contBannerYTextoPublicidadCol3">
        {* renderbanner banner=$banner6 photo=$photo6 cssclass="contBannerPublicidadCol3" width="180" height="*"
            beforeHTML='<div class="textoBannerPublicidad">publicidad</div>' *}
        {insert name="renderbanner" type=6 cssclass="contBannerPublicidadCol3" width="180" height="*"
            beforeHTML='<div class="textoBannerPublicidad">publicidad</div>'}
    </div>

    <div class="separadorHorizontal"></div>
    <!-- LA BOLSA -->
    <div class="containerLaBolsa">
	<div class="cabeceraLaBolsa">
	    <img src="{$params.IMAGE_DIR}bolsa/logoBolsa.gif" alt="La Bolsa" />
	</div>
	<div class="cuerpoLaBolsa">        
        {* 6 hours for cachelife/More info ./trunk/www/themes/xornal/plugins/function.remotecontent.php *}
        {remotecontent url="http://www.infobolsa.es/mini-ficha/ibex35.htm" onafter="remotecontent_onafter_infobolsa" cache="true" cachelife="30"}
    </div>
    
	<div class="cuerpoPiezaOpinionEconomia">
		  <div class="fotoPiezaOpinionEconomia">
			<a href="/opinions/opinions_do_autor/56/Vicente Martin.html" class="contSeccionListadoPortadaPCAuthor">
				<img alt="Vicente Martin" src="/themes/xornal/images/opinion/analisis_vicente_martin.gif"/>
		     </a>
          </div>
		  <div class="textoPiezaOpinionEconomia"><img alt="" src="/themes/xornal/images/flechitaMenu.gif"/>
              <a href="{$opinionVicenteMartin->permalink|default:"#"}">{$opinionVicenteMartin->title|clearslash}</a></div>
		</div>
	  </div>

    <div class="separadorHorizontal"></div>    
    {if !preg_match('/weather\.php/', $smarty.server.SCRIPT_NAME) }
        {include file="modulo_weather.tpl"}
    {/if}

    <a href="http://www.xornal.com/seccion/suplementos/">
        <img src="{$params.IMAGE_DIR}logos/suplementos_xornal.gif" alt="Contexto" style="margin-top: 20px; margin-bottom: 5px;margin-left: 5px;" />
    </a>

    <div class="separadorHorizontal"></div>
    <div style="display:inline;float:left;position:relative;">
        {if isset($frontpage_contexto_img) AND preg_match('/\.jpg$/', $frontpage_contexto_img) }
        <div style="margin-right:5px;text-align: center;">
            <a href="/seccion/suplementos/contexto/" title="Contexto">
                <img src="/media/images/contexto/{$frontpage_contexto_img}" border="0" />
            </a>
        </div>
        {/if}
        <div class="CContendorSuplementos">
            <img alt="" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
            <a style="color:#004B8E;" href="{$titulares_contexto->permalink|default:"#"}">{$titulares_contexto->title|clearslash}</a>
        </div>
    </div>
	<div class="fileteFotoVideoDia"></div>
    <div style="margin-top:10px;display:inline;float:left;position:relative;">
        {if isset($frontpage_estratexias_img) AND preg_match('/\.jpg$/', $frontpage_estratexias_img) }
        <div style="margin-right:5px;text-align: center;">
            <a href="/seccion/suplementos/estratexias/" title="Estratexias">
                <img src="/media/images/estratexias/{$frontpage_estratexias_img}" border="0" />
            </a>
        </div>
        {/if}
        <div class="CContendorSuplementos">
            <img alt="" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
            <a style="color:#004B8E;" href="{$titulares_estratexias->permalink|default:"#"}">{$titulares_estratexias->title|clearslash}</a>
        </div>
    </div>
	<div class="fileteFotoVideoDia"></div>
    <div style="margin-top:10px;display:inline;float:left;position:relative;">
        <div style="margin-right:5px;text-align: center;">
            <a href="/seccion/suplementos/exit/" title="Exit">
                <img src="/media/images/exit/logo_exit.jpg" border="0" />
            </a>
        </div>
        <div class="CContendorSuplementos">
            <img alt="" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
            <a style="color:#004B8E;" href="{$titulares_exit->permalink|default:"#"}">{$titulares_exit->title|clearslash}</a>
        </div>
    </div>
	<div class="fileteFotoVideoDia"></div>
    <div style="margin-top:10px;display:inline;float:left;position:relative;">        
        {if isset($frontpage_nos_img) AND preg_match('/\.jpg$/', $frontpage_nos_img) }
        <div style="margin-right:5px;text-align: center;">
            <a href="/seccion/suplementos/nos/" title="N&oacute;s">
                <img src="/media/images/nos/{$frontpage_nos_img}" border="0" />
            </a>
        </div>
        {/if}
        <div class="CContendorSuplementos">
            <img alt="" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
            <a style="color:#004B8E;" href="{$titulares_nos->permalink|default:"#"}">{$titulares_nos->title|clearslash}</a>
        </div>
    </div>
    <br /> <br />

    <a href="http://www.xornal.com/seccion/extras/">
        <img src="{$params.IMAGE_DIR}logos/extras.jpg" alt="Extras" style="margin-top: 20px; margin-bottom: 5px;margin-left: 25px;" />
    </a>

    <div class="separadorHorizontal"></div>
    <div style="margin-top:10px;display:inline;float:left;position:relative;">
        <a href="/seccion/extras/libros/">
            <img src="{$params.IMAGE_DIR}logos/libros.jpg" alt="Contexto" />
        </a><div class="txt_desc_extras" />&Uacute;ltimas novedades literarias</div>
        <div style="margin-top:5px;color:#004B8E;display:inline;float:left;font-family:Arial;font-size:12px;margin-top:5px;position:relative;width:180px;">
            <img alt="" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
            <a style="color:#004B8E;" href="{$titulares_libros->permalink|default:"#"}">{$titulares_libros->title|clearslash}</a>
        </div>
    </div>
	<div class="fileteFotoVideoDia"></div>
    <div style="margin-top:10px;display:inline;float:left;position:relative;">
        <a href="/seccion/extras/juridica/">
            <img src="{$params.IMAGE_DIR}logos/juridica.jpg" alt="Contexto" />
        </a><div class="txt_desc_extras" />El despacho de abogados Rivas & Montero analiza la actualidad jur&iacute;dica</div>
        <div style="margin-top:5px;color:#004B8E;display:inline;float:left;font-family:Arial;font-size:12px;margin-top:5px;position:relative;width:180px;">
            <img alt="" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
            <a style="color:#004B8E;" href="{$titulares_juridica->permalink|default:"#"}">{$titulares_juridica->title|clearslash}</a>
        </div>
    </div>
	<div class="fileteFotoVideoDia"></div>
    <div style="margin-top:10px;display:inline;float:left;position:relative;">
        <a href="/seccion/extras/prensa/">
            <img src="{$params.IMAGE_DIR}logos/laprensa.jpg" alt="Contexto" />
        </a><div class="txt_desc_extras" />An&aacute;lisis diario de la prensa gallega y espa&ntilde;ola</div>
        <div style="margin-top:5px;color:#004B8E;display:inline;float:left;font-family:Arial;font-size:12px;margin-top:5px;position:relative;width:180px;">
            <img alt="" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
            <a style="color:#004B8E;" href="{$titulares_prensa->permalink|default:"#"}">{$titulares_prensa->title|clearslash}</a>
        </div>
    </div>
	<div class="fileteFotoVideoDia"></div>
    <div style="margin-top:10px;display:inline;float:left;position:relative;">
        <a href="/seccion/extras/mirada-global/">
            <img src="{$params.IMAGE_DIR}logos/miradaglobal.jpg" alt="Contexto"/>
        </a><div class="txt_desc_extras" />El IGADI analiza la actualidad internacional</div>
        <div style="margin-top:5px;color:#004B8E;display:inline;float:left;font-family:Arial;font-size:12px;margin-top:5px;position:relative;width:180px;">
            <img alt="" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
            <a style="color:#004B8E;" href="{$titulares_mirada->permalink|default:"#"}">{$titulares_mirada->title|clearslash}</a>
        </div>
    </div>
    
    	<div class="fileteFotoVideoDia"></div>
    <div style="margin-top:10px;display:inline;float:left;position:relative;">
        <a href="/seccion/extras/esculca/">
            <img src="{$params.IMAGE_DIR}logos/esculca.jpg" alt="Contexto" />
        </a>
        <div style="margin-top:5px;color:#004B8E;display:inline;float:left;font-family:Arial;font-size:12px;margin-top:5px;position:relative;width:180px;">
            <img alt="" src="{$params.IMAGE_DIR}fotoVideoDia/flechitaAzul.gif"/>
            <a style="color:#004B8E;" href="{$titulares_esculca->permalink|default:"#"}">{$titulares_esculca->title|clearslash}</a>
        </div>
    </div>

    <div class="separadorHorizontal"></div>

    <div class="contBannerYTextoPublicidadCol3">
        {* renderbanner banner=$banner15 photo=$photo15 cssclass="contBannerPublicidadCol3" width="180" height="*"
            beforeHTML='<div class="textoBannerPublicidad">publicidad</div>' *}
        {insert name="renderbanner" type=15 cssclass="contBannerPublicidadCol3" width="180" height="*"
            beforeHTML='<div class="textoBannerPublicidad">publicidad</div>'}
    </div>
    <div class="separadorHorizontal"></div>
</div>
