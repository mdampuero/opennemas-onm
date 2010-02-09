<?php /* Smarty version 2.6.18, created on 2010-02-08 00:20:51
         compiled from index_column3.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'renderbanner', 'index_column3.tpl', 17, false),)), $this); ?>
<div class="column3">
    <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
logos/portada_papel_title.gif" alt="Contexto" style="margin-left:4px;"/>
    <?php if (isset ( $this->_tpl_vars['frontpage_newspaper_img'] ) && preg_match ( '/\.jpg$/' , $this->_tpl_vars['frontpage_newspaper_img'] )): ?>
    <div style="margin-right:5px;text-align: center;">
        <a href="<?php echo @SITE_URL; ?>
/portadas/#xornal" title="Primera pagina de la version impresa">
            <img src="/media/images/kiosko/<?php echo $this->_tpl_vars['frontpage_newspaper_img']; ?>
" border="0" alt="Xornal Frontpage newspaper" /></a>
    </div>
    <?php endif; ?>

<script type="text/javascript" src="http://cdn.widgetserver.com/syndication/subscriber/InsertWidget.js"></script><script>if (WIDGETBOX) WIDGETBOX.renderWidget('1dfcceaf-6acd-4a13-8218-dcc4d55fe567');</script><noscript>Get the <a href="http://www.widgetbox.com/widget/dralzheimer">Desktop Wallpapers</a> widget and many other <a href="http://www.widgetbox.com/">great free widgets</a> at <a href="http://www.widgetbox.com">Widgetbox</a>! Not seeing a widget? (<a href="http://docs.widgetbox.com/using-widgets/installing-widgets/why-cant-i-see-my-widget/">More info</a>)</noscript>
<script type="text/javascript" src="http://cdn.widgetserver.com/syndication/subscriber/InsertWidget.js"></script><script>if (WIDGETBOX) WIDGETBOX.renderWidget('a757d348-7ce0-4a8f-80e9-df9503b3944b');</script><noscript>Get the <a href="http://www.widgetbox.com/widget/facebookminisite">Facebook Minisite</a> widget and many other <a href="http://www.widgetbox.com/">great free widgets</a> at <a href="http://www.widgetbox.com">Widgetbox</a>! Not seeing a widget? (<a href="http://docs.widgetbox.com/using-widgets/installing-widgets/why-cant-i-see-my-widget/">More info</a>)</noscript>


    
    <div class="contBannerYTextoPublicidadCol3">
        <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 6, 'cssclass' => 'contBannerPublicidadCol3', 'width' => '255', 'height' => "*", 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>')), $this); ?>

    </div>
    <div class="separadorHorizontal"></div>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "container_bolsa.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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