<?php /* Smarty version 2.6.18, created on 2010-04-22 13:33:02
         compiled from article_last_column.tpl */ ?>

<div class="layout-column last-column last span-8">
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_TA_buttons.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_ad_column.tpl", 'smarty_include_vars' => array('type' => '103')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    <hr class="new-separator"/>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_column_video.tpl", 'smarty_include_vars' => array('video' => $this->_tpl_vars['videoInt'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    <hr class="new-separator"/>

    <div class="inner-news-highligther">
        <h3 class="widget-title">Destacadas en deportes <img src="images/bullets/bars-red.png" /></h3>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "article_inner_column.tpl", 'smarty_include_vars' => array('item' => $this->_tpl_vars['other_news'][0])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "article_inner_column.tpl", 'smarty_include_vars' => array('item' => $this->_tpl_vars['other_news'][1])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_headlines_past.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    <hr class="new-separator" />

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_ad_column.tpl", 'smarty_include_vars' => array('type' => '105')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    
 
</div>