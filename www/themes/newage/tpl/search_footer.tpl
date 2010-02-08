<div class="footer">

  {include file="modulo_sections_menu.tpl"}

    <div class="zonaHoraBusqueda">
	<div class="zonaHoraFecha">
	    {php}$arrMonth = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
		$arrDay = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
		echo $arrDay[date("w")].', '.date ("d"). ' de ' .$arrMonth[date("n")-1]. ' de ' .date("Y").' - '. date ('G:i'). ' h';
	    {/php}
	</div>
	<div class="zonaBusquedaBarraHora">
	    <div class="elemMenuBarraFecha"><a href="#">Hemeroteca</a></div>
	    <div class="separadorElemMenuBarraFecha"></div>
	    <div class="elemMenuBarraFecha"><a href="#">Servicios</a></div>
	    <div class="separadorElemMenuBarraFecha"></div>

	    <div class="containerBusqueda">
		<div class="elemMenuBarraFecha">Buscar en:</div>
		<div class="cajaBusqueda"><input class="textoABuscar" name="textoABuscar" type="text"/></div>
		<div class="destinoBusqueda">
		    <div class="radioBusqueda"><input type="radio" name="tipoBusqueda" /></div>
		    <div class="dondeBuscar">XornaldeGalicia</div>
		</div>
		<div class="destinoBusqueda">
		    <div class="radioBusqueda"><input type="radio" name="tipoBusqueda"/></div>
		    <div class="dondeBuscar">Google</div>
		</div>
	    </div>
	</div>
    </div>


<div class="separadorHorizontal"></div>

<div class="separadorBanners">
    <div class="banner728x90"><img src="media/728x90google.gif" alt="Google" /></div>
    <div class="banner234x90"><img src="media/200x90_4google.gif" alt="Google" /></div>
</div> 

<div class="separadorHorizontal"></div>

<div class="CContainerZonaLinksFooter">
    <div class="CLinksXornalGalileo">
	<div class="CLinkXornal"><a href="http://www.xornaldegalicia.com">XORNAL DE GALICIA</a></div>
	<div class="CSeparadorLinkXornal"></div>
	<div class="CLinkXornal"><a href="#">Galileo Galilei</a></div>
    </div>
    <div class="menuInferiorInt">
	<div class="menuCabeceraSecundario">
	    <ul>
		    <li id="Li12" class="opcion"><div class="CSeparadorMenuFlecha"></div><a href="#">Qui&eacute;nes somos</a></li>
		    <li id="Li13" class="opcion"><div class="CSeparadorMenuFlecha"></div><a href="#">Boletines</a></li>
		    <li id="Li14" class="opcion"><div class="CSeparadorMenuFlecha"></div><a href="#">Alertas</a></li>
		    <li id="Li15" class="opcion"><div class="CSeparadorMenuFlecha"></div><a href="#">Tarifas publicitarias</a></li>
		    <li id="Li16" class="opcion"><div class="CSeparadorMenuFlecha"><!--img src="{$params.IMAGE_DIR}flechitaMenu.gif" alt=""/--></div><a href="#">Contacto</a></li>
	    </ul>
	</div>
    </div>
</div>

</div>
