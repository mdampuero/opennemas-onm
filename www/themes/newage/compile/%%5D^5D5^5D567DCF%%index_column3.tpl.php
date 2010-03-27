<?php /* Smarty version 2.6.18, created on 2010-02-16 15:39:34
         compiled from index_column3.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'renderbanner', 'index_column3.tpl', 16, false),)), $this); ?>
<div class="column3">
    <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
logos/facebook-logo.png" alt="Contexto" style="text-align:center;margin:5px;"/>
    <?php if (isset ( $this->_tpl_vars['frontpage_newspaper_img'] ) && preg_match ( '/\.jpg$/' , $this->_tpl_vars['frontpage_newspaper_img'] )): ?>
    <div style="margin-right:5px;text-align: center;">
        <a href="<?php echo @SITE_URL; ?>
/portadas/#xornal" title="Primera pagina de la version impresa">
            <img src="/media/images/kiosko/<?php echo $this->_tpl_vars['frontpage_newspaper_img']; ?>
" border="0" alt="Xornal Frontpage newspaper" /></a>
    </div>
    <?php endif; ?>

    <iframe scrolling="no" frameborder="0" src="http://www.facebook.com/connect/connect.php?id=282535299100&connections=10"
      allowtransparency="true" style="border: none; width: 250px; height: 250px;"></iframe>
      
    
    <div class="contBannerYTextoPublicidadCol3">
        <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 6, 'cssclass' => 'contBannerPublicidadCol3', 'width' => '255', 'height' => "*", 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>')), $this); ?>

    </div>
    
    <div class="separadorHorizontal"></div>
        <!--DO NOT EDIT BELOW!- WIDGETS: http://www.sanebull.com/widgets -->
    <iframe scrolling="no" width="210" height="410" frameborder="0" marginheight="0" marginwidth="0" src="http://www.sanebull.com/widget_world_watch.jsp?market=all" id="display-widget"></iframe>
    <!--DO NOT EDIT ABOVE!-->

    <div class="separadorHorizontal"></div>    
    <?php if (! preg_match ( '/weather\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_weather.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endif; ?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "container_suplementos.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <br /> <br />
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "container_extras.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <div class="separadorHorizontal"></div>
    <div class="contBannerYTextoPublicidadCol3">
        <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 15, 'cssclass' => 'contBannerPublicidadCol3', 'width' => '255', 'height' => "*", 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>')), $this); ?>

    </div>
    <div class="separadorHorizontal"></div>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_actualidadfotos.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>