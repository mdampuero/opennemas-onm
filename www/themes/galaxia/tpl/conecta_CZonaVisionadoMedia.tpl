{* Visualizacion polls *}
<div class="CZonaVisionadoMedia">
    <div class="CVisorInfoMediaEncuesta">
        <div class="CContainerInfoMediaEncuesta">
        	<form name="enquisa" method="post" action="#" > 		        
	            <div class="CContainerSeccionVotosInfoMedia"><div class="CSeccionInfoMediaEncuesta"> {$poll->subtitle|clearslash}</div>
	            		<div class="COtrosInfoMediaEncuesta">Votos: {$poll->total_votes}</div>
	            		
	            </div>
                <div style="clear:both;"></div>
	            <div class="CTitularEncuesta">{$poll->title|clearslash}</div>
	            <div class="CZonaRespuestasEncuesta">           
	                 {section name=i loop=$items} 
			                <div class="CRespuestaEncuesta">
			                   <div class="CRadioEncuesta"><input type="radio" value="{$items[i].pk_item}" name="respEncuesta" alt="{$items[i].item}"/></div>
			                   <div class="CTextoRespuestaEnc">{$items[i].item}</div>
			                </div>
			                <div class="separadorHorizontalRespuesta"></div>
	                {/section}
	            </div>
	            
	            <div class="CBotonVotarEncuesta">
	               <input type="hidden" name="op" value='votar'/>          
	       		 <a onClick="document.enquisa.submit()" id="enquisa" >
	           		 <img alt="imagen" src="{$params.IMAGE_DIR}encuestas/botonVotarEncuesta.gif"/>
	            </a></div>
            </form>
            <br />
            <img src="/conecta/enquisa/v{$poll->id}.png" border="0" title="{$poll->title}" />

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
          {assign var="polls" value=$arrayPolls}        
          {section name=c loop=$polls}
		   		 <div class="elementoListadoMediaPag">
		            <div class="contSeccionFechaListado">
		                <div class="seccionMediaListado"><a href="/conecta/enquisa/{$arrayPolls[c]->id}.html" style="color:#004B8D;">{$arrayPolls[c]->subtitle|clearslash}</a></div>
		                <div class="fechaMediaListado"> {$polls[c]->changed}</div>
		            </div>
		            <div class="contTextoElemMediaListado">
		                <div class="textoElemMediaListado">
		                    <a href="/conecta/enquisa/{$polls[c]->id}.html"> {$polls[c]->title|clearslash}</a>
		                </div>
		            </div>
		            <div class="fileteIntraMedia"> </div>
		        </div>
         
		  {/section}
    
	    	<div class="posPaginadorGaliciaTitulares">
				<div class="CContenedorPaginado">
					<div class="link_paginador">+ Encuestas</div>
					<div class="CPaginas">
					{$pages->links}		
					</div>
				</div>
			</div>
	</div>
</div>