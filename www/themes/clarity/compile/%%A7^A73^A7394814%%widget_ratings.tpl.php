<?php /* Smarty version 2.6.18, created on 2010-04-22 12:50:45
         compiled from widget_ratings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'rating', 'widget_ratings.tpl', 27, false),array('insert', 'numComments', 'widget_ratings.tpl', 28, false),)), $this); ?>
    
<div class="vote-block span-10 ">
    <div class="vote">      
  
        <?php if (preg_match ( '/video\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
            <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'rating', 'id' => $this->_tpl_vars['video']->id, 'page' => 'video', 'type' => 'vote')), $this); ?>

             - <span><?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'numComments', 'id' => $this->_tpl_vars['video']->id)), $this); ?>
  Comentarios<span>
         <?php elseif (preg_match ( '/gallery\.php/' , $_SERVER['SCRIPT_NAME'] )): ?>
            <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'rating', 'id' => $this->_tpl_vars['album']->id, 'page' => 'video', 'type' => 'vote')), $this); ?>

             - <span><?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'numComments', 'id' => $this->_tpl_vars['album']->id)), $this); ?>
  Comentarios<span>
        <?php else: ?>
            <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'rating', 'id' => $this->_tpl_vars['article']->id, 'page' => 'article', 'type' => 'vote')), $this); ?>

               - <span><?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'numComments', 'id' => $this->_tpl_vars['article']->id)), $this); ?>
  Comentarios<span>
        <?php endif; ?>

        
    </div>
</div><!-- /vote-bloc -->