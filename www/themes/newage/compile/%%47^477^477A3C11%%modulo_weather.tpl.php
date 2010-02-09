<?php /* Smarty version 2.6.18, created on 2010-01-25 20:30:46
         compiled from modulo_weather.tpl */ ?>
<div class="containerTiempo">
    <div class="cabeceraTiempo">
        <a href="/tempo"><img alt="El TiempoXornal" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
tiempo/logoCabeceraTiempo.gif"/></a>
    </div>
    <div class="cuerpoTiempo">
        <div class="zonaMapaTiempo">
            <img src="<?php echo @MEDIA_PATH_URL; ?>
/weather/mapa.gif" usemap="#mapTempoColumna" />
            <map name="mapTempoColumna">
                <area shape="circle" coords="39,54,5" href="/tempo/santiago.html" title="Tiempo en Santiago" />
                <area shape="circle" coords="58,19,6" href="/tempo/ferrol.html" title="Tiempo en Ferrol" />
                <area shape="circle" coords="52,31,5" href="/tempo/acoruna.html" title="Tiempo en A Coruña" />
                <area shape="circle" coords="27,92,5" href="/tempo/pontevedra.html" title="Tiempo en Pontevedra" />
                <area shape="circle" coords="28,106,4" href="/tempo/vigo.html" title="Tiempo en Vigo" />
                <area shape="circle" coords="71,103,5" href="/tempo/ourense.html" title="Tiempo en Ourense" />
                <area shape="circle" coords="94,54,5" href="/tempo/lugo.html" title="Tiempo en Lugo" />
            </map>
        </div>
        <div class="listaEnlacesFotoVideoDia">
            <div class="enlaceFotoVideoDia">
                <div class="enlaceFotoVideoDia">
                    <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
fotoVideoDia/flechitaAzul.gif" alt=""/>
                    <a href="<?php echo @SITE_URL; ?>
tempo/acoruna.html" title="Predicción del tiempo en A Coruña">
                        A Coru&ntilde;a</a>
                </div>
                <div class="fileteFotoVideoDia"></div>
            </div>
            <div class="enlaceFotoVideoDia">
                <div class="enlaceFotoVideoDia">
                    <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
fotoVideoDia/flechitaAzul.gif" alt=""/>
                    <a href="<?php echo @SITE_URL; ?>
tempo/ferrol.html" title="Predicción del tiempo en Ferrol">
                        Ferrol</a>
                </div>
                <div class="fileteFotoVideoDia"></div>
            </div>
            <div class="enlaceFotoVideoDia">
                <div class="enlaceFotoVideoDia">
                    <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
fotoVideoDia/flechitaAzul.gif" alt=""/>
                    <a href="<?php echo @SITE_URL; ?>
tempo/lugo.html" title="Predicción del tiempo en Lugo">
                        Lugo</a>
                </div>
                <div class="fileteFotoVideoDia"></div>
            </div>
            <div class="enlaceFotoVideoDia">
                <div class="enlaceFotoVideoDia">
                    <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
fotoVideoDia/flechitaAzul.gif" alt=""/>
                    <a href="<?php echo @SITE_URL; ?>
tempo/ourense.html" title="Predicción del tiempo en Ourense">
                        Ourense</a>
                </div>
                <div class="fileteFotoVideoDia"></div>
            </div>
            <div class="enlaceFotoVideoDia">
                <div class="enlaceFotoVideoDia">
                    <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
fotoVideoDia/flechitaAzul.gif" alt=""/>
                    <a href="<?php echo @SITE_URL; ?>
tempo/pontevedra.html" title="Predicción del tiempo en Pontevedra">
                        Pontevedra</a>
                </div>
                <div class="fileteFotoVideoDia"></div>
            </div>
            <div class="enlaceFotoVideoDia">
                <div class="enlaceFotoVideoDia">
                    <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
fotoVideoDia/flechitaAzul.gif" alt=""/>
                    <a href="<?php echo @SITE_URL; ?>
tempo/santiago.html" title="Predicción del tiempo en Santiago">
                        Santiago</a>
                </div>
                <div class="fileteFotoVideoDia"></div>
            </div>
            <div class="enlaceFotoVideoDia">
                <div class="enlaceFotoVideoDia">
                    <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
fotoVideoDia/flechitaAzul.gif" alt=""/>
                    <a href="<?php echo @SITE_URL; ?>
tempo/vigo.html" title="Predicción del tiempo en Vigo">
                        Vigo</a>
                </div>
                <div class="fileteFotoVideoDia"></div>
            </div>
        </div>
        <div class="fuenteTiempo">
            Fuente:
            <a href="http://www.aemet.es" title="Instituto Nacional de Meteorolog&iacute;a">
                Instituto Nacional de Meteorolog&iacute;a</a>
        </div>
    </div>
</div>