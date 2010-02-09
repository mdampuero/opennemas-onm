<?php /* Smarty version 2.6.18, created on 2010-01-18 17:20:16
         compiled from index_column2_frontpages.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'renderbanner', 'index_column2_frontpages.tpl', 46, false),)), $this); ?>
<div class="column2big">   <!-- PIEZA ESPECIAL NOTICIA -->

    <?php if (strtolower ( $this->_tpl_vars['category_name'] ) == 'deportes'): ?>
    <!-- ****************** WIDGET DEPORTES ************** -->
    <iframe src="/media/widgets/deportes/index.php" style="border: 0px solid #ffffff" marginwidth=0 marginheight=0 scrolling=no width=300 height=600 noresize="noresize" frameborder="0" border="0"></iframe>
    <!-- ****************** FIN DEPORTES  **************** -->
    <?php endif; ?>

    <?php if (strtolower ( $this->_tpl_vars['category_name'] ) == 'economia'): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "container_col2_economia_grafico.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <div class="separadorHorizontal"></div>
    <?php endif; ?>

    
    
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

    <!-- ******************  Humor Grafico (album) **************** -->
     <div class="containerNoticiasXPress">
        <div class="cabeceraHumorGrafico"></div>
        <br />
           <a href="<?php echo $this->_tpl_vars['SITE_URL']; ?>
<?php echo $this->_tpl_vars['alb_humor'][0]->permalink; ?>
">
            <img width="295" src="<?php echo $this->_tpl_vars['SITE_URL']; ?>
/media/images/<?php echo $this->_tpl_vars['humores']['path_file']; ?>
/<?php echo $this->_tpl_vars['humores']['name']; ?>
" alt=" <?php echo $this->_tpl_vars['alb_humor'][0]->title; ?>
" title=" <?php echo $this->_tpl_vars['alb_humor'][0]->title; ?>
" style="margin-top: 10px;"/></a>
          <div class="creditos2"><i>Grafico: <?php echo $this->_tpl_vars['alb_humor'][0]->title; ?>
</i></div>	     
   
    </div>
    <div class="separadorHorizontal"></div>
    <div class="containerNoticiasXPress">
	<a href="http://www.xornal.com/proxectoterra"><img border="0" alt="Proxecto Terra" src="http://www.xornal.com/files/bannerXentes.png"/></a>
    </div>
</div>