<?php /* Smarty version 2.6.18, created on 2010-02-01 04:59:03
         compiled from article_printer.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'article_printer.tpl', 4, false),array('modifier', 'escape', 'article_printer.tpl', 4, false),array('modifier', 'count_words', 'article_printer.tpl', 37, false),array('function', 'breadcrub', 'article_printer.tpl', 28, false),array('function', 'articledate', 'article_printer.tpl', 40, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

    <script type="text/javascript" language="javascript" src="<?php echo $this->_tpl_vars['params']['JS_DIR']; ?>
prototype.js"></script>
    
    <link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
article_printer.css?cacheburst=1259853434"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['params']['CSS_DIR']; ?>
article_printer-p.css?cacheburst=1259853438" media="print" />
    
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
            <div class="breadcrub"><?php echo smarty_function_breadcrub(array('values' => $this->_tpl_vars['breadcrub']), $this);?>
</div>
            
            <a href="#imprimir" onclick="window.print();return false;" class="imprimir-link">Imprimir</a>
        </div>
        
        <div>  
            <h2><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</h2>
            
            <div class="firma_nota">
                <div class="firma_nombre"><?php if (((is_array($_tmp=$this->_tpl_vars['article']->agency)) ? $this->_run_mod_handler('count_words', true, $_tmp) : smarty_modifier_count_words($_tmp)) != '0'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->agency)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
<?php else: ?>Xornal de Galicia<?php endif; ?></div>
                <div class="separadorFirma"></div>
                                <?php echo smarty_function_articledate(array('article' => $this->_tpl_vars['article'],'created' => $this->_tpl_vars['article']->created), $this);?>

            </div>
            
            <div class="resumo">                
                <?php if ($this->_tpl_vars['photoInt']->name): ?>
                <div class="CNoticiaContenedorFoto">
                    <div class="CNoticia_foto">
                        <img src="<?php echo $this->_tpl_vars['MEDIA_IMG_PATH_WEB']; ?>
<?php echo $this->_tpl_vars['photoInt']->path_file; ?>
<?php echo $this->_tpl_vars['photoInt']->name; ?>
" alt="" />
                    </div>
                    <div class="clear"></div>
                    <div class="creditos_nota"><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->img2_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</div>	    
                </div>
                <?php endif; ?> 
                
                <?php echo ((is_array($_tmp=$this->_tpl_vars['article']->summary)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>

            </div>
        </div>
        <div class="clearer"></div>
        
        <div class="cuerpo_article">                                
            <?php echo ((is_array($_tmp=$this->_tpl_vars['article']->body)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>

        </div>            
    </div>

    <div class="url">
                <p><sub>[URL] http://www.xornal.com<?php echo $this->_tpl_vars['article']->permalink; ?>
</sub></p>
                
        <p>
            <strong>© XORNAL.COM, Fundado en 1999 como "EL PRIMER DIARIO ELECTRÓNICO DE GALICIA"</strong> <br />
            R/ Galileo Galilei, 4B (Polígono A Grela). <br />
            Redacción: redaccion@xornaldegalicia.com, Publicidad: publicidade@xornaldegalicia.com
        </p>
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