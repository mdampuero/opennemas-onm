<?php /* Smarty version 2.6.18, created on 2010-01-25 20:30:47
         compiled from index_deportesexpress.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'index_deportesexpress.tpl', 9, false),array('modifier', 'clearslash', 'index_deportesexpress.tpl', 11, false),)), $this); ?>
<div class="deportesExpress">
    <!-- ****************** DEPORTES XPRESS **************** -->
    <div class="containerDeportesXPress">
	<div class="cabeceraDeportesXPress"></div>
	<div id='div_deportes_express' class="listaDeportesXPress">	
		  		  <?php unset($this->_sections['exp']);
$this->_sections['exp']['name'] = 'exp';
$this->_sections['exp']['loop'] = is_array($_loop=$this->_tpl_vars['deportes_express']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['exp']['show'] = true;
$this->_sections['exp']['max'] = $this->_sections['exp']['loop'];
$this->_sections['exp']['step'] = 1;
$this->_sections['exp']['start'] = $this->_sections['exp']['step'] > 0 ? 0 : $this->_sections['exp']['loop']-1;
if ($this->_sections['exp']['show']) {
    $this->_sections['exp']['total'] = $this->_sections['exp']['loop'];
    if ($this->_sections['exp']['total'] == 0)
        $this->_sections['exp']['show'] = false;
} else
    $this->_sections['exp']['total'] = 0;
if ($this->_sections['exp']['show']):

            for ($this->_sections['exp']['index'] = $this->_sections['exp']['start'], $this->_sections['exp']['iteration'] = 1;
                 $this->_sections['exp']['iteration'] <= $this->_sections['exp']['total'];
                 $this->_sections['exp']['index'] += $this->_sections['exp']['step'], $this->_sections['exp']['iteration']++):
$this->_sections['exp']['rownum'] = $this->_sections['exp']['iteration'];
$this->_sections['exp']['index_prev'] = $this->_sections['exp']['index'] - $this->_sections['exp']['step'];
$this->_sections['exp']['index_next'] = $this->_sections['exp']['index'] + $this->_sections['exp']['step'];
$this->_sections['exp']['first']      = ($this->_sections['exp']['iteration'] == 1);
$this->_sections['exp']['last']       = ($this->_sections['exp']['iteration'] == $this->_sections['exp']['total']);
?>
		    <div class="deporteXPress">
		      <div class="horaDeporteXPress"><?php echo ((is_array($_tmp=$this->_tpl_vars['deportes_express'][$this->_sections['exp']['index']]->changed)) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
</div>
		      <div class="contTextoFileteDeporte">
			  <div class="textoDeporteXPress"><a href="<?php echo $this->_tpl_vars['deportes_express'][$this->_sections['exp']['index']]->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['deportes_express'][$this->_sections['exp']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></div>
			  <div class="fileteDeporteXPress"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
deportesXPress/fileteDashedDeportesXPress.gif" alt="" /></div>                                       
			</div>
		    </div>
		  <?php endfor; endif; ?>
		    
			<!-- LINK A MAS DEPORTESXPRESS-->		  
			<div class="linkMasDeportes">+Deportes</div>
			<div class="CPaginas"><?php echo $this->_tpl_vars['pages_deportes_express']->links; ?>
		
					</div>
		    </div>	 		
	</div>
    <!-- *************** FIN DEPORTES XPRESS *************** --> 
</div>