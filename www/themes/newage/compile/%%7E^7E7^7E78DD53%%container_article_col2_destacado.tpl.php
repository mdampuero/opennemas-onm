<?php /* Smarty version 2.6.18, created on 2010-01-25 20:30:46
         compiled from container_article_col2_destacado.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'container_article_col2_destacado.tpl', 3, false),array('modifier', 'count_words', 'container_article_col2_destacado.tpl', 6, false),array('function', 'articledate', 'container_article_col2_destacado.tpl', 8, false),array('function', 'typecontent', 'container_article_col2_destacado.tpl', 28, false),)), $this); ?>
<div class="ColumnHome2Especial" style="background: #eaeaea">
    <div class="noticiaEspecial">
        <div class="ColumnHome2-preHeader"><?php echo ((is_array($_tmp=$this->_tpl_vars['item']->subtitle)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
        <h2 style="font-size:18px;"><a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" style="color: #004b8d"><?php echo ((is_array($_tmp=$this->_tpl_vars['item']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></h2>
        <div class="ColumnHome2-author">
            <div class="ColumnHome2-author_name"><?php if (((is_array($_tmp=$this->_tpl_vars['item']->agency)) ? $this->_run_mod_handler('count_words', true, $_tmp) : smarty_modifier_count_words($_tmp)) != '0'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']->agency)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
<?php else: ?>Xornal de Galicia<?php endif; ?></div>
            <div class="ColumnHome2-authorSeparator"></div>
            <?php echo smarty_function_articledate(array('article' => $this->_tpl_vars['item'],'created' => $this->_tpl_vars['item']->created,'updated' => $this->_tpl_vars['item']->changed), $this);?>

        </div>
        <?php $_from = $this->_tpl_vars['photos']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['myId'] => $this->_tpl_vars['i']):
?>
            <?php if ($this->_tpl_vars['myId'] == $this->_tpl_vars['item']->id && ! empty ( $this->_tpl_vars['i'] )): ?>
                <div class="ColumnHome2_photo">
                    <div class="ColumnHome2_photo2"><img height="106" src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['i']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->img1_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" /></div>
                    <?php if (! empty ( $this->_tpl_vars['item']->img1_footer )): ?><div class="creditos2"><?php echo ((is_array($_tmp=$this->_tpl_vars['item']->img1_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div><?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; endif; unset($_from); ?>
        <p><?php if (! empty ( $this->_tpl_vars['item']->summary )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['item']->summary)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
<span class="CSigue"> <a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
"></a></span><?php else: ?>&nbsp;<?php endif; ?></p>
        <div class="ColumnHome2_related_news">
            <?php $this->assign('art_id', $this->_tpl_vars['item']->id); ?>
            <?php if (isset ( $this->_tpl_vars['relationed_c1'][$this->_tpl_vars['art_id']] )): ?>
                <?php $this->assign('relacionadas', $this->_tpl_vars['relationed_c1'][$this->_tpl_vars['art_id']]); ?>
            <?php else: ?>
                <?php $this->assign('relacionadas', "array()"); ?>
            <?php endif; ?>

            <?php unset($this->_sections['r']);
$this->_sections['r']['name'] = 'r';
$this->_sections['r']['loop'] = is_array($_loop=$this->_tpl_vars['relacionadas']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                 <?php echo smarty_function_typecontent(array('content' => $this->_tpl_vars['relacionadas'][$this->_sections['r']['index']],'view_date' => '0'), $this);?>

            <?php endfor; endif; ?>
        </div>
        <div class="ColumnHome2_participation">
            <?php if ($this->_tpl_vars['item']->with_comment == '1'): ?>
            <div class="CComentarios">Comentarios: <?php if ($this->_tpl_vars['numcomment1'][$this->_tpl_vars['art_id']] >= 1): ?> (<?php echo $this->_tpl_vars['numcomment1'][$this->_tpl_vars['art_id']]; ?>
)<?php endif; ?> <a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
#COpina">Opinar</a></div>
            <?php endif; ?>
        </div>
     </div>
</div>
<div class="separadorHorizontal"></div>