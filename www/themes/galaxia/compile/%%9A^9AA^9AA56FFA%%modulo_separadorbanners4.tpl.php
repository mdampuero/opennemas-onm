<?php /* Smarty version 2.6.18, created on 2010-01-14 01:41:18
         compiled from modulo_separadorbanners4.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'renderbanner', 'modulo_separadorbanners4.tpl', 6, false),)), $this); ?>
<div class="separadorHorizontal"></div>
<div class="separadorBanners">
        
    <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 11, 'cssclass' => 'banner728x90')), $this); ?>
    
    <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 13, 'cssclass' => 'banner234x90')), $this); ?>

</div>