<div class="containerNoticias">
    <div class="column12">        
        <div class="containerCol12 fondoContainerActualidad">
            <div class="column1">
            
                <div class="zonaVisorVideos">
                    <div class="cabeceraVisorVideos"><img src="{$params.IMAGE_DIR}/galeriaVideos/cabeceraGaleriaVid.gif" alt="imagen"></div>
                    
                    <div class="cuerpoVisorVideos">
                        <div class="contVisorVideo">
                            <object width="370" height="268">
                                    <param name="movie" value="http://www.youtube.com/v/{$video->videoid}"></param>
                                    <param name="allowFullScreen" value="true"></param>
                                    <param name="allowscriptaccess" value="always"></param>
                                    <embed src="http://www.youtube.com/v/{$video->videoid}" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="370" height="268"></embed>
                            </object>
                        </div>
                        <div class="contFlechaTextoGaleria">
                            <div class="flechaVideoGaleria"><img src="{$params.IMAGE_DIR}/galeriaVideos/flechaTextoGaleriaVid.gif" alt="imagen"></div>
                            <div class="textoVideoGaleria">{$video->title|clearslash|escape:'html'}</div>
                        </div>
                    </div>
                </div>                                            
            </div>
            <div class="column2">
                <div class="contNuestraSeleccion">
                    <!-- PESTANYA -->
                    <div class="contUnicaPestanyaGrande">
                        <div class="pestanyaSelecList">
                            <div class="contInfoPestanyaGrande">
                                <div class="flechaPestanyaSelecList"></div>
                                <div class="textoPestanyaSelecList">NUESTRA SELECCIÓN</div>
                            </div>
                            <div class="cierrePestanyaSelecList"></div>
                        </div>
                        
                    </div>

                    <!-- LISTA DE ELEMENOS SELECCIONADOS -->
                    <div class="listaMediaSeleccionada">
                    {section name=i loop=$videos}
                        <div class="elementoMediaSelec">
                            <div class="fotoElemMedia" style="background-color:#000;">
		                        <span class="CEdgeThumbVideo"></span>
		                        <span class="CContainerThumbVideo"><img width="75" alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title}" src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg"/></span>
                            </div>
                            <div class="contTextoElemMedia">
                                <div class="textoElemMedia">
                                    <a href="{$videos[i]->permalink}">{$videos[i]->title|clearslash|escape:'html'}</a>
                                </div>
                            </div>
                        </div>
					{/section}
                    </div>                    
                </div>                
            </div>
        <div class="zonaClasificacionVideos">
            <div class="zonaPestanyasMedia">
                <!-- PESTANYA -->
                
                <div class="pestanyaSelecList">
                    <div class="contInfoPestanyaGrande">
                        <div class="flechaPestanyaSelecList"></div>
                        <div class="textoPestanyaSelecList">VIDEOS PASADOS</div>
                    </div>
                    <div class="cierrePestanyaSelecList"></div>
                </div>
            </div>                           
	            <div class="listadoMedia" id="div_videos">
					{section name=i loop=$others_videos}
	                <div class="elementoListadoMediaPag">
	                    <div class="fotoElemMediaListado" style="background-color:#000;">
							<span class="CEdgeThumbVideo"></span>
							<span class="CContainerThumbVideo"><img width="80" alt="{$others_videos[i]->title|clearslash|escape:'html'}" title="{$others_videos[i]->title|clearslash|escape:'html'}" src="http://i4.ytimg.com/vi/{$others_videos[i]->videoid}/default.jpg" /></span>
	                    </div>
	                    <div class="contSeccionFechaListado">
	                        <div class="seccionMediaListado"><a href="{$others_videos[i]->permalink}" style="color:#004B8D;">{$others_videos[i]->title|clearslash|escape:'html'}</a></div>
	                        <div class="fechaMediaListado">{$others_videos[i]->changed}</div>
	                    </div>
	                    <div class="contTextoElemMediaListado">
	                        <div class="textoElemMediaListado">
	                            <a href="{$others_videos[i]->permalink}">{$others_videos[i]->description|clearslash}</a>
	                        </div>   
	                    </div>
	                    <div class="fileteIntraMedia"></div>
	                </div>
					{/section}
					 <div class="posPaginadorGaliciaTitulares">
						<div class="CContenedorPaginado">
							<div class="link_paginador">+ Videos</div>
							<div class="CPaginas"> {$pages->links}						
							</div>
						</div>
					</div>
				</div>

               
			</div>
		</div>
	</div>
	<div class="column3">
    {include file="modulo_column3_containerFotoVideoDiaMasListado.tpl"}
        <div class="separadorHorizontal"></div> 
        {include file="modulo_weather.tpl"}
    </div>
</div>