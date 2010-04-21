<?php /* Smarty version 2.6.18, created on 2010-04-21 13:34:01
         compiled from widget_ad_top.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'renderbanner', 'widget_ad_top.tpl', 10, false),)), $this); ?>

<div class="wrapper clearfix">
        <div id="publis" class="">
            <div id="publi_1" class="publi_banner">
                <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 1, 'width' => '728', 'height' => "*", 'cssclass' => "")), $this); ?>

            </div>
            <div id="publi_2" class="publi_corta">
                <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 2, 'width' => '234', 'height' => '90', 'cssclass' => "")), $this); ?>

            </div>
        </div>

</div>


