<?php /* Smarty version 2.6.18, created on 2010-01-14 01:41:18
         compiled from modulo_header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'renderbanner', 'modulo_header.tpl', 5, false),)), $this); ?>
<div class="header">
    <div class="logoXornalYBanner">        
        <div class="logoXornal"><a href="/"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
xornal-logo.jpg" alt="" /></a></div>
                <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 3, 'cssclass' => 'zonaBannerYMenuInferior', 'width' => '610', 'height' => '70')), $this); ?>

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
    
    <?php if ($this->_tpl_vars['category_name'] == 'home'): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_carousel.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endif; ?>      
</div>