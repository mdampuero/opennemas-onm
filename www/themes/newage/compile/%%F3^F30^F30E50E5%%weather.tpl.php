<?php /* Smarty version 2.6.18, created on 2010-01-26 22:03:19
         compiled from weather.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'weather.tpl', 26, false),array('function', 'remotecontent', 'weather.tpl', 33, false),)), $this); ?>
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
                        <?php if (! is_null ( $this->_tpl_vars['localidade'] )): ?>
                        <h2>El tiempo en <?php echo $this->_tpl_vars['titulo']; ?>
</h2>
                        <div class="subtitulo_nota">Predicción meteorológica para los próximos 7 días.</div>                        
                        <div class="apertura_nota">
                            <div class="firma_nota firma_tiempo">
                                <div class="firma_nombre">Información extraída del Institulo Nacional de Meteorología</div>
                                <div class="separadorFirma"></div>
                                <div class="firma_fecha"><?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d / %m / %Y") : smarty_modifier_date_format($_tmp, "%d / %m / %Y")); ?>
</div>
                                <div class="separadorFirma"></div>
                                <div class="firma_hora"><?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
 h.</div>
                            </div>
                        </div>                        
                        <table class="tabla_datos" summary="Esta tabla muestra la precidión para la localidad de <?php echo $this->_tpl_vars['titulo']; ?>
.">
                            <?php $this->assign('url', "http://www.aemet.es/es/eltiempo/prediccion/localidades?".($this->_tpl_vars['querystring'])); ?>
                            <?php echo smarty_function_remotecontent(array('url' => $this->_tpl_vars['url'],'onafter' => 'remotecontent_onafter_aemet','cache' => 'true','cachelife' => '120','cachename' => $this->_tpl_vars['cachename']), $this);?>
               
                        </table>                        
                        <?php else: ?>                             <h2>El tiempo</h2>
                            <div class="subtitulo_nota">Seleccione una ciudad para ver su predicción.</div>
                            
                            <div class="apertura_nota">
                                <div class="firma_nota firma_tiempo">
                                    <div class="firma_nombre">Información extraída del Institulo Nacional de Meteorología</div>
                                    <div class="separadorFirma"></div>
                                    <div class="firma_fecha"><?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d / %m / %Y") : smarty_modifier_date_format($_tmp, "%d / %m / %Y")); ?>
</div>
                                    <div class="separadorFirma"></div>
                                    <div class="firma_hora"><?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
 h.</div>
                                </div>
                            </div>                                                
                        <?php endif; ?>                        
                    </div> <!-- .containerTempo -->                
                </div> <!-- .column12 -->
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "weather_column3.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            </div> <!-- .containerNoticias -->            
            <div class="separadorHorizontal"></div>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modulo_separadorbanners2.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        </div> <!-- .container-->
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