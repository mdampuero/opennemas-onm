<?php /* Smarty version 2.6.18, created on 2010-04-22 13:14:54
         compiled from frontpage_article_big.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'upper', 'frontpage_article_big.tpl', 8, false),array('modifier', 'clearslash', 'frontpage_article_big.tpl', 8, false),array('function', 'renderTypeRelated', 'frontpage_article_big.tpl', 20, false),)), $this); ?>
<div class="nw-big">
    <div class="nw-category-name science"><span class="spacer">&nbsp;</span><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['item']->category_title)) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)))) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
 &nbsp;</div>
    <div class="content-new">
        <?php if (! empty ( $this->_tpl_vars['item']->img1_path )): ?>
            <img class="nw-image" src="<?php echo @MEDIA_IMG_PATH_WEB; ?>
/<?php echo $this->_tpl_vars['item']->img1_path; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->img_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->img_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
"/>
        <?php endif; ?>
        <h3 class="nw-title"><a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['item']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</a></h3>
        <p class="nw-subtitle"> <?php echo ((is_array($_tmp=$this->_tpl_vars['item']->summary)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
 </p>
        <?php if (! empty ( $this->_tpl_vars['item']->related_contents )): ?>
              <?php $this->assign('relacionadas', $this->_tpl_vars['item']->related_contents); ?>
              <div class="more-resources">
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
                        <?php if ($this->_tpl_vars['relacionadas'][$this->_sections['r']['index']]->pk_article != $this->_tpl_vars['item']->pk_article): ?>
                           <li><?php echo smarty_function_renderTypeRelated(array('content' => $this->_tpl_vars['relacionadas'][$this->_sections['r']['index']]), $this);?>
</li>
                        <?php endif; ?>
                    <?php endfor; endif; ?>
              </div>
        <?php endif; ?>
    </div>
</div>
 