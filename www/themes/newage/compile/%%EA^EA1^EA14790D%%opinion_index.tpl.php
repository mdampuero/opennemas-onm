<?php /* Smarty version 2.6.18, created on 2010-01-25 21:38:24
         compiled from opinion_index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'intersticial', 'opinion_index.tpl', 4, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_head.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<body>

<?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'intersticial', 'type' => '50')), $this); ?>
    
    
<div class="global_metacontainer">
    <div class="marco_metacontainer">
        <div class="metacontainer">
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_separadorbanners1.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <div class="container">
                <div class="containerNoticias">
                    <div class="column12">
                        <div class="containerCol12 fondoContainerActualidad">
                             <form method="post" action="/opinions/listar_autores/'.$('autores').options[this.selectedIndex].value/<?php echo time(); ?>
.html">
                                 Seleccione autor:
                                 <select name="autores" id="autores" class="" onChange=" <?php echo ' if(this.options[this.selectedIndex].value){ window.location=\'/opinions/opinions_do_autor/\'+this.options[this.selectedIndex].value+\'/\'+this.options[this.selectedIndex].getAttribute(\'name\')+\'.html\';} '; ?>
">
                                    <option name=" " value="0" selected="selected">--Autor--</option>
                                    <option name="Editorial" value="1" <?php if ($this->_tpl_vars['author_id'] == 1): ?> selected="selected" <?php endif; ?> >Editorial</option>
                                    <option name="Director" value="2" <?php if ($this->_tpl_vars['author_id'] == 2): ?> selected="selected" <?php endif; ?> >Director</option>
                                    <?php unset($this->_sections['as']);
$this->_sections['as']['name'] = 'as';
$this->_sections['as']['loop'] = is_array($_loop=$this->_tpl_vars['autores']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['as']['show'] = true;
$this->_sections['as']['max'] = $this->_sections['as']['loop'];
$this->_sections['as']['step'] = 1;
$this->_sections['as']['start'] = $this->_sections['as']['step'] > 0 ? 0 : $this->_sections['as']['loop']-1;
if ($this->_sections['as']['show']) {
    $this->_sections['as']['total'] = $this->_sections['as']['loop'];
    if ($this->_sections['as']['total'] == 0)
        $this->_sections['as']['show'] = false;
} else
    $this->_sections['as']['total'] = 0;
if ($this->_sections['as']['show']):

            for ($this->_sections['as']['index'] = $this->_sections['as']['start'], $this->_sections['as']['iteration'] = 1;
                 $this->_sections['as']['iteration'] <= $this->_sections['as']['total'];
                 $this->_sections['as']['index'] += $this->_sections['as']['step'], $this->_sections['as']['iteration']++):
$this->_sections['as']['rownum'] = $this->_sections['as']['iteration'];
$this->_sections['as']['index_prev'] = $this->_sections['as']['index'] - $this->_sections['as']['step'];
$this->_sections['as']['index_next'] = $this->_sections['as']['index'] + $this->_sections['as']['step'];
$this->_sections['as']['first']      = ($this->_sections['as']['iteration'] == 1);
$this->_sections['as']['last']       = ($this->_sections['as']['iteration'] == $this->_sections['as']['total']);
?>
                                         <option name="<?php echo $this->_tpl_vars['autores'][$this->_sections['as']['index']]->name; ?>
" value="<?php echo $this->_tpl_vars['autores'][$this->_sections['as']['index']]->pk_author; ?>
" <?php if ($this->_tpl_vars['autores'][$this->_sections['as']['index']]->pk_author == $this->_tpl_vars['author_id']): ?> selected="selected" <?php endif; ?> ><?php echo $this->_tpl_vars['autores'][$this->_sections['as']['index']]->name; ?>
</option>
                                    <?php endfor; endif; ?>
                                 </select>
                             </form>
                        </div>
                        <div class="containerCol12 fondoContainerActualidad">
                            <?php if ($this->_tpl_vars['author_id'] > 0): ?>
                                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "opinion_author_opinions.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                            <?php else: ?>
                                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "opinion_index_content.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "opinion_index_column3.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                </div>
                
                <div class="separadorHorizontal"></div>
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_separadorbanners3.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                             </div>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        </div>
    </div>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_analytics.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</body>
</html>