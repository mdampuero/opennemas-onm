<?php /* Smarty version 2.6.18, created on 2010-01-25 21:38:24
         compiled from modulo_opinion_lista_xornalistas.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'truncate', 'modulo_opinion_lista_xornalistas.tpl', 7, false),)), $this); ?>
<form method="post" action="/opinions/listar_autores/'.$('autores').options[this.selectedIndex].value/<?php echo time(); ?>
.html">        
    <select name="autores" id="autores" class="" onChange="<?php echo ' if(this.options[this.selectedIndex].value!=-1){ window.location=\'/opinions/opinions_do_autor/\'+this.options[this.selectedIndex].value+\'/\'+this.options[this.selectedIndex].getAttribute(\'name\')+\'.html\';} '; ?>
">
        <option name="" value="0">--Autor--</option>
        <option name="Editorial" value="1" <?php if ($this->_tpl_vars['author_id'] == 1): ?> selected="selected" <?php endif; ?>>Editorial</option>
        <option name="Director" value="2" <?php if ($this->_tpl_vars['author_id'] == 2): ?> selected="selected" <?php endif; ?> >Director</option>
        <?php unset($this->_sections['as']);
$this->_sections['as']['name'] = 'as';
$this->_sections['as']['loop'] = is_array($_loop=$this->_tpl_vars['todos_pag']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                <option name="<?php echo $this->_tpl_vars['todos_pag'][$this->_sections['as']['index']]->name; ?>
" value="<?php echo $this->_tpl_vars['todos_pag'][$this->_sections['as']['index']]->pk_author; ?>
" <?php if ($this->_tpl_vars['todos_pag'][$this->_sections['as']['index']]->pk_author == $this->_tpl_vars['author_id']): ?> selected="selected" <?php endif; ?> ><?php echo ((is_array($_tmp=$this->_tpl_vars['todos_pag'][$this->_sections['as']['index']]->name)) ? $this->_run_mod_handler('truncate', true, $_tmp, 24, "...", 'TRUE') : smarty_modifier_truncate($_tmp, 24, "...", 'TRUE')); ?>
 </option>
        <?php endfor; endif; ?>
    </select>
</form>