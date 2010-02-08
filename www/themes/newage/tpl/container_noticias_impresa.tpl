<div class="containerVerano">
    <a href="/estaticas/subscripcion/"><img src="{$params.IMAGE_DIR}exclusivo_papel.jpg" width="300" height="30" alt="Edici&oacute;n impresa"/></a>
    <div class="listaNoticiasXPress">
        <!-- NOTICIA XPRES -->
        <div style="width:300px;text-align:justify;background-color:#DCDFEE;margin:2px 0 2px 0;font-weight:bold;" class="noticiaXPress">
        {section name=exp loop=$articles_impresa}
            <div style="width:290px;margin:5px 0 5px 5px;" class="contTextoFilete">
                <a href="{$articles_impresa[exp]->permalink}">
                <img src="{$params.IMAGE_DIR}home_noticias/flechitaRoja.gif" />
			<span style="color: #004b7e; font-size: 13px;">
				{$articles_impresa[exp]->title|clearslash}</a>
			</span>
                <p><a href="{$articles_impresa[exp]->permalink}"><div style="font-weight:normal;">{$articles_impresa[exp]->summary|clearslash|strip_tags:false}</div>
		</a></p>
            </div>
        {/section}
        </div>
    </div>
{*        <div style="height:175px;overflow-x:hidden;overflow-y:scroll;width:300px;text-align:justify;background-color:#DCDFEE;margin:2px 0 2px 0;margin-top-style: solid;margin-top-color:white;" class="noticiaXPress">
        {section name=exp loop=$articles_impresa}
            <div style="width:270px;margin:5px 0 5px 5px;">
                <div style="margin: 5px 0pt 5px 0px; color: black; display: inline; float: left; height: 15px; position: relative; width: 270px;">
                    <img src="{$params.IMAGE_DIR}home_noticias/flechitaRoja.gif" />&nbsp;{$articles_impresa[exp]->subtitle|clearslash|capitalize}</div>
                <div style="font-weight:bold;border-bottom: 1px solid rgb(255, 255, 255); border-top-style: solid; border-right-style: solid; border-top-width: 0px; border-right-width: 0px; clear: both;">
                    <a href="{$articles_impresa[exp]->permalink}">{$articles_impresa[exp]->title|clearslash}
                    <p>{$articles_impresa[exp]->summary|clearslash}</p></a></div>
            </div>
        {/section}
    </div>*}
    <div style="clear:both"></div><a href="/estaticas/subscripcion/"><div class="CSeparadorMenuFlecha"></div>Suscr&iacute;bete a nuestra edici&oacute;n impresa</a>
</div>