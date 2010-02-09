<?php /* Smarty version 2.6.18, created on 2010-01-26 21:52:03
         compiled from index_column2_frontpages.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'renderbanner', 'index_column2_frontpages.tpl', 25, false),)), $this); ?>
<div class="column2big">   <!-- PIEZA ESPECIAL NOTICIA -->

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "index_noticias_express.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    <div class="separadorHorizontal"></div>
        
    <?php if ($this->_config[0]['vars']['container_noticias_gente'] == 1): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "index_gente.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endif; ?>
    <?php if ($this->_config[0]['vars']['container_noticias_fotos'] == 1): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_actualidadfotos.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endif; ?>

    <div class="separadorHorizontal"></div>

    <!-- ****************** PUBLICIDAD ******************* -->
    <div class="contBannerYTextoPublicidad">
        <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 5, 'cssclass' => 'contBannerPublicidad', 'width' => '295', 'height' => '295', 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>', 'afterHTML' => '<div class="separadorHorizontal"></div>')), $this); ?>

    </div>

    <!-- **************** ARTICLES MODULE ***************** -->
    
        
    <!-- ****************** PUBLICIDAD ******************* -->
    <div class="contBannerYTextoPublicidad">
        <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 14, 'cssclass' => 'contBannerPublicidad', 'width' => '295', 'height' => '295', 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>', 'afterHTML' => '<div class="separadorHorizontal"></div>')), $this); ?>

    </div>
    

    <!-- **************** ARTICLES MODULE ***************** -->
        
    <!-- **************** NOTICIA ESPECIAL *************** -->
        
    
    <!-- ****************** PUBLICIDAD ******************* -->
    <div class="contBannerYTextoPublicidad">
        <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 16, 'cssclass' => 'contBannerPublicidad', 'width' => '295', 'height' => '295', 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>', 'afterHTML' => '<div class="separadorHorizontal"></div>')), $this); ?>

    </div>  
</div>