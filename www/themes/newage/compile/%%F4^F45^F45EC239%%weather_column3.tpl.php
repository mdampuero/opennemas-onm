<?php /* Smarty version 2.6.18, created on 2010-01-26 22:03:21
         compiled from weather_column3.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'renderbanner', 'weather_column3.tpl', 5, false),)), $this); ?>
<div class="column3">
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_weather.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <div class="separadorHorizontal"></div>
    <div class="contBannerYTextoPublicidadCol3">
        <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 6, 'cssclass' => 'contBannerPublicidadCol3', 'width' => '180', 'height' => "*", 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>')), $this); ?>

    </div>
</div>