<?php /* Smarty version 2.6.18, created on 2010-01-27 11:45:25
         compiled from opinion_printer.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'opinion_printer.tpl', 4, false),array('modifier', 'escape', 'opinion_printer.tpl', 4, false),array('function', 'articledate', 'opinion_printer.tpl', 35, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['opinion']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

    <script type="text/javascript" language="javascript" src="<?php echo($this->js_dir); ?>prototype.js"></script>
    
    <link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
article_printer.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
article_printer-p.css" media="print" />
    
    <meta name="robots" content="noindex, nofollow" />
    <meta name="googlebot" content="noindex, noarchive, nofollow" /> 
</head>

<body>
<div id="container">
    
    <div class="logoXornalYBanner">        
        <div class="logoXornal">
            <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
xornal-logo.jpg" alt="Xornal.com - Xornal de Galicia"
                 height="70" />
        </div>
    </div>
    <div class="noticia">        
        <div>           
            <a href="#imprimir" onclick="window.print();return false;" class="imprimir-link">Imprimir</a>
        </div>        
        <div>  
            <h2><?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</h2>
            
            <div class="firma_nota">
                <div class="firma_nombre"><?php echo $this->_tpl_vars['author']; ?>
</div>
                <div class="separadorFirma"></div>
                <?php echo smarty_function_articledate(array('article' => $this->_tpl_vars['opinion'],'created' => $this->_tpl_vars['opinion']->created,'updated' => $this->_tpl_vars['opinion']->changed), $this);?>

            </div>
        </div>
        <div class="clearer"></div>
        
        <div class="cuerpo_article">                                
            <?php echo ((is_array($_tmp=$this->_tpl_vars['opinion']->body)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>

        </div>            
    </div>

    <div class="url">
                <sub>[URL] http://www.xornal.com<?php echo $this->_tpl_vars['opinion']->permalink; ?>
</sub>
    </div>
</div>

<?php echo '
<script type="text/javascript">
Event.observe(window, \'load\', function() {
    window.print();
});
</script>
'; ?>


</body>
</html>