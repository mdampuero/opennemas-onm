<?php /* Smarty version 2.6.18, created on 2010-04-22 23:28:23
         compiled from article_inner_column.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'article_inner_column.tpl', 11, false),)), $this); ?>
<div class="nw-big">


        <?php if (! empty ( $this->_tpl_vars['item']->img1_path )): ?>
            <img class="nw-image" src="<?php echo @MEDIA_IMG_PATH_WEB; ?>
/<?php echo $this->_tpl_vars['item']->img1_path; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->img_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->img_footer)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
"/>
        <?php endif; ?>
        <h3 class="nw-title"><a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->permalink)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['item']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['item']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</a></h3>
        <p class="nw-subtitle"> <?php echo ((is_array($_tmp=$this->_tpl_vars['item']->summary)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
 </p>

     
</div>
  					 