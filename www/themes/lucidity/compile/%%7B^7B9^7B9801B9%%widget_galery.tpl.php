<?php /* Smarty version 2.6.18, created on 2010-03-03 22:14:59
         compiled from widget_galery.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'widget_galery.tpl', 13, false),array('modifier', 'escape', 'widget_galery.tpl', 13, false),)), $this); ?>
    
<div class="flickr-highlighter clearfix">
    <div class="flickr-highlighter-header"><img src="<?php echo @MEDIA_PATH_URL; ?>
/sections/flickr.png" alt=""/></div>
    <div class="flickr-highlighter-big clearfix">
          <?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['lastAlbum']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
                <?php if ($this->_sections['i']['first']): ?>
                    <li class="first"><a href="#">
                        <img alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="<?php echo @MEDIA_IMG_PATH_WEB; ?>
album/crops/<?php echo $this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->id; ?>
.jpg" />
                    </a></li>
                <?php elseif ($this->_sections['i']['last']): ?>
                    <li class="last"><a href="#">
                        <img alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="<?php echo @MEDIA_IMG_PATH_WEB; ?>
album/crops/<?php echo $this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->id; ?>
.jpg" />
                    </a></li>
                <?php else: ?>
                    <li class=""><a href="#">
                        <img alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="<?php echo @MEDIA_IMG_PATH_WEB; ?>
album/crops/<?php echo $this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->id; ?>
.jpg" />
                    </a></li>
                <?php endif; ?>

            <?php endfor; endif; ?>
        <a href="#">Envíanos tu careto, y te pondremos aquí...</a>
    </div>
    <div class="flickr-highlighter-footer">&nbsp;</div>
</div>