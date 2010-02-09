<?php /* Smarty version 2.6.18, created on 2010-01-14 01:41:18
         compiled from index_noticias_express.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'index_noticias_express.tpl', 9, false),array('modifier', 'clearslash', 'index_noticias_express.tpl', 14, false),)), $this); ?>
<!-- ****************** NOTICIAS XPRESS **************** -->
<div style="margin-top:10px" class="containerNoticiasXPress">
  <div class="cabeceraNoticiasXPress"></div>
  <div id='div_articles_home_express' class="listaNoticiasXPress">
      <!-- NOTICIA XPRES -->
      <?php unset($this->_sections['exp']);
$this->_sections['exp']['name'] = 'exp';
$this->_sections['exp']['loop'] = is_array($_loop=$this->_tpl_vars['articles_home_express']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
    <div class="noticiaXPress">
        <div class="contHoraNoticiaXPress">
        <div class="horaNoticiaXPress"><?php echo ((is_array($_tmp=$this->_tpl_vars['articles_home_express'][$this->_sections['exp']['index']]->created)) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
</div>
        <div class="iconoRayoXPress"></div>
        </div>
        <div class="contTextoFilete">
        <div class="textoNoticiaXPress">
          <a href="<?php echo $this->_tpl_vars['articles_home_express'][$this->_sections['exp']['index']]->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['articles_home_express'][$this->_sections['exp']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a></div>
        <div class="fileteNoticiaXPress">
          <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
noticiasXPress/fileteDashedNoticiasXPress.gif" alt=""/></div>
        </div>
    </div>
      <?php endfor; endif; ?>
   
	  <!-- LINK A MAS NOTICIASXPRESS-->
	    <div class="CContenedorPaginado">
	        <div class="link_mas_nota">+ NoticiasXpress</div>
	        <div class="CPaginas"> <?php echo $this->_tpl_vars['pages_home_express']->links; ?>
          
	        </div>
	    </div>
	  <!-- LINK A MAS NOTICIASXPRESS-->
	  
	    <!-- FIN NOTICIA XPRESS -->
  </div>
</div>
<!-- ****************** FIN NOTICIAS XPRESS ************ -->