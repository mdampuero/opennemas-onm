<div class="containerVerano">
    <a href="/seccion/sociedad/xornal-de-veran/"><img src="{$params.IMAGE_DIR}veran/xornal-veran-logo.jpg" width="300" alt="Xornal Veran"/></a>

    <a href="/tempo/veran/"><img src="{$params.IMAGE_DIR}veran/otempo.jpg" style="margin-bottom: -3px;" width="300" alt="Xornal Veran" /></a>
    
    <div class="listaNoticiasXPress">
        <!-- NOTICIA XPRES -->
        <div style="width:300px;text-align:justify;background-color:#DCDFEE;margin:2px 0 2px 0;font-weight:bold;" class="noticiaXPress">
        {section name=exp loop=$articles_xornalveran}
            <div style="width:290px;margin:5px 0 5px 5px;" class="contTextoFilete">
                <img src="{$params.IMAGE_DIR}home_noticias/flechitaRoja.gif"/>
                <a href="{$articles_xornalveran[exp]->permalink}">{$articles_xornalveran[exp]->title|clearslash}</a>
            </div>
        {/section}
            <div style="width:290px;margin:5px 0 5px 5px;" class="contTextoFilete">
                <img src="{$params.IMAGE_DIR}home_noticias/flechitaRoja.gif"/>&nbsp;<a href="{$articles_relatoveran->permalink}">Relato de verano: {$articles_relatoveran->title|clearslash}</a>
            </div>
        </div>
    </div>
    <a href="/seccion/sociedad/axenda-de-veran/"><img src="{$params.IMAGE_DIR}veran/axenda.jpg" width="300" alt="Xornal Veran" style="margin-bottom: -3px;"/></a>
        <!-- NOTICIA XPRES -->
        <div style="height:175px;overflow-x:hidden;overflow-y:scroll;width:300px;text-align:justify;background-color:#DCDFEE;margin:2px 0 2px 0;margin-top-style: solid;margin-top-color:white;" class="noticiaXPress">
        {section name=exp loop=$articles_axendaveran}
            <div style="width:270px;margin:5px 0 5px 5px;">
                <div style="margin: 5px 0pt 5px 0px; color: black; display: inline; float: left; height: 15px; position: relative; width: 270px;">
                    <img src="{$params.IMAGE_DIR}home_noticias/flechitaRoja.gif"/>&nbsp;{$articles_axendaveran[exp]->subtitle|clearslash|capitalize}</div>
                <div style="font-weight:bold;border-bottom: 1px solid rgb(255, 255, 255); border-top-style: solid; border-right-style: solid; border-top-width: 0px; border-right-width: 0px; clear: both;">
                    <a href="{$articles_axendaveran[exp]->permalink}">{$articles_axendaveran[exp]->title|clearslash}</a></div>
            </div>
        {/section}
    </div>
</div>