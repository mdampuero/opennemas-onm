<?php /* Smarty version 2.6.18, created on 2010-04-22 12:50:45
         compiled from widget_utilities.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'widget_utilities.tpl', 20, false),)), $this); ?>

<div class="utilities span-6 last">
   <ul>
    <li><a href="" class="utilities-send-by-email"  onclick="return false;" title="Enviar por email a un amigo"><span>Enviar por email</span></a></li>
    <li><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
utilities/separator.png" alt="Email" onclick="javascript:sendbyemail('Título da nova')"/></li>
    <li><a href="" class="utilities-print" onclick="javascript:window.print();return false;" title="Imprimir"><span>Imprimir</span></a></li>
    <li><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
utilities/separator.png" alt="Email" /></li>
    <li><a href="" class="utilities-increase-text"  onclick="increaseFontSize();return false;" title="Incrementar el tamaño del texto"><span>Incrementar el tamaño del texto</span></a></li>
    <li><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
utilities/separator.png" alt="Email" /></li>
    <li><a href="" class="utilities-decrease-text"  onclick="decreaseFontSize();return false;" title="Decrementar el tamaño del texto"><span>Reducir el tamaño del texto</span></a></li>
    <li><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
utilities/separator.png" alt="Email" /></li>
    <li>
      <div style="display: inline;" class="share-actions">
            <a href="#" class="utilities-share" onclick="share();return false;" title="Compartir en las redes sociales"><span>Compartir en las redes sociales</span></a>
            <ul style="display:none;">
              <li><img alt="Share this post on Twitter" src="/themes/lucidity/images/utilities/toolsicon_anim.gif"> <a title="Compartir en Twiter" target="_blank" href="http://twitter.com/home?status=<?php if (! empty ( $this->_tpl_vars['article']->title_int )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title_int)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
<?php endif; ?> <?php echo @SITE_URL; ?>
<?php echo $this->_tpl_vars['article']->permalink; ?>
">Send to Twitter</a></li>
              <li><img alt="Share on Facebook" src="/themes/lucidity/images/utilities/facebook-share.gif"> <a title="Compartir en Facebook" href="http://www.facebook.com/sharer.php?u=<?php echo @SITE_URL; ?>
<?php echo $this->_tpl_vars['article']->permalink; ?>
&t=<?php if (! empty ( $this->_tpl_vars['article']->title_int )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title_int)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['article']->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
<?php endif; ?>">Share on Facebook</a></li>
            </ul>
      </div>
    </li>
</ul>
</div><!-- /utilities -->
