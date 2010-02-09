<?php /* Smarty version 2.6.18, created on 2010-01-26 21:56:35
         compiled from modulo_footer.tpl */ ?>
<div class="footer">        
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_sections_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <div class="zonaHoraBusqueda">
        <div class="zonaHoraFecha">
            <?php $arrMonth = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
            $arrDay = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
            echo $arrDay[date("w")].', '.date ("d"). ' de ' .$arrMonth[date("n")-1]. ' de ' .date("Y").' - '. date ('G:i'). ' h';
             ?>
        </div>
        <div class="zonaBusquedaBarraHora" style="width: 440px;">
            <div style="float:right" class="containerBusqueda">
                <form action="/search.php" id="cse-search-box2">
                    <input type="hidden" name="cx" value="partner-pub-4524925515449269:kfaqom-99at" />
                    <input type="hidden" name="cof" value="FORID:10" />
                    <input type="hidden" name="ie" value="UTF-8" />
                    <div class="elemMenuBarraFecha">Buscar en:</div>
                    <div class="cajaBusqueda"><input class="textoABuscar" name="q" type="text" /></div>
                    <div class="destinoBusqueda">
                        <div class="radioBusqueda"><input type="radio" name="destino" value="xornal" checked="checked" onclick="cx.value='partner-pub-4524925515449269:kfaqom-99at'" /></div>
                        <div class="dondeBuscar">Xornal</div>
                    </div>
                    <div class="destinoBusqueda">
                        <div class="radioBusqueda"><input type="radio" name="destino" value="google" onclick="cx.value='partner-pub-4524925515449269:l5xds69cix0'" /></div>
                        <div class="dondeBuscar">Google&nbsp;</div>
                    </div>                    
                </form>
            </div>
        </div>
    </div>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_separadorbanners4.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <div class="separadorHorizontal"></div>
    <div class="CContainerZonaLinksFooter">
        <div class="CLinksXornalGalileo">
            <div class="CLinkXornal">XXXXXXXXXXXXXXXXX</div>
        </div>
        <div class="menuInferiorInt">
            <div class="menuCabeceraSecundario">
                                <ul>
                    <li id="Li12" class="opcion"><div class="CSeparadorMenuFlecha"></div><a href="/estaticas/quen/">Qui&eacute;nes somos</a></li>
                    <li id="Li13" class="opcion"><div class="CSeparadorMenuFlecha"></div><a href="/estaticas/contacto/">Contacto</a></li>
                    <li id="Li14" class="opcion"><div class="CSeparadorMenuFlecha"></div><a href="/estaticas/subscripcion/">Suscripciones</a></li>
                    <li id="Li15" class="opcion"><div class="CSeparadorMenuFlecha"></div><a href="/estaticas/publicidade/">Publicidad</a></li>
                </ul>
            </div>
        </div>    
    </div>    
</div>