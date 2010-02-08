<div class="column2Noticia">
<!-- **************** NOTICIAS SUGERIDAS ***************** -->
    {if count($suggested)>0}
        <div class="textoGaliciaTitulares"><img src="{$params.IMAGE_DIR}noticia/tambientepuedeinteresar.gif" alt="Tambien te puede interesar"/></div>
        <div class="CContainerRecomendaciones">
            <div class="CListaRecomendaciones">
                {section name=r loop=$suggested}
                    {if $suggested[r].pk_content neq $article->pk_article && $suggested[r].title}
                        <!-- TITULAR RECOMENDACION-->
                        <div class="CRecomendacion">
                            <div class="CContainerIconoTextoRecomendacion">
                                <div class="iconoRecomendacion"></div>
                                <div class="textoRecomendacion">
                                     <a href="{$suggested[r].permalink}">{$suggested[r].title|clearslash}</a>
                                </div>
                            </div>
                            <div class="fileteRecomendacion"><img src="{$params.IMAGE_DIR}noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>
                        </div>
                    {/if}
                {/section}
            </div>
        </div>
        <div class="separadorHorizontal"></div>
    {/if}

    {if $photoExt->name || $photoInt->name}
    
    <div class="textoGaliciaTitulares"><img src="{$params.IMAGE_DIR}noticia/fotosdelanoticia.gif" alt="Fotos de la Noticia"/></div>

        {if $photoExt->name}
        <div class="CNoticiaContenedorFoto">
            <!--div class="CCabeceraVideo"></div-->
            <a href="{$MEDIA_IMG_PATH_WEB}{$photoExt->path_file}{$photoExt->name}" class="lightwindow" rel=""  caption='{$article->img1_footer|clearslash}' title='{$article->title|clearslash}'>
                <div class="CNoticia_foto">
                   <img src="{$MEDIA_IMG_PATH_WEB}{$photoExt->path_file}{$photoExt->name}" title="{$article->title|clearslash}" alt="{$article->img1_footer|clearslash}" width="295"/>
                </div>
            </a>
            <div class="clear"></div>
            <div class="creditos_nota">{$article->img1_footer|clearslash}</div>
        </div>
        {/if}
        {if $photoInt->name}
        <div class="CNoticiaContenedorFoto">
            <!--div class="CCabeceraVideo"></div-->
            <a href="{$MEDIA_IMG_PATH_WEB}{$photoInt->path_file}{$photoInt->name}" class="lightwindow" rel=""  caption='{$article->img2_footer|clearslash}' title='{$article->title|clearslash}'>
                <div class="CNoticia_foto">
                   <img src="{$MEDIA_IMG_PATH_WEB}{$photoInt->path_file}{$photoInt->name}" title="{$article->title|clearslash}" alt="{$article->img2_footer|clearslash}" width="295"/>
                </div>
            </a>
            <div class="clear"></div>
            <div class="creditos_nota">{$article->img2_footer|clearslash}</div>
        </div>
        {/if}
        <div class="separadorHorizontal"></div>
    {/if}

    <!-- **************** NOTICIAS RECOMENDADAS***************** -->    
    {if isset($articles_express) && !empty($articles_express)}
    <div class="containerGaliciaTitulares">
        <div class="cabeceraGaliciaTitulares"></div>
        <div id="div_articles_express"  class="listaGaliciaTitulares">
            {* <!-- TITULARES --> *}
            {section name=exp loop=$articles_express}
                <div class="noticiaGaliciaTitulares">
                <div class="iconoGaliciaTitulares"></div>
                <div class="contTextoFilete">
                    <div class="textoGaliciaTitulares"><a href="{$articles_express[exp]->permalink}">{$articles_express[exp]->title|clearslash}</a></div>
                    <div class="fileteGaliciaTitulares"><img src="{$params.IMAGE_DIR}galiciaTitulares/fileteDashedGaliciaTitulares.gif" alt=""/></div>
                </div>
                </div>
            {/section}
            {* <!-- TITULARES --> *}
        </div>
    </div>
    {/if}
    
    <!-- ********************* PUBLICIDAD ********************** -->
    {* Banner columna interior 1 - posición 102 *}    
    {* renderbanner banner=$banner102 photo=$photo102 cssclass="banner295x295" beforeHTML=$beforeAdv *}
    {insert name="renderbanner" type=102 cssclass="banner295x295" beforeHTML=$beforeAdv}
    <div class="separadorHorizontal"></div>
    <!-- ********************* PUBLICIDAD ********************** -->

    <!-- ********************* PESTAÑAS ************************* -->
    {if isset($articles_viewed) && !empty($articles_viewed)}
        {include file="modulo_content_vistados_comentados.tpl"}
    {/if}    
    
    {* Banner columna interior 2 - posición 103 *}
    {* renderbanner banner=$banner103 photo=$photo103 cssclass="banner295x295" beforeHTML=$beforeAdv *}
    {insert name="renderbanner" type=103 cssclass="banner295x295" beforeHTML=$beforeAdv}    
</div>
