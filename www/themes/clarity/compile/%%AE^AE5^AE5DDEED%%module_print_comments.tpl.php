<?php /* Smarty version 2.6.18, created on 2010-04-22 12:53:59
         compiled from module_print_comments.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'math', 'module_print_comments.tpl', 10, false),array('function', 'humandate', 'module_print_comments.tpl', 19, false),array('modifier', 'clearslash', 'module_print_comments.tpl', 12, false),array('insert', 'voteComment', 'module_print_comments.tpl', 16, false),)), $this); ?>
 
<?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['comments']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
<a name="<?php echo $this->_tpl_vars['comments'][$this->_sections['c']['index']]->id; ?>
"></a>
<div class="list-comments span-16">
    <div class="comment-wrapper">
        <div class="comment-number"><?php echo smarty_function_math(array('x' => $this->_sections['c']['iteration'],'y' => $this->_tpl_vars['paginacion']->_currentPage,'equation' => 'x+(y-1)*9'), $this);?>
</div>
        <div class="comment-content span-14 prepend-2">
            <strong><?php echo ((is_array($_tmp=$this->_tpl_vars['comments'][$this->_sections['c']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</strong>
            <?php echo ((is_array($_tmp=$this->_tpl_vars['comments'][$this->_sections['c']['index']]->body)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>

        </div>
        <div class="">
            <div class="span-5"><?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'voteComment', 'id' => $this->_tpl_vars['comments'][$this->_sections['c']['index']]->id, 'page' => 'article', 'type' => 'vote')), $this); ?>
</div>
            <div class="span-10">  escrito por
                <span class="comment-author"><?php echo ((is_array($_tmp=$this->_tpl_vars['comments'][$this->_sections['c']['index']]->author)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</span>                                    
                <span class="comment-time"><?php echo smarty_function_humandate(array('created' => $this->_tpl_vars['comments'][$this->_sections['c']['index']]->created), $this);?>
 </span>
            </div>
        </div>
    </div>
</div>
<?php endfor; endif; ?>