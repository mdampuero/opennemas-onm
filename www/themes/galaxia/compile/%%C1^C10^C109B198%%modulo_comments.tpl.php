<?php /* Smarty version 2.6.18, created on 2010-01-14 02:29:11
         compiled from modulo_comments.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'math', 'modulo_comments.tpl', 6, false),array('modifier', 'clearslash', 'modulo_comments.tpl', 9, false),array('insert', 'voteComment', 'modulo_comments.tpl', 16, false),)), $this); ?>
            <div class="CComentario" id="div_comments">                          
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
                    <div class="CComentario">
                        <a name="<?php echo $this->_tpl_vars['comments'][$this->_sections['c']['index']]->id; ?>
"></a>
                       <?php if ($this->_tpl_vars['paginacion']): ?>
                           <div class="CNumeroComentario"><?php echo smarty_function_math(array('x' => $this->_sections['c']['iteration'],'y' => $this->_tpl_vars['paginacion']->_currentPage,'equation' => 'x+(y-1)*9'), $this);?>
</div>
                       <?php endif; ?>
                        <div class="CInfoComentario">
                            <div class="CTitularComentario"><?php echo ((is_array($_tmp=$this->_tpl_vars['comments'][$this->_sections['c']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
                            <div class="CDatosComentario">
                            <div class="CNombreComentarista"><?php echo ((is_array($_tmp=$this->_tpl_vars['comments'][$this->_sections['c']['index']]->author)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
                            <div class="CFechaComentario"><?php echo $this->_tpl_vars['comments'][$this->_sections['c']['index']]->created; ?>
</div>
                            </div>
                            <div class="CTextoComentario"><?php echo ((is_array($_tmp=$this->_tpl_vars['comments'][$this->_sections['c']['index']]->body)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>
                        </div>
                         <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'voteComment', 'id' => $this->_tpl_vars['comments'][$this->_sections['c']['index']]->id, 'page' => 'article', 'type' => 'vote')), $this); ?>

                    </div>
                <?php endfor; endif; ?>
                 <p align="center"> <?php echo $this->_tpl_vars['paginacion']->links; ?>
</p>
           </div>