<div class="column2">
    {*
    <iframe src="/media/widgets/twitterhome/index.php" width="280" height="335" scrolling="no" frameborder="0" style="border: 0px solid #fff" noresize="noresize"></iframe>
            <div class="separadorHorizontal"></div>
    <iframe src="/media/widgets/twitterhome_2/index.php" width="280" height="335" scrolling="no" frameborder="0" style="border: 0px solid #fff" noresize="noresize"></iframe>
            <div class="separadorHorizontal"></div>
    *}

    {*	::::::::::::::::: BANNER OBRADOIRO ::::::::::::::::::::: *}

    {*	::::::::::::::::: FIN BANNER OBRADOIRO ::::::::::::::::: *}

    {*	::::::::::::::::: BANNER EXCLUSIVO ::::::::::::::::::::: *}
    {*	<object width="300" height="163">
            <param name="wmode" value="opaque" />
            <param name="movie" value="http://www.xornal.com/media/statics/flash/banner_exclusivo_sup.swf">
            <embed wmode="opaque" src="http://www.xornal.com/media/statics/flash/banner_exclusivo_sup.swf" width="300" height="163"></embed>
        </object> *}
    {*	::::::::::::::::: FIN BANNER EXCLUSIVO ::::::::::::::::: *}

    {*	::::::::::::::::: EXCLUSIVAS IMPRESAS :::::::::::::::::: *}

        {if strtolower($category_name) == 'home' && $articles_impresa}
            {include file="container_noticias_impresa.tpl"}
            <div class="separadorHorizontal"></div>
        {/if}

    {*	::::::::::::::  FIN EXCLUSIVAS IMPRESAS  ::::::::::::::: *}

    <!-- ::::::::::::::::::   WIDGET DEPORTES :::::::::::::: ::::::: -->

        { if strtolower($category_name) == 'deportes' } {* || strtolower($category_name) == 'home'*}
            {* TWITTER DEPOR *}
            {*	<iframe src="/media/widgets/twitterhome/index.php" width="280" height="335" scrolling="no" frameborder="0" style="border: 0px solid #fff" noresize="noresize"></iframe>
                    <div class="separadorHorizontal"></div> *}

            {* TWITTER CELTA *}
            {*	<iframe src="/media/widgets/twitterhome_2/index.php" width="280" height="335" scrolling="no" frameborder="0" style="border: 0px solid #fff" noresize="noresize"></iframe>
                    <div class="separadorHorizontal"></div>*}

            {* RESULTADOS LIGA *}
            <iframe src="/media/widgets/deportes/index.php" style="border: 0px solid #ffffff" marginwidth=0 marginheight=0 scrolling=no width=300 height=600 noresize="noresize" frameborder="0" border="0"></iframe>
            <div class="separadorHorizontal"></div>

        {else}
            {include file="index_noticias_express.tpl"}
                <div class="separadorHorizontal"></div>

        { /if }

    <!-- :::::::::::::::: FIN  WIDGET DEPORTES :::::::::::::::::::: -->    

    {if strtolower($category_name) == 'economia'}
        {include file="container_col2_economia_grafico.tpl"}
        <div class="separadorHorizontal"></div>
    {/if}

    {*<!--- Especial Torre de Hercules
    <div class="contBannerYTextoPublicidad">
    <a href='http://www.xornal.com/seccion/galicia/la-torre--patrimonio/'><img src='http://www.xornal.com/files/torre.jpg' width="300px" alt='A Torre de Hercules: Patrimonio da Humanidade' border='0'></a>
    </div>

    <div class="separadorHorizontal"></div-->*}

    {if isset($smarty.request.page) && $smarty.request.page>0}
         {renderitems items=$column filter="\$i==1" tpl="container_article_col2.tpl"}
    {else}
        {if strtolower($category_name) == 'home'}
           {renderplaceholder items=$column tpl='container_article_col2_destacado.tpl' placeholder="placeholder_1_0"}
        {else}
           {renderplaceholder items=$column tpl='container_article_col2.tpl' placeholder="placeholder_1_0"}
        {/if}
    {/if}
    
    {if #container_noticias_gente# == 1}
        {include file="index_gente.tpl"}
    {/if}
    {if #container_noticias_fotos# == 1}
        {include file="modulo_actualidadfotos.tpl"}
    {/if}

    <div class="separadorHorizontal"></div>

    <!-- ****************** PUBLICIDAD ******************* -->
    <div class="contBannerYTextoPublicidad">
    {insert name="renderbanner" type=5 cssclass="contBannerPublicidad" width="300" height="300"
        beforeHTML='<div class="textoBannerPublicidad">publicidad</div>' afterHTML='<div class="separadorHorizontal"></div>'}
    </div>

    <!-- **************** ARTICLES MODULE ***************** -->
    
    {if isset($smarty.request.page) && $smarty.request.page>0}
        {renderitems items=$column filter="\$i%2==1 && \$i>=3 && \$i<=7" tpl="container_article_col2.tpl"}
    {else}
        {renderplaceholder items=$column tpl='container_article_col2.tpl' placeholder="placeholder_1_1"}
    {/if}
    {*include file="index_1m.tpl"*}

    <!-- ****************** PUBLICIDAD ******************* -->
    <div class="contBannerYTextoPublicidad">
    {insert name="renderbanner" type=14 cssclass="contBannerPublicidad" width="300" height="300"
        beforeHTML='<div class="textoBannerPublicidad">publicidad</div>'  afterHTML='<div class="separadorHorizontal"></div>'}
    </div>
    

    <!-- **************** ARTICLES MODULE ***************** -->
    {if isset($smarty.request.page) && $smarty.request.page>0}
        {renderitems items=$column filter="\$i==9"  tpl="container_article_col2.tpl"}
    {else}
        {renderplaceholder items=$column tpl='container_article_col2.tpl' placeholder="placeholder_1_2"}
    {/if}
    
    <!-- **************** NOTICIA ESPECIAL *************** -->
    {if isset($smarty.request.page) && $smarty.request.page>0}
        {renderitems items=$column filter="\$i==11" tpl="container_article_col2_especial.tpl"}
    {else}
        {renderplaceholder items=$column tpl='container_article_col2_especial.tpl' placeholder="placeholder_1_3"}
    {/if}
    
    
    <!-- ****************** PUBLICIDAD ******************* -->
    <div class="contBannerYTextoPublicidad">
    {insert name="renderbanner" type=16 cssclass="contBannerPublicidad" width="300" height="300"
        beforeHTML='<div class="textoBannerPublicidad">publicidad</div>' afterHTML='<div class="separadorHorizontal"></div>'}
    </div>  

    <!-- ******************  Humor Grafico (album) **************** -->
     <div class="containerNoticiasXPress">
        <div class="cabeceraHumorGrafico"></div>
        <br />
           <a href="{$SITE_URL}{$alb_humor[0]->permalink}">
            <img width="295" src="{$SITE_URL}/media/images/{$humores.path_file}/{$humores.name}" alt=" {$alb_humor[0]->title}" title=" {$alb_humor[0]->title}" style="margin-top: 10px;"/></a>
          <div class="creditos2"><i>Gráfico: {$alb_humor[0]->title}</i></div>	     
   
    </div>
    <div class="separadorHorizontal"></div>
    <div class="containerNoticiasXPress">
	<a href="http://www.xornal.com/proxectoterra"><img border="0" alt="Proxecto Terra" src="http://www.xornal.com/files/bannerXentes.png"/></a>
    </div>
</div>