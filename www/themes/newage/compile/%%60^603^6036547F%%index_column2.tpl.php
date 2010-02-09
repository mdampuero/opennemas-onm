<?php /* Smarty version 2.6.18, created on 2010-01-26 16:50:13
         compiled from index_column2.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'renderitems', 'index_column2.tpl', 4, false),array('function', 'renderplaceholder', 'index_column2.tpl', 7, false),array('insert', 'renderbanner', 'index_column2.tpl', 24, false),)), $this); ?>
<div class="column2">

    <?php if (isset ( $_REQUEST['page'] ) && $_REQUEST['page'] > 0): ?>
         <?php echo smarty_function_renderitems(array('items' => $this->_tpl_vars['column'],'filter' => "\$i==1",'tpl' => "container_article_col2.tpl"), $this);?>

    <?php else: ?>
        <?php if (strtolower ( $this->_tpl_vars['category_name'] ) == 'home'): ?>
           <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'container_article_col2.tpl','placeholder' => 'placeholder_1_0'), $this);?>

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
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 5, 'cssclass' => 'contBannerPublicidad', 'width' => '245', 'height' => '245', 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>', 'afterHTML' => '<div class="separadorHorizontal"></div>')), $this); ?>

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
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 14, 'cssclass' => 'contBannerPublicidad', 'width' => '245', 'height' => '245', 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>', 'afterHTML' => '<div class="separadorHorizontal"></div>')), $this); ?>

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
        <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'container_article_col2.tpl','placeholder' => 'placeholder_1_3'), $this);?>

    <?php endif; ?>
    
    
    <!-- ****************** PUBLICIDAD ******************* -->
    <div class="contBannerYTextoPublicidad">
    <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 16, 'cssclass' => 'contBannerPublicidad', 'width' => '245', 'height' => '245', 'beforeHTML' => '<div class="textoBannerPublicidad">publicidad</div>', 'afterHTML' => '<div class="separadorHorizontal"></div>')), $this); ?>

    </div>  
</div>