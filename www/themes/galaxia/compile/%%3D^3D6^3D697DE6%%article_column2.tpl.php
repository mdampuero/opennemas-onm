<?php /* Smarty version 2.6.18, created on 2010-01-20 17:39:27
         compiled from article_column2.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'article_column2.tpl', 14, false),array('insert', 'renderbanner', 'article_column2.tpl', 80, false),)), $this); ?>
<div class="column2Noticia">
<!-- **************** NOTICIAS SUGERIDAS ***************** -->
    <?php if (count ( $this->_tpl_vars['suggested'] ) > 0): ?>
        <div class="textoGaliciaTitulares"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
noticia/tambientepuedeinteresar.gif" alt="Tambien te puede interesar"/></div>
        <div class="CContainerRecomendaciones">
            <div class="CListaRecomendaciones">
                <?php unset($this->_sections['r']);
$this->_sections['r']['name'] = 'r';
$this->_sections['r']['loop'] = is_array($_loop=$this->_tpl_vars['suggested']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['r']['show'] = true;
$this->_sections['r']['max'] = $this->_sections['r']['loop'];
$this->_sections['r']['step'] = 1;
$this->_sections['r']['start'] = $this->_sections['r']['step'] > 0 ? 0 : $this->_sections['r']['loop']-1;
if ($this->_sections['r']['show']) {
    $this->_sections['r']['total'] = $this->_sections['r']['loop'];
    if ($this->_sections['r']['total'] == 0)
        $this->_sections['r']['show'] = false;
} else
    $this->_sections['r']['total'] = 0;
if ($this->_sections['r']['show']):

            for ($this->_sections['r']['index'] = $this->_sections['r']['start'], $this->_sections['r']['iteration'] = 1;
                 $this->_sections['r']['iteration'] <= $this->_sections['r']['total'];
                 $this->_sections['r']['index'] += $this->_sections['r']['step'], $this->_sections['r']['iteration']++):
$this->_sections['r']['rownum'] = $this->_sections['r']['iteration'];
$this->_sections['r']['index_prev'] = $this->_sections['r']['index'] - $this->_sections['r']['step'];
$this->_sections['r']['index_next'] = $this->_sections['r']['index'] + $this->_sections['r']['step'];
$this->_sections['r']['first']      = ($this->_sections['r']['iteration'] == 1);
$this->_sections['r']['last']       = ($this->_sections['r']['iteration'] == $this->_sections['r']['total']);
?>
                    <?php if ($this->_tpl_vars['suggested'][$this->_sections['r']['index']]['pk_content'] != $this->_tpl_vars['article']->pk_article && $this->_tpl_vars['suggested'][$this->_sections['r']['index']]['title']): ?>
                        <!-- TITULAR RECOMENDACION-->
                        <div class="CRecomendacion">
                            <div class="CContainerIconoTextoRecomendacion">
                                <div class="iconoRecomendacion"></div>
                                <div class="textoRecomendacion">
                                     <a href="<?php echo $this->_tpl_vars['suggested'][$this->_sections['r']['index']]['permalink']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['suggested'][$this->_sections['r']['index']]['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
                                </div>
                            </div>
                            <div class="fileteRecomendacion"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>
                        </div>
                    <?php endif; ?>
                <?php endfor; endif; ?>
            </div>
        </div>
        <div class="separadorHorizontal"></div>
    <?php endif; ?>

    <?php if ($this->_tpl_vars['photoExt']->name || $this->_tpl_vars['photoInt']->name): ?>
    
    <div class="textoGaliciaTitulares"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
noticia/fotosdelanoticia.gif" alt="Fotos de la Noticia"/></div>

        <?php if ($this->_tpl_vars['photoExt']->name): ?>
        <div class="CNoticiaContenedorFoto">
            <!--div class="CCabeceraVideo"></div-->
            <a href="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['photoExt']->path_file; ?>
<?php echo $this->_tpl_vars['photoExt']->name; ?>
" class="lightwindow" rel=""  caption='<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->img1_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
' title='<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
'>
                <div class="CNoticia_foto">
                   <img src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['photoExt']->path_file; ?>
<?php echo $this->_tpl_vars['photoExt']->name; ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->img1_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" width="295"/>
                </div>
            </a>
            <div class="clear"></div>
            <div class="creditos_nota"><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->img1_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
        </div>
        <?php endif; ?>
        <?php if ($this->_tpl_vars['photoInt']->name): ?>
        <div class="CNoticiaContenedorFoto">
            <!--div class="CCabeceraVideo"></div-->
            <a href="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['photoInt']->path_file; ?>
<?php echo $this->_tpl_vars['photoInt']->name; ?>
" class="lightwindow" rel=""  caption='<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->img2_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
' title='<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
'>
                <div class="CNoticia_foto">
                   <img src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['photoInt']->path_file; ?>
<?php echo $this->_tpl_vars['photoInt']->name; ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->img2_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" width="295"/>
                </div>
            </a>
            <div class="clear"></div>
            <div class="creditos_nota"><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->img2_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
        </div>
        <?php endif; ?>
        <div class="separadorHorizontal"></div>
    <?php endif; ?>

    <!-- **************** NOTICIAS RECOMENDADAS***************** -->    
    <?php if (isset ( $this->_tpl_vars['articles_express'] ) && ! empty ( $this->_tpl_vars['articles_express'] )): ?>
    <div class="containerGaliciaTitulares">
        <div class="cabeceraGaliciaTitulares"></div>
        <div id="div_articles_express"  class="listaGaliciaTitulares">
                        <?php unset($this->_sections['exp']);
$this->_sections['exp']['name'] = 'exp';
$this->_sections['exp']['loop'] = is_array($_loop=$this->_tpl_vars['articles_express']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['exp']['show'] = true;
$this->_sections['exp']['max'] = $this->_sections['exp']['loop'];
$this->_sections['exp']['step'] = 1;
$this->_sections['exp']['start'] = $this->_sections['exp']['step'] > 0 ? 0 : $this->_sections['exp']['loop']-1;
if ($this->_sections['exp']['show']) {
    $this->_sections['exp']['total'] = $this->_sections['exp']['loop'];
    if ($this->_sections['exp']['total'] == 0)
        $this->_sections['exp']['show'] = false;
} else
    $this->_sections['exp']['total'] = 0;
if ($this->_sections['exp']['show']):

            for ($this->_sections['exp']['index'] = $this->_sections['exp']['start'], $this->_sections['exp']['iteration'] = 1;
                 $this->_sections['exp']['iteration'] <= $this->_sections['exp']['total'];
                 $this->_sections['exp']['index'] += $this->_sections['exp']['step'], $this->_sections['exp']['iteration']++):
$this->_sections['exp']['rownum'] = $this->_sections['exp']['iteration'];
$this->_sections['exp']['index_prev'] = $this->_sections['exp']['index'] - $this->_sections['exp']['step'];
$this->_sections['exp']['index_next'] = $this->_sections['exp']['index'] + $this->_sections['exp']['step'];
$this->_sections['exp']['first']      = ($this->_sections['exp']['iteration'] == 1);
$this->_sections['exp']['last']       = ($this->_sections['exp']['iteration'] == $this->_sections['exp']['total']);
?>
                <div class="noticiaGaliciaTitulares">
                <div class="iconoGaliciaTitulares"></div>
                <div class="contTextoFilete">
                    <div class="textoGaliciaTitulares"><a href="<?php echo $this->_tpl_vars['articles_express'][$this->_sections['exp']['index']]->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['articles_express'][$this->_sections['exp']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></div>
                    <div class="fileteGaliciaTitulares"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
galiciaTitulares/fileteDashedGaliciaTitulares.gif" alt=""/></div>
                </div>
                </div>
            <?php endfor; endif; ?>
                    </div>
    </div>
    <?php endif; ?>
    
    <!-- ********************* PUBLICIDAD ********************** -->
        
        <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 102, 'cssclass' => 'banner295x295', 'beforeHTML' => $this->_tpl_vars['beforeAdv'])), $this); ?>

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
    
            <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 103, 'cssclass' => 'banner295x295', 'beforeHTML' => $this->_tpl_vars['beforeAdv'])), $this); ?>
    
</div>