<?php /* Smarty version 2.6.18, created on 2010-01-25 21:43:49
         compiled from album_containerActualidadFoto.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'album_containerActualidadFoto.tpl', 32, false),array('modifier', 'escape', 'album_containerActualidadFoto.tpl', 32, false),array('modifier', 'date_format', 'album_containerActualidadFoto.tpl', 87, false),)), $this); ?>
<div class="containerActualidadFoto">
    <div class="column123">
        <div class="cabeceraVisorVideos">
            <?php if ($this->_tpl_vars['category_name'] == 'humor-grafico'): ?><img alt="imagen" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
noticiasXPress/logoHumorGrafico.jpg"/></div>
            <?php else: ?><img alt="imagen" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
galeriaFotos/cabeceraGaleriaFotos.gif"/></div>
            <?php endif; ?>
        <!-- PESTANYAS -->
        <div class="CContainerPestanyasActualidadFotos">
            <div class="pestanyaSelecList">
                <div class="contInfoPestanyaGrande">
                    <div class="flechaPestanyaSelecList"></div>
                    <div class="textoPestanyaSelecList">FOTOGALER&Iacute;AS MAS VISTAS</div>
                </div>
                <div class="cierrePestanyaSelecList"></div>
            </div>
            <div class="espacioInterPestanyasGrande"></div>
            <a href="/video">
            <div class="pestanyaNoSelecList">
                <div class="contInfoPestanyaGrande">
                    <div class="flechaPestanyaNoSelecList"></div>
                    <div class="textoPestanyaNoSelecList">ACTUALIDAD VIDEO</div>
                </div>
                <div class="cierrePestanyaNoSelecList"></div>
            </div>
            </a>
        </div>
        <!-- ***************** VISOR DE FOTOS **************-->
        <div class="zonaVisorFotos">
            <div class="cuerpoVisorFotos">
                <div class="contVisorFoto">
                  <?php if ($this->_tpl_vars['albumArray']): ?>
                    <a href="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['albumArray'][0]->path_file; ?>
<?php echo $this->_tpl_vars['albumArray'][0]->name; ?>
" class="lightwindow" rel='xornal[album]' title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['album']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" caption="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['albumDescrip'][0])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" author="<?php echo $this->_tpl_vars['album']->agency; ?>
" onClick="return false;">
                        <div class="CVisorRealFoto"><img alt="imagen" width="498px" height="340px" src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['albumArray'][0]->path_file; ?>
<?php echo $this->_tpl_vars['albumArray'][0]->name; ?>
"></div>
                        <div class="CBandaAzulVisorFoto">
                            <div class="CVerFotosVisorFotosBandaAzul">                          
                                <img alt="imagen" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
galeriaFotos/flechitaBlanca.gif"/>Haz clic para ver las fotos
                            </div>
                        </div>
                    </a>
                    <?php else: ?>
                        <div class="CVisorRealFoto"><img alt="imagen" width="498px" height="340px" src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['albumArray'][0]->path_file; ?>
<?php echo $this->_tpl_vars['albumArray'][0]->name; ?>
"></div>
                    <?php endif; ?>
                </div>
                <div class="marcoInfoFoto">
                    <div class="contInfoFoto">
                        <div class="posInfoFoto">
                            <div class="CTitularVisorFotos"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['album']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</div>
                            <div class="CTextoVisorFotos"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['album']->description)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</div>
                            <div class="CClickParaVerFotosVisorFotos">
                               <?php if ($this->_tpl_vars['albumArray']): ?>   <a href="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['albumArray'][1]->path_file; ?>
<?php echo $this->_tpl_vars['albumArray'][1]->name; ?>
" class="lightwindow" rel='xornal[album]' title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['album']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" caption="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['albumDescrip'][1])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" author="<?php echo $this->_tpl_vars['album']->agency; ?>
"><img alt="imagen" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
galeriaFotos/flechitaOscura.gif"/>Haz clic para ver las fotos</a> <?php endif; ?>
                            </div>
                        </div>
                        <div class="agenciaInfoFoto"><img alt="imagen" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
galeriaFotos/flechitaClara.gif"/><?php echo $this->_tpl_vars['album']->agency; ?>
</div>
                    </div>
                </div>
            </div>
        </div>
        <!--
            <?php echo '<a href="javascript: myLightWindow.activateWindow({'; ?>
href: '<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['albumArray'][0]->path_file; ?>
<?php echo $this->_tpl_vars['albumArray'][0]->name; ?>
', title: '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['album']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
', author: '<?php echo $this->_tpl_vars['album']->agency; ?>
', caption: '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['albumDescrip'][0])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
', rel: 'xornal[album]'<?php echo '});">'; ?>
<img alt="imagen" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
galeriaFotos/flechitaBlanca.gif"/>Haz clic para ver las fotos</a>
        -->
        <?php unset($this->_sections['n']);
$this->_sections['n']['name'] = 'n';
$this->_sections['n']['start'] = (int)2;
$this->_sections['n']['loop'] = is_array($_loop=$this->_tpl_vars['albumArray']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['n']['show'] = true;
$this->_sections['n']['max'] = $this->_sections['n']['loop'];
$this->_sections['n']['step'] = 1;
if ($this->_sections['n']['start'] < 0)
    $this->_sections['n']['start'] = max($this->_sections['n']['step'] > 0 ? 0 : -1, $this->_sections['n']['loop'] + $this->_sections['n']['start']);
else
    $this->_sections['n']['start'] = min($this->_sections['n']['start'], $this->_sections['n']['step'] > 0 ? $this->_sections['n']['loop'] : $this->_sections['n']['loop']-1);
if ($this->_sections['n']['show']) {
    $this->_sections['n']['total'] = min(ceil(($this->_sections['n']['step'] > 0 ? $this->_sections['n']['loop'] - $this->_sections['n']['start'] : $this->_sections['n']['start']+1)/abs($this->_sections['n']['step'])), $this->_sections['n']['max']);
    if ($this->_sections['n']['total'] == 0)
        $this->_sections['n']['show'] = false;
} else
    $this->_sections['n']['total'] = 0;
if ($this->_sections['n']['show']):

            for ($this->_sections['n']['index'] = $this->_sections['n']['start'], $this->_sections['n']['iteration'] = 1;
                 $this->_sections['n']['iteration'] <= $this->_sections['n']['total'];
                 $this->_sections['n']['index'] += $this->_sections['n']['step'], $this->_sections['n']['iteration']++):
$this->_sections['n']['rownum'] = $this->_sections['n']['iteration'];
$this->_sections['n']['index_prev'] = $this->_sections['n']['index'] - $this->_sections['n']['step'];
$this->_sections['n']['index_next'] = $this->_sections['n']['index'] + $this->_sections['n']['step'];
$this->_sections['n']['first']      = ($this->_sections['n']['iteration'] == 1);
$this->_sections['n']['last']       = ($this->_sections['n']['iteration'] == $this->_sections['n']['total']);
?>
            <a href="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['albumArray'][$this->_sections['n']['index']]->path_file; ?>
<?php echo $this->_tpl_vars['albumArray'][$this->_sections['n']['index']]->name; ?>
" class="lightwindow lightwindow_hidden" rel='xornal[album]' title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['album']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" caption="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['albumDescrip'][$this->_sections['n']['index']])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" author="<?php echo $this->_tpl_vars['album']->agency; ?>
">image #<?php echo $this->_sections['n']['index']; ?>
</a>
        <?php endfor; endif; ?>
        <!-- PESTANYA -->
        <div class="zonaPestanyasMedia3Cols">
            <div class="pestanyaSelecList">
                <div class="contInfoPestanyaGrande">
                    <div class="flechaPestanyaSelecList"></div>
                    <div class="textoPestanyaSelecList">MAS FOTOGALER&Iacute;AS</div>
                </div>
                <div class="cierrePestanyaSelecList"></div>
            </div>
        </div>
    </div>
    <div class="column123">
        <div class="agrupaColumnas fondoActualidadVideo">
            <div class="column12 separacionVertical15">
                <div class="zonaClasificacionVideos">
                    <div class="listadoMedia" id="div_albums">
                        <?php unset($this->_sections['n']);
$this->_sections['n']['name'] = 'n';
$this->_sections['n']['loop'] = is_array($_loop=$this->_tpl_vars['list_albums']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['n']['show'] = true;
$this->_sections['n']['max'] = $this->_sections['n']['loop'];
$this->_sections['n']['step'] = 1;
$this->_sections['n']['start'] = $this->_sections['n']['step'] > 0 ? 0 : $this->_sections['n']['loop']-1;
if ($this->_sections['n']['show']) {
    $this->_sections['n']['total'] = $this->_sections['n']['loop'];
    if ($this->_sections['n']['total'] == 0)
        $this->_sections['n']['show'] = false;
} else
    $this->_sections['n']['total'] = 0;
if ($this->_sections['n']['show']):

            for ($this->_sections['n']['index'] = $this->_sections['n']['start'], $this->_sections['n']['iteration'] = 1;
                 $this->_sections['n']['iteration'] <= $this->_sections['n']['total'];
                 $this->_sections['n']['index'] += $this->_sections['n']['step'], $this->_sections['n']['iteration']++):
$this->_sections['n']['rownum'] = $this->_sections['n']['iteration'];
$this->_sections['n']['index_prev'] = $this->_sections['n']['index'] - $this->_sections['n']['step'];
$this->_sections['n']['index_next'] = $this->_sections['n']['index'] + $this->_sections['n']['step'];
$this->_sections['n']['first']      = ($this->_sections['n']['iteration'] == 1);
$this->_sections['n']['last']       = ($this->_sections['n']['iteration'] == $this->_sections['n']['total']);
?>
                        <div class="elementoListadoMediaPag">
                            <div class="fotoElemMedia">
                               <img style="height:88px;" src="/media/images/album/crops/<?php echo $this->_tpl_vars['list_albums'][$this->_sections['n']['index']]->id; ?>
.jpg">
                            </div>
                            <div class="contSeccionFechaListado">
                                <div class="seccionMediaListado"><a href="<?php echo $this->_tpl_vars['list_albums'][$this->_sections['n']['index']]->permalink; ?>
" style="color:#004B8D;"><?php echo ((is_array($_tmp=$this->_tpl_vars['list_albums'][$this->_sections['n']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></div>
                                <div class="fechaMediaListado"><?php echo ((is_array($_tmp=$this->_tpl_vars['list_albums'][$this->_sections['n']['index']]->created)) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y") : smarty_modifier_date_format($_tmp, "%d/%m/%Y")); ?>
</div>
                            </div>
                            <div class="contTextoElemMediaListado">                               
                                <div class="textoElemMediaListado">
                                    <a href="<?php echo $this->_tpl_vars['list_albums'][$this->_sections['n']['index']]->permalink; ?>
"> <?php echo ((is_array($_tmp=$this->_tpl_vars['list_albums'][$this->_sections['n']['index']]->description)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
                                </div>
                            </div>
                            <div class="fileteIntraMedia"></div>
                        </div>
                        <?php endfor; endif; ?>
                       

                        <div class="posPaginadorGaliciaTitulares">
							<div class="CContenedorPaginado">
								<div class="link_paginador">+ Albums </div>
								<div class="CPaginas"> <?php echo $this->_tpl_vars['pages']->links; ?>
									
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
    </div>
</div>