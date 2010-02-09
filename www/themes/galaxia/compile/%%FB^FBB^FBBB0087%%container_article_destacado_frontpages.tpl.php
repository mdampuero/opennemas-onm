<?php /* Smarty version 2.6.18, created on 2010-01-18 17:20:15
         compiled from container_article_destacado_frontpages.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'container_article_destacado_frontpages.tpl', 2, false),array('modifier', 'count_words', 'container_article_destacado_frontpages.tpl', 5, false),array('function', 'articledate', 'container_article_destacado_frontpages.tpl', 7, false),array('function', 'typecontent', 'container_article_destacado_frontpages.tpl', 26, false),)), $this); ?>
<div class="CNoticiaHome1"  style="width:620px;">
    <div class="antetitulo"><?php echo ((is_array($_tmp=$this->_tpl_vars['item']->subtitle)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
     <h2 style="font-size:30px;"><a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['item']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></h2>
    <div class="firma">
        <div class="firma_nombre"><?php if (((is_array($_tmp=$this->_tpl_vars['item']->agency)) ? $this->_run_mod_handler('count_words', true, $_tmp) : smarty_modifier_count_words($_tmp)) != '0'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']->agency)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
<?php else: ?>Xornal de Galicia<?php endif; ?></div>
        <div class="separadorFirma"></div>
          <?php echo smarty_function_articledate(array('article' => $this->_tpl_vars['item'],'created' => $this->_tpl_vars['item']->created,'updated' => $this->_tpl_vars['item']->changed), $this);?>

    </div>
      <?php if (! empty ( $this->_tpl_vars['video_destacada'] )): ?>
         <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "visor_video.tpl", 'smarty_include_vars' => array('video' => $this->_tpl_vars['video_destacada'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      <?php else: ?>
             <?php if (! empty ( $this->_tpl_vars['photo_destacada'] )): ?>
                  <div class="contenedorFoto">
                    <div class="CNoticiaHome1_foto2"><img height="150" src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['photo_destacada']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->img1_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" /></div>
                    <?php if (! empty ( $this->_tpl_vars['item']->img1_footer )): ?><div class="creditos2"><?php echo ((is_array($_tmp=$this->_tpl_vars['item']->img1_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div><?php endif; ?>
                </div>
             <?php endif; ?>
      <?php endif; ?>

    <p><?php if (! empty ( $this->_tpl_vars['item']->summary )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']->summary)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
<span class="CSigue"> <a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
"></a></span><?php else: ?>&nbsp;<?php endif; ?></p>
    <div class="CNoticiaHome1-contenedorTexto">
        <div class="CContenedorNoticiasRelacionadas">
            <?php unset($this->_sections['r']);
$this->_sections['r']['name'] = 'r';
$this->_sections['r']['loop'] = is_array($_loop=$this->_tpl_vars['relationed']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                <?php if ($this->_tpl_vars['relationed'][$this->_sections['r']['index']]->pk_article != $this->_tpl_vars['item']->pk_article): ?>
               <div class="CNoticiaRelacionada">
             <?php echo smarty_function_typecontent(array('content' => $this->_tpl_vars['relationed'][$this->_sections['r']['index']],'view_date' => '0'), $this);?>

               </div>
              <?php endif; ?>
            <?php endfor; endif; ?>
        </div>
    </div>
    <div class="CContenedorParticipacion">
  <?php if ($this->_tpl_vars['item']->with_comment == '1'): ?>
    <div class="CComentarios">Comentarios: <?php if ($this->_tpl_vars['numcomment'] >= '1'): ?> (<?php echo $this->_tpl_vars['numcomment']; ?>
)<?php endif; ?> <a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
#COpina">Opinar</a></div>
  <?php endif; ?>
    </div>
</div>
<div class="separadorHorizontal"></div>