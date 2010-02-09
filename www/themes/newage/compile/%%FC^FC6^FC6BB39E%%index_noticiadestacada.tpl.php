<?php /* Smarty version 2.6.18, created on 2010-02-03 17:10:46
         compiled from index_noticiadestacada.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'index_noticiadestacada.tpl', 15, false),array('modifier', 'count_words', 'index_noticiadestacada.tpl', 17, false),array('function', 'articledate', 'index_noticiadestacada.tpl', 19, false),array('function', 'typecontent', 'index_noticiadestacada.tpl', 26, false),)), $this); ?>
<div class="noticiaEspecialHome">
    <div class="CNoticiaDestacada">
        <?php if (empty ( $this->_tpl_vars['video_destacada'] )): ?>
            <?php if (! empty ( $this->_tpl_vars['photo_destacada'] )): ?>
            <div class="CContenedorFotoNoticiaDestacada">
                <img src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['photo_destacada']; ?>
" alt="imagen_destacada" />
                <p style="margin:0px;text-align:right">
                    <?php if (! empty ( $this->_tpl_vars['destaca'][0]->img1_footer )): ?><?php echo $this->_tpl_vars['destaca'][0]->img1_footer; ?>
<?php endif; ?>
                </p>
            </div>
            <?php endif; ?>
        <?php else: ?>
             <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "visor_video.tpl", 'smarty_include_vars' => array('video' => $this->_tpl_vars['video_destacada'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endif; ?>
        <h2><a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['destaca'][0]->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['destaca'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></h2>
        <div class="firma_destacado">
            <div class="firma_nombre"><?php if (((is_array($_tmp=$this->_tpl_vars['destaca'][0]->agency)) ? $this->_run_mod_handler('count_words', true, $_tmp) : smarty_modifier_count_words($_tmp)) != '0'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['destaca'][0]->agency)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
<?php else: ?>Xornal de Galicia<?php endif; ?></div>
            <div class="separadorFirma"></div>
               <?php echo smarty_function_articledate(array('article' => $this->_tpl_vars['destaca'][0],'created' => $this->_tpl_vars['destaca'][0]->created,'updated' => $this->_tpl_vars['destaca'][0]->changed), $this);?>

        </div>
        <p><?php echo ((is_array($_tmp=$this->_tpl_vars['destaca'][0]->summary)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
<span class="CSigue"> <a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['destaca'][0]->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
"></a></span></p>
        <?php if (isset ( $this->_tpl_vars['relationed'] ) && ! empty ( $this->_tpl_vars['relationed'] )): ?>
        <div class="CContenedorNoticiasRelacionadasDestacada">
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
                <?php if ($this->_tpl_vars['relationed'][$this->_sections['r']['index']]->pk_article != $this->_tpl_vars['destaca'][0]->pk_article): ?>
                    <?php echo smarty_function_typecontent(array('content' => $this->_tpl_vars['relationed'][$this->_sections['r']['index']],'view_date' => '0'), $this);?>

                <?php endif; ?>
            <?php endfor; endif; ?>           
        </div>
        <?php endif; ?>
        <?php if ($this->_tpl_vars['destaca'][0]->with_comment == '1'): ?>
            <div class="CContenedorTextoNoticiaDestacada">
                <div class="CContenedorParticipacion_destacado">
                    <div class="CComentarios CComentariosNotaDestacada">Comentarios: <?php if ($this->_tpl_vars['numcomment'] >= '1'): ?> (<?php echo $this->_tpl_vars['numcomment']; ?>
)<?php endif; ?> <a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['destaca'][0]->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
#COpina">Opinar</a></div>
                                    </div>
            </div>
         <?php endif; ?>
    </div>
</div> 