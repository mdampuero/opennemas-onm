<?php /* Smarty version 2.6.18, created on 2010-01-27 11:45:35
         compiled from playas.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'playas.tpl', 24, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_head.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<body>
<style>
@import url( <?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
fotoVideoDia.css );
</style>
<!--[if IE]>
<script language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
png.js" type="text/javascript"></script>
<![endif]-->
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
                    <div class="containerTempo">
                        <h2>Predicción meteorol&oacute;gica para hoy.</h2>
                        <div class="apertura_nota">
                            <div class="firma_nota firma_tiempo">
                                <div class="firma_nombre">Informaci&oacute;n extra&iacute;da de Meteogalicia (Conseller&iacute;a de Medio Ambiente, Territorio e Infraestruturas) e Agencia Estatal de Meteorolog&iacute;a</div>
                                <div class="separadorFirma"></div>
                                <div class="firma_fecha"><?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d / %m / %Y") : smarty_modifier_date_format($_tmp, "%d / %m / %Y")); ?>
</div>
                                <div class="separadorFirma"></div>
                                <div class="firma_hora"><?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
 h.</div>
                            </div>
                        </div>
                                                <img src='<?php echo $this->_tpl_vars['img_tiempo']; ?>
' alt='Tiempo' title='Tiempo'>
                    </div>
                </div>
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "weather_column3.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            </div>
            <div class="separadorHorizontal"></div>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_separadorbanners2.tpl", 'smarty_include_vars' => array()));
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