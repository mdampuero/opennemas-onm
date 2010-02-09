<?php /* Smarty version 2.6.18, created on 2010-01-26 22:25:32
         compiled from conecta_Videos_listado.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'conecta_Videos_listado.tpl', 9, false),array('modifier', 'truncate', 'conecta_Videos_listado.tpl', 18, false),array('function', 'humandate', 'conecta_Videos_listado.tpl', 12, false),)), $this); ?>
 <?php if (! empty ( $this->_tpl_vars['arrayVideos'] )): ?>
    <?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['arrayVideos']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
        <?php if ($this->_tpl_vars['arrayVideos'][$this->_sections['c']['index']]->id != $this->_tpl_vars['videoID'][0]->id): ?>
            <div class="elementoListadoMediaPag">
                <div class="fotoElemMediaListado">
                    <img style="width:78px;height:94px;" src="http://i4.ytimg.com/vi/<?php echo $this->_tpl_vars['arrayVideos'][$this->_sections['c']['index']]->code; ?>
/default.jpg">
                </div>
                <div class="contSeccionFechaListado">
                    <div class="seccionMediaListado"><a href="<?php echo $this->_tpl_vars['arrayVideos'][$this->_sections['c']['index']]->permalink; ?>
" style="color:#004B8D;"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrayVideos'][$this->_sections['c']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></div>
                    <div class="fechaMediaListado">
                           <?php $this->assign('id_author', $this->_tpl_vars['arrayVideos'][$this->_sections['c']['index']]->fk_user); ?>
                            Enviada por <b><?php echo $this->_tpl_vars['conecta_users'][$this->_tpl_vars['id_author']]->nick; ?>
 </b> <?php echo smarty_function_humandate(array('article' => $this->_tpl_vars['arrayVideos'][$this->_sections['c']['index']],'created' => $this->_tpl_vars['arrayVideos'][$this->_sections['c']['index']]->created), $this);?>


                    </div>
                </div>
                <div class="contTextoElemMediaListado">
                    <div class="textoElemMediaListado">
                        <a href="<?php echo $this->_tpl_vars['arrayVideos'][$this->_sections['c']['index']]->permalink; ?>
"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrayVideos'][$this->_sections['c']['index']]->description)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('truncate', true, $_tmp, 250) : smarty_modifier_truncate($_tmp, 250)); ?>
</a>
                    </div>
                </div>
                <div class="fileteIntraMedia"></div>
            </div>
        <?php endif; ?>
    <?php endfor; endif; ?>
    <div class="posPaginadorGaliciaTitulares">
      <?php if ($this->_tpl_vars['pages']->links): ?>
            <div class="CContenedorPaginado">
                <div class="link_paginador">+ Videos</div>
                <div class="CPaginas">
                    <?php echo $this->_tpl_vars['pages']->links; ?>

                </div>
            </div>
      <?php endif; ?>
    </div>

<?php endif; ?>