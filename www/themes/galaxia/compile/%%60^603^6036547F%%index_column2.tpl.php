<?php /* Smarty version 2.6.18, created on 2010-01-14 01:41:18
         compiled from index_column2.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'renderitems', 'index_column2.tpl', 66, false),array('function', 'renderplaceholder', 'index_column2.tpl', 69, false),array('insert', 'renderbanner', 'index_column2.tpl', 86, false),)), $this); ?>
<div class="column2">
    
    
    
            
    
        <?php if (strtolower ( $this->_tpl_vars['category_name'] ) == 'home' && $this->_tpl_vars['articles_impresa']): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "container_noticias_impresa.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <div class="separadorHorizontal"></div>
        <?php endif; ?>

    
    <!-- ::::::::::::::::::   WIDGET DEPORTES :::::::::::::: ::::::: -->

        <?php if (strtolower ( $this->_tpl_vars['category_name'] ) == 'deportes'): ?>                         
                        
                        <iframe src="/media/widgets/deportes/index.php" style="border: 0px solid #ffffff" marginwidth=0 marginheight=0 scrolling=no width=300 height=600 noresize="noresize" frameborder="0" border="0"></iframe>
            <div class="separadorHorizontal"></div>

        <?php else: ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "index_noticias_express.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                <div class="separadorHorizontal"></div>

        <?php endif; ?>

    <!-- :::::::::::::::: FIN  WIDGET DEPORTES :::::::::::::::::::: -->    

    <?php if (strtolower ( $this->_tpl_vars['category_name'] ) == 'economia'): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "container_col2_economia_grafico.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <div class="separadorHorizontal"></div>
    <?php endif; ?>

    
    <?php if (isset ( $_REQUEST['page'] ) && $_REQUEST['page'] > 0): ?>
         <?php echo smarty_function_renderitems(array('items' => $this->_tpl_vars['column'],'filter' => "\$i==1",'tpl' => "container_article_col2.tpl"), $this);?>

    <?php else: ?>
        <?php if (strtolower ( $this->_tpl_vars['category_name'] ) == 'home'): ?>
           <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'container_article_col2_destacado.tpl','placeholder' => 'placeholder_1_0'), $this);?>

        <?php else: ?>
           <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'container_article_col2.tpl','placeholder' => 'placeholder_1_0'), $this);?>

        <?php endif; ?>
    <?php endif; ?>
    
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
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 5, 'cssclass' => 'contBannerPublicidad', 'width' => '300', 'height' => '300', 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>', 'afterHTML' => '<div class="separadorHorizontal"></div>')), $this); ?>

    </div>

    <!-- **************** ARTICLES MODULE ***************** -->
    
    <?php if (isset ( $_REQUEST['page'] ) && $_REQUEST['page'] > 0): ?>
        <?php echo smarty_function_renderitems(array('items' => $this->_tpl_vars['column'],'filter' => "\$i%2==1 && \$i>=3 && \$i<=7",'tpl' => "container_article_col2.tpl"), $this);?>

    <?php else: ?>
        <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'container_article_col2.tpl','placeholder' => 'placeholder_1_1'), $this);?>

    <?php endif; ?>
    
    <!-- ****************** PUBLICIDAD ******************* -->
    <div class="contBannerYTextoPublicidad">
    <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 14, 'cssclass' => 'contBannerPublicidad', 'width' => '300', 'height' => '300', 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>', 'afterHTML' => '<div class="separadorHorizontal"></div>')), $this); ?>

    </div>
    

    <!-- **************** ARTICLES MODULE ***************** -->
    <?php if (isset ( $_REQUEST['page'] ) && $_REQUEST['page'] > 0): ?>
        <?php echo smarty_function_renderitems(array('items' => $this->_tpl_vars['column'],'filter' => "\$i==9",'tpl' => "container_article_col2.tpl"), $this);?>

    <?php else: ?>
        <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'container_article_col2.tpl','placeholder' => 'placeholder_1_2'), $this);?>

    <?php endif; ?>
    
    <!-- **************** NOTICIA ESPECIAL *************** -->
    <?php if (isset ( $_REQUEST['page'] ) && $_REQUEST['page'] > 0): ?>
        <?php echo smarty_function_renderitems(array('items' => $this->_tpl_vars['column'],'filter' => "\$i==11",'tpl' => "container_article_col2_especial.tpl"), $this);?>

    <?php else: ?>
        <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'container_article_col2_especial.tpl','placeholder' => 'placeholder_1_3'), $this);?>

    <?php endif; ?>
    
    
    <!-- ****************** PUBLICIDAD ******************* -->
    <div class="contBannerYTextoPublicidad">
    <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 16, 'cssclass' => 'contBannerPublicidad', 'width' => '300', 'height' => '300', 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>', 'afterHTML' => '<div class="separadorHorizontal"></div>')), $this); ?>

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
          <div class="creditos2"><i>Gráfico: <?php echo $this->_tpl_vars['alb_humor'][0]->title; ?>
</i></div>	     
   
    </div>
    <div class="separadorHorizontal"></div>
    <div class="containerNoticiasXPress">
	<a href="http://www.xornal.com/proxectoterra"><img border="0" alt="Proxecto Terra" src="http://www.xornal.com/files/bannerXentes.png"/></a>
    </div>
</div>