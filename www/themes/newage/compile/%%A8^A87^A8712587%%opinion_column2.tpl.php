<?php /* Smarty version 2.6.18, created on 2010-01-29 22:34:55
         compiled from opinion_column2.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'opinion_column2.tpl', 5, false),array('insert', 'renderbanner', 'opinion_column2.tpl', 29, false),)), $this); ?>
<div class="column2Noticia">
  <!-- **************** NOTICIAS RECOMENDADAS***************** -->
    <div class="CContainerRecomendaciones">
        <div class="CCabeceraRecomendaciones">
              <?php if ($this->_tpl_vars['other_opinions']): ?>Otros art&iacute;culos de <?php echo ((is_array($_tmp=$this->_tpl_vars['author_name'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
 <?php endif; ?>
        </div>
        <div class="CListaRecomendaciones">
            <!-- TITULAR RECOMENDACION-->
            <div class="CRecomendacion">
                <?php unset($this->_sections['a']);
$this->_sections['a']['name'] = 'a';
$this->_sections['a']['loop'] = is_array($_loop=$this->_tpl_vars['other_opinions']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['a']['show'] = true;
$this->_sections['a']['max'] = $this->_sections['a']['loop'];
$this->_sections['a']['step'] = 1;
$this->_sections['a']['start'] = $this->_sections['a']['step'] > 0 ? 0 : $this->_sections['a']['loop']-1;
if ($this->_sections['a']['show']) {
    $this->_sections['a']['total'] = $this->_sections['a']['loop'];
    if ($this->_sections['a']['total'] == 0)
        $this->_sections['a']['show'] = false;
} else
    $this->_sections['a']['total'] = 0;
if ($this->_sections['a']['show']):

            for ($this->_sections['a']['index'] = $this->_sections['a']['start'], $this->_sections['a']['iteration'] = 1;
                 $this->_sections['a']['iteration'] <= $this->_sections['a']['total'];
                 $this->_sections['a']['index'] += $this->_sections['a']['step'], $this->_sections['a']['iteration']++):
$this->_sections['a']['rownum'] = $this->_sections['a']['iteration'];
$this->_sections['a']['index_prev'] = $this->_sections['a']['index'] - $this->_sections['a']['step'];
$this->_sections['a']['index_next'] = $this->_sections['a']['index'] + $this->_sections['a']['step'];
$this->_sections['a']['first']      = ($this->_sections['a']['iteration'] == 1);
$this->_sections['a']['last']       = ($this->_sections['a']['iteration'] == $this->_sections['a']['total']);
?>
                    <div class="CContainerIconoTextoRecomendacion">
                    <div class="iconoRecomendacion"></div>
                    <div class="textoRecomendacion"><a href="<?php echo $this->_tpl_vars['other_opinions'][$this->_sections['a']['index']]->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['other_opinions'][$this->_sections['a']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></div>
                    </div>
                    <div class="fileteRecomendacion"></div>
                <?php endfor; endif; ?>
            </div>
        </div>
    </div>

    <div class="CContainerOtrasOpiniones" id="list_authors">
        <a href="/seccion/opinion/" id="cabeceraOpinion"></a>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_opinion_lista_xornalistas.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>

    <!-- ********************* PUBLICIDAD ********************** -->
    <div class="separadorHorizontal"></div>
    <div class="contBannerYTextoPublicidad">
    <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 5, 'cssclass' => 'contBannerPublicidad', 'width' => '295', 'height' => '295', 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>')), $this); ?>

    </div>
    <div class="separadorHorizontal"></div>
    <!-- ********************* PUBLICIDAD ********************** -->
    <!-- ********************* PESTAÑAS ************************* -->
    <?php if (isset ( $this->_tpl_vars['articles_viewed'] ) && ! empty ( $this->_tpl_vars['articles_viewed'] )): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_content_vistados_comentados.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endif; ?>
</div>