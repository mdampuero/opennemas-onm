<?php /* Smarty version 2.6.18, created on 2010-04-21 23:27:41
         compiled from widget_more_news.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'widget_more_news.tpl', 8, false),array('modifier', 'escape', 'widget_more_news.tpl', 22, false),)), $this); ?>
    
<div class="more-news-section">
    <ul class="more-news-section-sectionlist clearfix">
        <li class="first"><a href="/seccion/<?php echo $this->_tpl_vars['category_hdata']['name']; ?>
/" title="Sección:<?php echo $this->_tpl_vars['category_hdata']['title']; ?>
"><strong><?php echo ((is_array($_tmp=$this->_tpl_vars['category_hdata']['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</strong>:</a></li>
         <?php if (! empty ( $this->_tpl_vars['category_hdata']['subcategories'] )): ?>
             <?php $_from = $this->_tpl_vars['category_hdata']['subcategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['s'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['s']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['c'] => $this->_tpl_vars['subcat']):
        $this->_foreach['s']['iteration']++;
?>
                <?php if (($this->_foreach['s']['iteration'] == $this->_foreach['s']['total'])): ?>
                    <li class="last"><a href="/seccion/<?php echo $this->_tpl_vars['category_hdata']['name']; ?>
/<?php echo $this->_tpl_vars['c']; ?>
/" title="Seccion:<?php echo $this->_tpl_vars['subcat']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['subcat'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</a></li>
                <?php else: ?>
                    <li ><a href="/seccion/<?php echo $this->_tpl_vars['category_hdata']['name']; ?>
/<?php echo $this->_tpl_vars['c']; ?>
/" title="Seccion:<?php echo $this->_tpl_vars['subcat']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['subcat'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</a></li>
                <?php endif; ?>
             <?php endforeach; endif; unset($_from); ?>
        <?php endif; ?>
    </ul>
    <ul class="more-news-section-links">
        <?php $_from = $this->_tpl_vars['titulares_cat'][$this->_tpl_vars['index']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['t'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['t']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['c'] => $this->_tpl_vars['sub']):
        $this->_foreach['t']['iteration']++;
?>
            <?php if (($this->_foreach['t']['iteration'] <= 1)): ?>
                <li class="first"><a href="<?php echo $this->_tpl_vars['sub']['permalink']; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['sub']['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['sub']['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</a></li>
            <?php elseif (($this->_foreach['t']['iteration'] == $this->_foreach['t']['total'])): ?>
                <li class="last"><a href="<?php echo $this->_tpl_vars['sub']['permalink']; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['sub']['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['sub']['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</a></li>
            <?php else: ?>
                <li><a href="/seccion/<?php echo $this->_tpl_vars['sub']['permalink']; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['sub']['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['sub']['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</a></li>
            <?php endif; ?>
        <?php endforeach; endif; unset($_from); ?>
    </ul>
</div>

 