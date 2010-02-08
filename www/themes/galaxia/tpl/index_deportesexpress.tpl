<div class="deportesExpress">
    <!-- ****************** DEPORTES XPRESS **************** -->
    <div class="containerDeportesXPress">
	<div class="cabeceraDeportesXPress"></div>
	<div id='div_deportes_express' class="listaDeportesXPress">	
		  {*<!-- DEPORTE XPRES -->*}
		  {section name=exp loop=$deportes_express}
		    <div class="deporteXPress">
		      <div class="horaDeporteXPress">{$deportes_express[exp]->changed|date_format:"%H:%M"}</div>
		      <div class="contTextoFileteDeporte">
			  <div class="textoDeporteXPress"><a href="{$deportes_express[exp]->permalink}">{$deportes_express[exp]->title|clearslash}</a></div>
			  <div class="fileteDeporteXPress"><img src="{$params.IMAGE_DIR}deportesXPress/fileteDashedDeportesXPress.gif" alt="" /></div>                                       
			</div>
		    </div>
		  {/section}
		    {*<!-- DEPORTE XPRES -->*}

			<!-- LINK A MAS DEPORTESXPRESS-->		  
			<div class="linkMasDeportes">+Deportes</div>
			<div class="CPaginas">{$pages_deportes_express->links}		
					</div>
		    </div>	 		
	</div>
    <!-- *************** FIN DEPORTES XPRESS *************** --> 
</div>