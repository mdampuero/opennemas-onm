<div class="column2Noticia">
  <!-- **************** NOTICIAS RECOMENDADAS***************** -->
    <div class="CContainerRecomendaciones">
        <div class="CCabeceraRecomendaciones">
              {if $other_opinions}Otros art&iacute;culos de {$author_name|clearslash} {/if}
        </div>
        <div class="CListaRecomendaciones">
            <!-- TITULAR RECOMENDACION-->
            <div class="CRecomendacion">
                {section name=a loop=$other_opinions}
                    <div class="CContainerIconoTextoRecomendacion">
                    <div class="iconoRecomendacion"></div>
                    <div class="textoRecomendacion"><a href="{$other_opinions[a]->permalink}">{$other_opinions[a]->title|clearslash}</a></div>
                    </div>
                    <div class="fileteRecomendacion"></div>
                {/section}
            </div>
        </div>
    </div>

    <div class="CContainerOtrasOpiniones" id="list_authors">
        <a href="/seccion/opinion/" id="cabeceraOpinion"></a>
        { include file="modulo_opinion_lista_xornalistas.tpl"}
    </div>

    <!-- ********************* PUBLICIDAD ********************** -->
    <div class="separadorHorizontal"></div>
    <div class="contBannerYTextoPublicidad">
    {insert name="renderbanner" type=5 cssclass="contBannerPublicidad" width="295" height="295"
        beforeHTML='<div class="textoBannerPublicidad">publicidad</div>'}
    </div>
    <div class="separadorHorizontal"></div>
    <!-- ********************* PUBLICIDAD ********************** -->
    <!-- ********************* PESTAÑAS ************************* -->
    {if isset($articles_viewed) && !empty($articles_viewed)}
        {include file=modulo_content_vistados_comentados.tpl"}
    {/if}
</div>