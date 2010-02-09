<?php /* Smarty version 2.6.18, created on 2010-01-30 05:51:22
         compiled from mobile.index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'mobile.index.tpl', 20, false),array('modifier', 'strip_tags', 'mobile.index.tpl', 32, false),array('function', 'imageattrs', 'mobile.index.tpl', 21, false),array('function', 'math', 'mobile.index.tpl', 22, false),array('function', 'humandate', 'mobile.index.tpl', 26, false),)), $this); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mobile/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    
        <?php $this->assign('total_photos', 0); ?>

		<?php unset($this->_sections['dest']);
$this->_sections['dest']['name'] = 'dest';
$this->_sections['dest']['loop'] = is_array($_loop=$this->_tpl_vars['destaca']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['dest']['show'] = true;
$this->_sections['dest']['max'] = $this->_sections['dest']['loop'];
$this->_sections['dest']['step'] = 1;
$this->_sections['dest']['start'] = $this->_sections['dest']['step'] > 0 ? 0 : $this->_sections['dest']['loop']-1;
if ($this->_sections['dest']['show']) {
    $this->_sections['dest']['total'] = $this->_sections['dest']['loop'];
    if ($this->_sections['dest']['total'] == 0)
        $this->_sections['dest']['show'] = false;
} else
    $this->_sections['dest']['total'] = 0;
if ($this->_sections['dest']['show']):

            for ($this->_sections['dest']['index'] = $this->_sections['dest']['start'], $this->_sections['dest']['iteration'] = 1;
                 $this->_sections['dest']['iteration'] <= $this->_sections['dest']['total'];
                 $this->_sections['dest']['index'] += $this->_sections['dest']['step'], $this->_sections['dest']['iteration']++):
$this->_sections['dest']['rownum'] = $this->_sections['dest']['iteration'];
$this->_sections['dest']['index_prev'] = $this->_sections['dest']['index'] - $this->_sections['dest']['step'];
$this->_sections['dest']['index_next'] = $this->_sections['dest']['index'] + $this->_sections['dest']['step'];
$this->_sections['dest']['first']      = ($this->_sections['dest']['iteration'] == 1);
$this->_sections['dest']['last']       = ($this->_sections['dest']['iteration'] == $this->_sections['dest']['total']);
?>
		
				<?php $this->assign('article', $this->_tpl_vars['destaca'][$this->_sections['dest']['index']]); ?>
		<?php $this->assign('id', $this->_tpl_vars['article']->id); ?>
		
		<div class="noticia principal">
            <?php if (isset ( $this->_tpl_vars['photos'][$this->_tpl_vars['id']] ) && ( $this->_tpl_vars['total_photos'] < 3 )): ?>
                <a href="<?php echo @BASE_URL; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
">
                    <img src="/media/images/<?php echo $this->_tpl_vars['photos'][$this->_tpl_vars['id']]; ?>
" alt="" <?php echo smarty_function_imageattrs(array('image' => $this->_tpl_vars['photos'][$this->_tpl_vars['id']]), $this);?>
/></a>
                <?php echo smarty_function_math(array('equation' => "x + 1",'x' => $this->_tpl_vars['total_photos'],'assign' => 'total_photos'), $this);?>
 
            <?php endif; ?>
			
			<div class="titular">
				<div class="fecha"><?php echo smarty_function_humandate(array('article' => $this->_tpl_vars['article'],'created' => $this->_tpl_vars['article']->created,'updated' => $this->_tpl_vars['article']->changed), $this);?>
</div>
				<?php if ($this->_tpl_vars['section'] == 'home'): ?><span class="seccion">[<?php echo $this->_tpl_vars['ccm']->get_title($this->_tpl_vars['article']->category_name); ?>
]</span><?php endif; ?>
				<a href="<?php echo @BASE_URL; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title=""><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
			</div>
			
			<div class="entradilla">
				<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->summary)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)); ?>

			</div>
		</div>
                
	<?php endfor; endif; ?>					
	
    <?php $this->assign('placeholder', 'placeholder'); ?>
    <?php if ($this->_tpl_vars['section'] == 'home'): ?>
        <?php $this->assign('placeholder', 'home_placeholder'); ?>
    <?php endif; ?>
    
		<?php unset($this->_sections['art']);
$this->_sections['art']['name'] = 'art';
$this->_sections['art']['loop'] = is_array($_loop=$this->_tpl_vars['articles_home']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['art']['show'] = true;
$this->_sections['art']['max'] = $this->_sections['art']['loop'];
$this->_sections['art']['step'] = 1;
$this->_sections['art']['start'] = $this->_sections['art']['step'] > 0 ? 0 : $this->_sections['art']['loop']-1;
if ($this->_sections['art']['show']) {
    $this->_sections['art']['total'] = $this->_sections['art']['loop'];
    if ($this->_sections['art']['total'] == 0)
        $this->_sections['art']['show'] = false;
} else
    $this->_sections['art']['total'] = 0;
if ($this->_sections['art']['show']):

            for ($this->_sections['art']['index'] = $this->_sections['art']['start'], $this->_sections['art']['iteration'] = 1;
                 $this->_sections['art']['iteration'] <= $this->_sections['art']['total'];
                 $this->_sections['art']['index'] += $this->_sections['art']['step'], $this->_sections['art']['iteration']++):
$this->_sections['art']['rownum'] = $this->_sections['art']['iteration'];
$this->_sections['art']['index_prev'] = $this->_sections['art']['index'] - $this->_sections['art']['step'];
$this->_sections['art']['index_next'] = $this->_sections['art']['index'] + $this->_sections['art']['step'];
$this->_sections['art']['first']      = ($this->_sections['art']['iteration'] == 1);
$this->_sections['art']['last']       = ($this->_sections['art']['iteration'] == $this->_sections['art']['total']);
?>
		<?php if ($this->_tpl_vars['articles_home'][$this->_sections['art']['index']]->{(($_var=$this->_tpl_vars['placeholder']) && substr($_var,0,2)!='__') ? $_var : $this->trigger_error("cannot access property \"$_var\"")} != 'placeholder_0_0'): ?>
                        <?php $this->assign('article', $this->_tpl_vars['articles_home'][$this->_sections['art']['index']]); ?>
            <?php $this->assign('id', $this->_tpl_vars['article']->id); ?>        
                    
            <div class="noticia con_entradilla">
                <div class="titular">
                    <div class="fecha"><?php echo smarty_function_humandate(array('article' => $this->_tpl_vars['article'],'created' => $this->_tpl_vars['article']->created,'updated' => $this->_tpl_vars['article']->changed), $this);?>
</div>
                    <?php if ($this->_tpl_vars['section'] == 'home'): ?><span class="seccion">[<?php echo $this->_tpl_vars['ccm']->get_title($this->_tpl_vars['article']->category_name); ?>
]</span><?php endif; ?>
                    <a href="<?php echo @BASE_URL; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title=""><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
                </div>
                
                <div class="entradilla">
                    <?php if (isset ( $this->_tpl_vars['photos'][$this->_tpl_vars['id']] ) && ( $this->_tpl_vars['total_photos'] < 3 )): ?>
                        <a href="<?php echo @BASE_URL; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
">
                            <img src="/media/images/<?php echo $this->_tpl_vars['photos'][$this->_tpl_vars['id']]; ?>
" alt="" <?php echo smarty_function_imageattrs(array('image' => $this->_tpl_vars['photos'][$this->_tpl_vars['id']]), $this);?>
/></a>
                        <?php echo smarty_function_math(array('equation' => "x + 1",'x' => $this->_tpl_vars['total_photos'],'assign' => 'total_photos'), $this);?>
 
                    <?php endif; ?>
                    <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->summary)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)); ?>

                </div>
                
                <br class="clearer" />
            </div>
        <?php endif; ?>
	<?php endfor; endif; ?>			

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "mobile/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>