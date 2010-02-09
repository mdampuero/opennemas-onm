<?php /* Smarty version 2.6.18, created on 2010-01-30 05:51:22
         compiled from mobile/header.tpl */ ?>
<?php echo '<?xml'; ?>
 version="1.0" encoding="UTF-8" <?php echo '?>'; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
	<title>Xornal.com versión móvil</title> 
    <link rel="stylesheet" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
<?php echo @BASE_URL; ?>
/estilo.css" type="text/css" media="screen" charset="UTF-8" />
    <link rel="stylesheet" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
<?php echo @BASE_URL; ?>
/estilo.css" type="text/css" media="handheld" charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />    
</head>
    
<body>	
    <div id="cabecera">
        <a href="http://www.xornal.com<?php echo @BASE_URL; ?>
/" title="Xornal de Galicia - Xornal.com">
            <img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
xornal-logo.jpg" alt="" /></a>
    </div>
    
        <div id="menu">
        <?php if ($this->_tpl_vars['section'] != 'home'): ?>
            <a href="<?php echo @BASE_URL; ?>
/">portada</a>
        <?php endif; ?>
        <a href="<?php echo @BASE_URL; ?>
/ultimas-noticias/"<?php if ($this->_tpl_vars['section'] == 'ultimas'): ?> class="current"<?php endif; ?>>últimas noticias</a>
        <a href="<?php echo @BASE_URL; ?>
/seccion/opinion/"<?php if (strtolower ( $this->_tpl_vars['section'] ) == 'opinion'): ?> class="current"<?php endif; ?>>opinion</a>
        <a href="<?php echo @BASE_URL; ?>
/seccion/polItica/"<?php if (strtolower ( $this->_tpl_vars['section'] ) == 'politica'): ?> class="current"<?php endif; ?>>politica</a>
        <a href="<?php echo @BASE_URL; ?>
/seccion/galicia/"<?php if (strtolower ( $this->_tpl_vars['section'] ) == 'galicia'): ?> class="current"<?php endif; ?>>galicia</a>
        <a href="#secciones">más...</a>        
    </div>
    
    	
	<div id="contenido">
		