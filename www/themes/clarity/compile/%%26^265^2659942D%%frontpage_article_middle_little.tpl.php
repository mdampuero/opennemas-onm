<?php /* Smarty version 2.6.18, created on 2010-04-22 12:03:42
         compiled from frontpage_article_middle_little.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'frontpage_article_middle_little.tpl', 10, false),)), $this); ?>

<div class="nw-little">
    <div class="content-new">
        <h3 class="nw-title"><a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
">
            <?php echo ((is_array($_tmp=$this->_tpl_vars['item']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</a>
        </h3>
        <p class="nw-subtitle"><?php echo ((is_array($_tmp=$this->_tpl_vars['item']->summary)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
 </p>
    </div>
</div>