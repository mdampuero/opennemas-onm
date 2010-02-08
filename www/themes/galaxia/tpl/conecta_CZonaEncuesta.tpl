<div class="CZonaEncuesta">
    <div class="CVisorInfoEncuestaRes">
        <div class="CContainerEncuestaRes">
            <div class="CContainerSeccionVotosInfoMedia">
                <div class="CSeccionInfoMediaEncuesta"> {$poll->subtitle|clearslash}</div>
                <div class="COtrosInfoMediaEncuesta">Votos: {$poll->total_votes}</div>
                <div class="COtrosInfoMediaEncuesta">
                    {$ya_vote} 
                </div>
                <div class="CBotonVotarEncuestaArriba">
                   <a href="/conecta/enquisa/{$poll->id}.html" ><img alt="imagen" src="{$params.IMAGE_DIR}encuestas/botonVotarEncuesta.gif"/></a>	
                </div>
            </div>
            <div class="CTitularEncuestaRespuesta">{$poll->title|clearslash}</div>
            <div class="CContainerGraficoBarras">              
                {* "v" indica que é de tipo vertical *}
                <img src="/conecta/enquisa/v{$poll->id}.png" border="0" title="{$poll->title}" />
            </div>
        </div>
    </div>
</div>
{if $poll->with_comment eq '1'}
    {include file="modulo_copina.tpl" item=$poll}
{/if}

<div class="zonaClasificacionVideos">
    <div class="zonaPestanyasMedia">
        <!-- PESTANYA -->

        <div class="pestanyaSelecList">
            <div class="contInfoPestanyaGrande">
                <div class="flechaPestanyaSelecList"></div>
                <div class="textoPestanyaSelecList">MÁS ENCUESTAS</div>
            </div>
            <div class="cierrePestanyaSelecList"></div>
        </div>
    </div>
    <div class="listadoMedia" id="div_pc_{$accion}">
          {section name=c loop=$arrayPolls start=0}
      
		   		 <div class="elementoListadoMediaPag">
		            <div class="contSeccionFechaListado">
		                <div class="seccionMediaListado"><a href="/conecta/enquisa/{$arrayPolls[c]->id}.html" style="color:#004B8D;">{$arrayPolls[c]->subtitle|clearslash}</a></div>
		                <div class="fechaMediaListado"> {$arrayPolls[c]->changed}</div>
		            </div>
		            <div class="contTextoElemMediaListado">
		                <div class="textoElemMediaListado">
		                    <a href="/conecta/enquisa/{$arrayPolls[c]->id}.html">{$arrayPolls[c]->title|clearslash}</a>
		                </div>
		            </div>
		            <div class="fileteIntraMedia"></div>
		        </div>
          {sectionelse}
            <div class="CTitularEncuestaRespuesta">No hay más encuestas</div>
		  {/section}
		   <div class="posPaginadorGaliciaTitulares">
		<div class="CContenedorPaginado">
			<div class="link_paginador">+ Encuestas</div>
			<div class="CPaginas"> {$pages->links}					
			</div>
		</div>
	</div>
    </div>
   
</div>