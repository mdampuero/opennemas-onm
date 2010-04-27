<?php /* Smarty version 2.6.18, created on 2010-04-22 12:53:59
         compiled from widget_ad_column.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'widget_ad_column.tpl', 2, false),array('insert', 'renderbanner', 'widget_ad_column.tpl', 2, false),)), $this); ?>
<div class="publi_310 clearfix">
        <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => ((is_array($_tmp=@$this->_tpl_vars['type'])) ? $this->_run_mod_handler('default', true, $_tmp, '103') : smarty_modifier_default($_tmp, '103')), 'cssclass' => "", 'width' => '284', 'height' => "*")), $this); ?>

</div>