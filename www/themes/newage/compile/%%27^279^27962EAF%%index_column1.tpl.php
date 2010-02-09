<?php /* Smarty version 2.6.18, created on 2010-01-25 20:30:46
         compiled from index_column1.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'renderplaceholder', 'index_column1.tpl', 8, false),array('function', 'renderitems', 'index_column1.tpl', 17, false),)), $this); ?>
<div class="column1">
        
        <?php if (! empty ( $this->_tpl_vars['destaca'] )): ?>
        <?php if (( $this->_tpl_vars['destaca'][0]->columns != '2' && $this->_tpl_vars['category_name'] != 'home' ) || ( $this->_tpl_vars['destaca'][0]->home_columns != '2' && $this->_tpl_vars['category_name'] == 'home' )): ?>    
                        <?php echo smarty_function_renderplaceholder(array('video' => $this->_tpl_vars['video_destacada'],'items' => $this->_tpl_vars['destaca'],'relationed' => $this->_tpl_vars['relationed'],'tpl' => 'container_article_destacado.tpl','placeholder' => 'placeholder_0_0'), $this);?>

        <?php endif; ?>
    <?php endif; ?>
    
    <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['articles_home'],'tpl' => 'container_article_col1.tpl','placeholder' => 'placeholder_0_0'), $this);?>
    
    
        
    <?php if (isset ( $_REQUEST['page'] ) && $_REQUEST['page'] > 0): ?>
        <?php echo smarty_function_renderitems(array('items' => $this->_tpl_vars['column'],'filter' => "\$i%2==0 && \$i<11",'tpl' => "container_article_col1.tpl"), $this);?>

        <?php echo smarty_function_renderitems(array('items' => $this->_tpl_vars['column'],'filter' => "\$i>=12 ",'tpl' => "container_article_col1.tpl"), $this);?>

    <?php else: ?>
        <?php echo smarty_function_renderplaceholder(array('items' => $this->_tpl_vars['column'],'tpl' => 'container_article_col1.tpl','placeholder' => 'placeholder_0_1'), $this);?>

    <?php endif; ?>

    <?php if (! empty ( $this->_tpl_vars['paginacion'] )): ?>         <p align='center'>
        <a title="Portada" href="/seccion/<?php echo $this->_tpl_vars['actual_category']; ?>
/"> Portada </a> |
        <?php echo $this->_tpl_vars['paginacion']; ?>

        </p>
    <?php endif; ?>
</div>