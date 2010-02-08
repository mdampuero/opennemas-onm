    <!-- ********************* PESTAÑAS ************************* -->
    <div class="containerNoticiasMasVistasYValoradas">
        <!-- *************** PESTANYAS **************** -->
        <div class="zonaPestanyas">
            <div class="pestanyaON" id="pestanha0">
                <div class="pestanya">
                    <div class="flechaPestanya"></div>
                    {if preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME)}
                        <div class="textoPestanya" onclick="{literal}get_plus_content('Opinion',{container:'pestanha0'});{/literal}">Opiniones + vistas</div>
                    {else}
                        <div class="textoPestanya" onclick="{literal}get_plus_content('Article',{container:'pestanha0',category:'{/literal}{$article->category}{literal}'});{/literal}">Noticias + vistas</div>
                    {/if}
                </div>
                <div class="cierrePestanya"></div>
            </div>

            <div class="espacioInterPestanyas"></div>

            <div class="pestanyaOFF" id="pestanha1">
                <div class="pestanya">
                    <div class="flechaPestanya"></div>
                    {if preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME)}
                        <div class="textoPestanya" onclick="{literal}get_plus_content('Opinion',{container:'pestanha1',author:'{/literal}{$opinion->fk_author}{literal}'});{/literal}">+ vistas del autor</div>
                    {else}
                        <div class="textoPestanya" onclick="{literal}get_plus_content('Comment',{container:'pestanha1',category:'{/literal}{$article->pk_fk_content_category}{literal}'});{/literal}">Noticias + comentadas</div>
                    {/if}
                </div>
                <div class="cierrePestanya"></div>
            </div>
        </div>
        <!-- ************* LISTA DE NOTICIAS ********** -->
        <div id="div_articles_viewed" class="CListaNoticiasMas">
            <!-- TITULAR RECOMENDACION-->
            {section name=view loop=$articles_viewed}
            <div class="CNoticiaMas">
                <div class="CContainerIconoTextoNoticiaMas">
                    <div class="iconoNoticiaMas"></div>
                    <div class="textoNoticiaMas"><a href="{$articles_viewed[view]->permalink}">{$articles_viewed[view]->title|clearslash}</a></div>
                </div>
                <div class="fileteNoticiaMas"><img src="{$params.IMAGE_DIR}noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>
            </div>
            {/section}
        </div>
    <!-- ************ PAGINADO ************** -->
    </div>
