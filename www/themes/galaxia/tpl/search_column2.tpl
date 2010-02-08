<div class="column2Noticia">
	<div class="containerGaliciaTitulares">
        <div class="cabeceraGaliciaTitulares"></div>
        <div class="listaGaliciaTitulares">

            {* <!-- TITULARES --> *}
            {section name=exp loop=$articles_express}
                <div class="noticiaGaliciaTitulares">
                    <div class="iconoGaliciaTitulares"></div>
                    <div class="contTextoFilete">
                        <div class="textoGaliciaTitulares"><a href="/{$articles_express[exp]->permalink}">{$articles_express[exp]->title|clearslash}</a></div>
                        <div class="fileteGaliciaTitulares"><img src="{$params.IMAGE_DIR}galiciaTitulares/fileteDashedGaliciaTitulares.gif" alt=""/></div>
                    </div>
                </div>
            {/section}
            {* <!-- TITULARES --> *}

        </div>
        <div class="posPaginadorGaliciaTitulares">
            <div class="CContenedorPaginado">
                <div class="link_paginador">
                    <a href="#">+ Galicia titulares</a>
                </div>
                <div class="CPaginas">
                    <div class="numpagina_paginador">1</div>
                    <div class="separador_pag_paginador"></div>
                    <div class="numpagina_paginador"><a href="#">2</a></div>
                    <div class="separador_pag_paginador"></div>
                    <div class="numpagina_paginador"><a href="#">3</a></div>
                    <div class="separador_pag_paginador"></div>
                    <div class="numpagina_paginador"><a href="#">4</a></div>
                    <div class="separador_pag_paginador"></div>
                    <div class="numpagina_paginador"><a href="#">5</a></div>
                    <div class="separador_pag_paginador"></div>
                    <div class="numpagina_paginador"><a href="#">6</a></div>
                    <div class="separador_pag_paginador"></div>
                    <div class="numpagina_paginador"><a href="#">7</a></div>
                    <div class="separador_pag_paginador"></div>
                </div>
            </div>
		</div>
	</div>


	<div class="separadorHorizontal"></div>

	<!-- ****************** PUBLICIDAD ******************* -->
	<div class="contBannerYTextoPublicidad">
        <div class="textoBannerPublicidad">publicidad</div>
        <div class="contBannerPublicidad"><img src="media/banner208x208.gif" alt="" /></div>
	</div>



	<div class="separadorHorizontal"></div>



	<!-- **************** NOTICIAS RECOMENDADAS***************** -->
	 {if $adjuntos}
		<div class="CContainerRecomendaciones">

			<div class="CCabeceraRecomendaciones">
				Si te ha interesado esta informaci&oacute;n te recomendamos:
			</div>
			<div class="CListaRecomendaciones">

					{section name=r loop=$relationed}
					{if $relationed[r]->pk_article neq $article->pk_article}
							<!-- TITULAR RECOMENDACION-->
							<div class="CRecomendacion">
							<div class="CContainerIconoTextoRecomendacion">
								<div class="iconoRecomendacion"></div>
								<div class="textoRecomendacion">
									<a href="/{$relationed[r]->permalink}" {if $relationed[r]->content_type eq 3}target="_blank"{/if}>
                                        {$relationed[r]->title|clearslash}</a>
                                </div>
							</div>
							<div class="fileteRecomendacion"><img src="{$params.IMAGE_DIR}noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>
							</div>
					{/if}
					{/section}
					{section name=n loop=$adjuntos}
							<!-- TITULAR RECOMENDACION-->
							<div class="CRecomendacion">
							<div class="CContainerIconoTextoRecomendacion">
								<div class="iconoRecomendacion"></div>
								<div class="textoRecomendacion">
									<a href="media/files/{$adjuntos[n]->category_name}/{$adjuntos[n]->path|clearslash}">{$adjuntos[n]->title|clearslash}
									</a></div>
							</div>
							<div class="fileteRecomendacion"><img src="{$params.IMAGE_DIR}noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>
							</div>
					{/section}

			</div>

		</div>


		<div class="separadorHorizontal"></div>
	{/if}

	<!-- ********************* PESTAÑAS ************************* -->
	<div class="containerNoticiasMasVistasYValoradas">
	<!-- *************** PESTANYAS **************** -->
	<div class="zonaPestanyas">
		<div class="pestanya">
		<div class="pestanyaSelect">
			<div class="flechaPestanyaOn"></div>
			<div class="textoPestanyaSelec">Noticias + vistas</div>
		</div>
		<div class="cierrePestanyaSelect"></div>
		</div>

		<div class="espacioInterPestanyas"></div>

		<div class="pestanya">
		<div class="pestanyaNoSelect">
			<div class="flechaPestanyaOff"></div>
			<div class="textoPestanyaNoSelec">Noticias + valoradas</div>
		</div>
		<div class="cierrePestanyaNoSelect"></div>
		</div>
	</div>

	<!-- ************* LISTA DE NOTICIAS ********** -->
	<div class="CListaNoticiasMas">
	<!-- TITULAR RECOMENDACION-->

	{section name=view loop=$articles_viewed}
	      <div class="CNoticiaMas">
		<div class="CContainerIconoTextoNoticiaMas">
			<div class="iconoNoticiaMas"></div>
			<div class="textoNoticiaMas"><a href="/{$articles_viewed[view]->permalink}">{$articles_viewed[view]->title|clearslash}</a></div>
		</div>
		<div class="fileteNoticiaMas"><img src="{$params.IMAGE_DIR}noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>
	      </div>
	{/section}

	</div>

	<!-- ************ PAGINADO ************** -->
	<div class="posPaginadorNoticiasMas">
        <div class="CContenedorPaginado">
            <div class="link_mas_nota">
                <a href="#">Noticias + vistas</a>
            </div>
            <div class="CPaginas">
            <div class="numpagina_nota">1</div>
            <div class="separadorFirma"></div>
            <div class="numpagina_nota"><a href="#">2</a></div>
            <div class="separadorFirma"></div>
            <div class="numpagina_nota"><a href="#">3</a></div>
            <div class="separadorFirma"></div>
            <div class="numpagina_nota"><a href="#">4</a></div>
            <div class="separadorFirma"></div>
            <div class="numpagina_nota"><a href="#">5</a></div>
            <div class="separadorFirma"></div>
            <div class="numpagina_nota"><a href="#">6</a></div>
            <div class="separadorFirma"></div>
            <div class="numpagina_nota"><a href="#">7</a></div>
            <div class="separadorFirma"></div>
            </div>
        </div>
	</div>

	</div>

	<div class="separadorHorizontal"></div>

	{ include file="modulo_bookmarks.tpl" }

	<div class="CTextoNotaEnviarA">
		Nota: es posible que tengas que estar registrado y autentificado en estos servicios para poder anotar el contenido correctamente
	</div>

	</div>

