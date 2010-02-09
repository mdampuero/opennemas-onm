<?php /* Smarty version 2.6.18, created on 2010-01-14 01:41:18
         compiled from modulo_separadorbanners1.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'renderbanner', 'modulo_separadorbanners1.tpl', 4, false),)), $this); ?>
<div class="separadorBannersTOP">        
        <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 1, 'cssclass' => 'bannerTOP728x90')), $this); ?>
    
    <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'renderbanner', 'type' => 2, 'cssclass' => 'bannerTOP234x90')), $this); ?>

</div>