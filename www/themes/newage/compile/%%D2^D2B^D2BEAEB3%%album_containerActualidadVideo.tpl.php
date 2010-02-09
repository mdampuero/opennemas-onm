<?php /* Smarty version 2.6.18, created on 2010-01-28 13:01:27
         compiled from album_containerActualidadVideo.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'album_containerActualidadVideo.tpl', 20, false),array('modifier', 'escape', 'album_containerActualidadVideo.tpl', 20, false),)), $this); ?>
<div class="containerNoticias">
    <div class="column12">        
        <div class="containerCol12 fondoContainerActualidad">
            <div class="column1">
            
                <div class="zonaVisorVideos">
                    <div class="cabeceraVisorVideos"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
/galeriaVideos/cabeceraGaleriaVid.gif" alt="imagen"></div>
                    
                    <div class="cuerpoVisorVideos">
                        <div class="contVisorVideo">
                            <object width="370" height="268">
                                    <param name="movie" value="http://www.youtube.com/v/<?php echo $this->_tpl_vars['video']->videoid; ?>
"></param>
                                    <param name="allowFullScreen" value="true"></param>
                                    <param name="allowscriptaccess" value="always"></param>
                                    <embed src="http://www.youtube.com/v/<?php echo $this->_tpl_vars['video']->videoid; ?>
" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="370" height="268"></embed>
                            </object>
                        </div>
                        <div class="contFlechaTextoGaleria">
                            <div class="flechaVideoGaleria"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
/galeriaVideos/flechaTextoGaleriaVid.gif" alt="imagen"></div>
                            <div class="textoVideoGaleria"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['video']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</div>
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
                    <?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['videos']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
                        <div class="elementoMediaSelec">
                            <div class="fotoElemMedia" style="background-color:#000;">
		                        <span class="CEdgeThumbVideo"></span>
		                        <span class="CContainerThumbVideo"><img width="75" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo $this->_tpl_vars['videos'][$this->_sections['i']['index']]->title; ?>
" src="http://i4.ytimg.com/vi/<?php echo $this->_tpl_vars['videos'][$this->_sections['i']['index']]->videoid; ?>
/default.jpg"/></span>
                            </div>
                            <div class="contTextoElemMedia">
                                <div class="textoElemMedia">
                                    <a href="<?php echo $this->_tpl_vars['videos'][$this->_sections['i']['index']]->permalink; ?>
"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>
                                </div>
                            </div>
                        </div>
					<?php endfor; endif; ?>
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
					<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['others_videos']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
	                <div class="elementoListadoMediaPag">
	                    <div class="fotoElemMediaListado" style="background-color:#000;">
							<span class="CEdgeThumbVideo"></span>
							<span class="CContainerThumbVideo"><img width="80" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['others_videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['others_videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="http://i4.ytimg.com/vi/<?php echo $this->_tpl_vars['others_videos'][$this->_sections['i']['index']]->videoid; ?>
/default.jpg" /></span>
	                    </div>
	                    <div class="contSeccionFechaListado">
	                        <div class="seccionMediaListado"><a href="<?php echo $this->_tpl_vars['others_videos'][$this->_sections['i']['index']]->permalink; ?>
" style="color:#004B8D;"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['others_videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a></div>
	                        <div class="fechaMediaListado"><?php echo $this->_tpl_vars['others_videos'][$this->_sections['i']['index']]->changed; ?>
</div>
	                    </div>
	                    <div class="contTextoElemMediaListado">
	                        <div class="textoElemMediaListado">
	                            <a href="<?php echo $this->_tpl_vars['others_videos'][$this->_sections['i']['index']]->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['others_videos'][$this->_sections['i']['index']]->description)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
	                        </div>   
	                    </div>
	                    <div class="fileteIntraMedia"></div>
	                </div>
					<?php endfor; endif; ?>
					 <div class="posPaginadorGaliciaTitulares">
						<div class="CContenedorPaginado">
							<div class="link_paginador">+ Videos</div>
							<div class="CPaginas"> <?php echo $this->_tpl_vars['pages']->links; ?>
						
							</div>
						</div>
					</div>
				</div>

               
			</div>
		</div>
	</div>
	<div class="column3">
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_column3_containerFotoVideoDiaMasListado.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <div class="separadorHorizontal"></div> 
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_weather.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>
</div>