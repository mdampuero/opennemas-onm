<?php /* Smarty version 2.6.18, created on 2010-01-14 01:41:18
         compiled from modulo_column3_containerFotoVideoDiaMasListado.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'modulo_column3_containerFotoVideoDiaMasListado.tpl', 6, false),array('function', 'typepccontent', 'modulo_column3_containerFotoVideoDiaMasListado.tpl', 52, false),)), $this); ?>
<?php if (isset ( $this->_tpl_vars['graficasconecta'] ) && count ( $this->_tpl_vars['graficasconecta'] )): ?>
<div class="enlaceFotoVideoDia">
    <div class="enlaceFotoVideoDia">
        <a href="/conecta/enquisa/"><img alt="Encuestas Conecta" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
fotoVideoDia/encuestasXornal.gif" style="margin-bottom: 5px;"/></a>
        <?php unset($this->_sections['enqs']);
$this->_sections['enqs']['name'] = 'enqs';
$this->_sections['enqs']['loop'] = is_array($_loop=$this->_tpl_vars['graficasconecta']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['enqs']['show'] = true;
$this->_sections['enqs']['max'] = $this->_sections['enqs']['loop'];
$this->_sections['enqs']['step'] = 1;
$this->_sections['enqs']['start'] = $this->_sections['enqs']['step'] > 0 ? 0 : $this->_sections['enqs']['loop']-1;
if ($this->_sections['enqs']['show']) {
    $this->_sections['enqs']['total'] = $this->_sections['enqs']['loop'];
    if ($this->_sections['enqs']['total'] == 0)
        $this->_sections['enqs']['show'] = false;
} else
    $this->_sections['enqs']['total'] = 0;
if ($this->_sections['enqs']['show']):

            for ($this->_sections['enqs']['index'] = $this->_sections['enqs']['start'], $this->_sections['enqs']['iteration'] = 1;
                 $this->_sections['enqs']['iteration'] <= $this->_sections['enqs']['total'];
                 $this->_sections['enqs']['index'] += $this->_sections['enqs']['step'], $this->_sections['enqs']['iteration']++):
$this->_sections['enqs']['rownum'] = $this->_sections['enqs']['iteration'];
$this->_sections['enqs']['index_prev'] = $this->_sections['enqs']['index'] - $this->_sections['enqs']['step'];
$this->_sections['enqs']['index_next'] = $this->_sections['enqs']['index'] + $this->_sections['enqs']['step'];
$this->_sections['enqs']['first']      = ($this->_sections['enqs']['iteration'] == 1);
$this->_sections['enqs']['last']       = ($this->_sections['enqs']['iteration'] == $this->_sections['enqs']['total']);
?>
        <p> <a class="no_underline" href="/conecta/enquisa/<?php echo $this->_tpl_vars['graficasconecta'][$this->_sections['enqs']['index']]->id; ?>
.html" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['graficasconecta'][$this->_sections['enqs']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
"><b><?php echo ((is_array($_tmp=$this->_tpl_vars['graficasconecta'][$this->_sections['enqs']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</b></a></p>
                <a class="no_underline" href="/conecta/enquisa/<?php echo $this->_tpl_vars['graficasconecta'][$this->_sections['enqs']['index']]->id; ?>
.html" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['graficasconecta'][$this->_sections['enqs']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
">
            <img src="/conecta/enquisa/h<?php echo $this->_tpl_vars['graficasconecta'][$this->_sections['enqs']['index']]->id; ?>
.png" border="0" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['graficasconecta'][$this->_sections['enqs']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" /></a>
        <br />
        <?php endfor; endif; ?>
    </div>
    <div class="separadorHorizontal"></div>
</div>
<?php endif; ?>

<div class="containerFotoVideoDiaMasListado">
    <?php if (! ( preg_match ( '/planconecta\.php/' , $_SERVER['SCRIPT_NAME'] ) )): ?>
        <div class="containerFotoVideoDia">
            <div class="logoFotoVideoDia"><a href="/conecta/"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
fotoVideoDia/logoPlanConecta.gif" alt="Logo Conecta"/></a></div>
            <div class="zonaPestanyasFotoVideoDia">
                <div class="pestanyaFotoDia">
                    <div class="textoPestanyaMediaDia"><a style="cursor:pointer;" onclick="$('videodia').hide();$('photodia').show();$('textvideodia').hide();$('textphotodia').show();">Foto del d&iacute;a</a></div>
                </div>
                <div class="pestanyaVideoDia">
                    <div class="textoPestanyaMediaDia"><a style="cursor:pointer;" onclick="$('videodia').show();$('photodia').hide();$('textvideodia').show();$('textphotodia').hide();">Video del d&iacute;a</a></div>
                </div>
            </div>
            <div class="zonaVisualizacionFotoVideoDia" id="photodia"  style="display:inline;">
                <a style="cursor:pointer;" href="/conecta/foto-dia/"><img src="<?php echo $this->_tpl_vars['MEDIA_CONECTA_WEB']; ?>
<?php echo $this->_tpl_vars['photodia']->path_file; ?>
" width="180" height="115" alt="Foto del dia"/> </a>
            </div>
            <div class="zonaVisualizacionFotoVideoDia" id="videodia" style="display:none;">
                <a style="cursor:pointer;" href="/conecta/video-dia/"><img src="http://i4.ytimg.com/vi/<?php echo $this->_tpl_vars['videodia']->code; ?>
/default.jpg"  width="180" height="115" alt="Video del dia"/> </a>
            </div>
            <div class="franjaAzulFotoVideoDia"><div class="flechitaBlancaFotoVideoDia"></div>
                <div id="textphotodia" class="textoFranjaAzulFotoVideoDia" style="display:inline;"><?php echo ((is_array($_tmp=$this->_tpl_vars['photodia']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
                <div id="textvideodia" class="textoFranjaAzulFotoVideoDia" style="display:none;"><?php echo ((is_array($_tmp=$this->_tpl_vars['videodia']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
            </div>
        </div>
    <?php else: ?>
        <div class="containerFotoVideoDia">
            <div class="logoFotoVideoDia"><a href="/conecta/"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
fotoVideoDia/logoPlanConecta.gif" alt="Logo Conecta"/></a></div>
        </div>
    <?php endif; ?>
    <?php unset($this->_sections['t']);
$this->_sections['t']['loop'] = is_array($_loop=$this->_tpl_vars['allcategorys']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['t']['name'] = 't';
$this->_sections['t']['show'] = true;
$this->_sections['t']['max'] = $this->_sections['t']['loop'];
$this->_sections['t']['step'] = 1;
$this->_sections['t']['start'] = $this->_sections['t']['step'] > 0 ? 0 : $this->_sections['t']['loop']-1;
if ($this->_sections['t']['show']) {
    $this->_sections['t']['total'] = $this->_sections['t']['loop'];
    if ($this->_sections['t']['total'] == 0)
        $this->_sections['t']['show'] = false;
} else
    $this->_sections['t']['total'] = 0;
if ($this->_sections['t']['show']):

            for ($this->_sections['t']['index'] = $this->_sections['t']['start'], $this->_sections['t']['iteration'] = 1;
                 $this->_sections['t']['iteration'] <= $this->_sections['t']['total'];
                 $this->_sections['t']['index'] += $this->_sections['t']['step'], $this->_sections['t']['iteration']++):
$this->_sections['t']['rownum'] = $this->_sections['t']['iteration'];
$this->_sections['t']['index_prev'] = $this->_sections['t']['index'] - $this->_sections['t']['step'];
$this->_sections['t']['index_next'] = $this->_sections['t']['index'] + $this->_sections['t']['step'];
$this->_sections['t']['first']      = ($this->_sections['t']['iteration'] == 1);
$this->_sections['t']['last']       = ($this->_sections['t']['iteration'] == $this->_sections['t']['total']);
?>
        <?php $this->assign('categorys', $this->_tpl_vars['allcategorys'][$this->_sections['t']['index']]); ?>
        <?php unset($this->_sections['c']);
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['categorys']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['show'] = true;
$this->_sections['c']['max'] = $this->_sections['c']['loop'];
$this->_sections['c']['step'] = 1;
$this->_sections['c']['start'] = $this->_sections['c']['step'] > 0 ? 0 : $this->_sections['c']['loop']-1;
if ($this->_sections['c']['show']) {
    $this->_sections['c']['total'] = $this->_sections['c']['loop'];
    if ($this->_sections['c']['total'] == 0)
        $this->_sections['c']['show'] = false;
} else
    $this->_sections['c']['total'] = 0;
if ($this->_sections['c']['show']):

            for ($this->_sections['c']['index'] = $this->_sections['c']['start'], $this->_sections['c']['iteration'] = 1;
                 $this->_sections['c']['iteration'] <= $this->_sections['c']['total'];
                 $this->_sections['c']['index'] += $this->_sections['c']['step'], $this->_sections['c']['iteration']++):
$this->_sections['c']['rownum'] = $this->_sections['c']['iteration'];
$this->_sections['c']['index_prev'] = $this->_sections['c']['index'] - $this->_sections['c']['step'];
$this->_sections['c']['index_next'] = $this->_sections['c']['index'] + $this->_sections['c']['step'];
$this->_sections['c']['first']      = ($this->_sections['c']['iteration'] == 1);
$this->_sections['c']['last']       = ($this->_sections['c']['iteration'] == $this->_sections['c']['total']);
?>
           <div class="enlaceFotoVideoDia">
               <div class="enlaceFotoVideoDia">
                   <img alt="Flechita azul" src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
fotoVideoDia/flechitaAzul.gif"/>
                   <a href="/conecta/<?php echo smarty_function_typepccontent(array('content_type' => $this->_tpl_vars['categorys'][$this->_sections['c']['index']]->fk_content_type), $this);?>
/<?php echo $this->_tpl_vars['categorys'][$this->_sections['c']['index']]->name; ?>
/"><?php echo $this->_tpl_vars['categorys'][$this->_sections['c']['index']]->title; ?>
</a>
               </div>
               <div class="fileteFotoVideoDia"></div>
            </div>
        <?php endfor; endif; ?>
    <?php endfor; endif; ?>
    <div class="menuInferiorFotoVideoDia">
        <a href="/conecta/login/">Login</a> | <a href="/conecta/rexistro/">Registrarse</a>
    </div>
</div>