<?php /* Smarty version 2.6.18, created on 2010-01-28 20:07:54
         compiled from modulo_header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'img_tag', 'modulo_header.tpl', 3, false),)), $this); ?>
<div class="header">
    <div class="logoXornalYBanner">        
        <div class="logoXornal"><a href="/"><?php echo smarty_function_img_tag(array('file' => "logo-opennemas.png",'baseurl' => $this->_tpl_vars['params']['IMAGE_DIR'],'alt' => 'OpenNemas'), $this);?>
</a></div>
    </div>    
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_sections_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_zonaHoraBusqueda.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>  
</div>