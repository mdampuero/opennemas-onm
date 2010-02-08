<div class="column2big">   <!-- PIEZA ESPECIAL NOTICIA -->

    {include file="index_noticias_express.tpl"}

    <div class="separadorHorizontal"></div>
    {*if isset($smarty.request.page) && $smarty.request.page>0}
         {renderitems items=$column filter="\$i==1" tpl="container_article_col2.tpl"}
    {else}
        {renderplaceholder items=$column tpl='container_article_col2.tpl' placeholder="placeholder_1_0"}
    {/if*}
    
    {if #container_noticias_gente# == 1}
        {include file="index_gente.tpl"}
    {/if}
    {if #container_noticias_fotos# == 1}
        {include file="modulo_actualidadfotos.tpl"}
    {/if}

    <div class="separadorHorizontal"></div>

    <!-- ****************** PUBLICIDAD ******************* -->
    <div class="contBannerYTextoPublicidad">
    {* renderbanner banner=$banner5 photo=$photo5 cssclass="contBannerPublicidad" width="295" height="295"
    beforeHTML='<div class="textoBannerPublicidad">publicidad</div>' afterHTML='<div class="separadorHorizontal"></div>' *}
    {insert name="renderbanner" type=5 cssclass="contBannerPublicidad" width="295" height="295"
        beforeHTML='<div class="textoBannerPublicidad">publicidad</div>' afterHTML='<div class="separadorHorizontal"></div>'}
    </div>

    <!-- **************** ARTICLES MODULE ***************** -->
    
    {*if isset($smarty.request.page) && $smarty.request.page>0}
        {renderitems items=$column filter="\$i%2==1 && \$i>=3 && \$i<=7" tpl="container_article_col2.tpl"}
    {else}
        {renderplaceholder items=$column tpl='container_article_col2.tpl' placeholder="placeholder_1_1"}
    {/if*}
    {*include file="index_1m.tpl"*}

    <!-- ****************** PUBLICIDAD ******************* -->
    <div class="contBannerYTextoPublicidad">
    {* renderbanner banner=$banner14 photo=$photo14 cssclass="contBannerPublicidad" width="295" height="295"
    beforeHTML='<div class="textoBannerPublicidad">publicidad</div>'  afterHTML='<div class="separadorHorizontal"></div>' *}
    {insert name="renderbanner" type=14 cssclass="contBannerPublicidad" width="295" height="295"
        beforeHTML='<div class="textoBannerPublicidad">publicidad</div>'  afterHTML='<div class="separadorHorizontal"></div>'}
    </div>
    

    <!-- **************** ARTICLES MODULE ***************** -->
    {*if isset($smarty.request.page) && $smarty.request.page>0}
        {renderitems items=$column filter="\$i==9"  tpl="container_article_col2.tpl"}
    {else}
        {renderplaceholder items=$column tpl='container_article_col2.tpl' placeholder="placeholder_1_2"}
    {/if*}
    
    <!-- **************** NOTICIA ESPECIAL *************** -->
    {*if isset($smarty.request.page) && $smarty.request.page>0}
        {renderitems items=$column filter="\$i==11" tpl="container_article_col2_especial.tpl"}
    {else}
        {renderplaceholder items=$column tpl='container_article_col2_especial.tpl' placeholder="placeholder_1_3"}
    {/if*}
    
    
    <!-- ****************** PUBLICIDAD ******************* -->
    <div class="contBannerYTextoPublicidad">
    {* renderbanner banner=$banner16 photo=$photo16 cssclass="contBannerPublicidad" width="295" height="295"
        beforeHTML='<div class="textoBannerPublicidad">publicidad</div>' afterHTML='<div class="separadorHorizontal"></div>' *}
    {insert name="renderbanner" type=16 cssclass="contBannerPublicidad" width="295" height="295"
        beforeHTML='<div class="textoBannerPublicidad">publicidad</div>' afterHTML='<div class="separadorHorizontal"></div>'}
    </div>  
</div>