<?php /* Smarty version 2.6.18, created on 2010-04-22 12:22:10
         compiled from widget_gallery.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'widget_gallery.tpl', 10, false),array('modifier', 'escape', 'widget_gallery.tpl', 10, false),)), $this); ?>
<div class="layout-column first-column span-12">
    <div class="photos-highlighter clearfix span-12">
        <div class="photos-header"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
widgets/photos-highlighter-header.png" alt=""/></div>
        <div class="photos-highlighter-big clearfix">
            <a href="<?php echo $this->_tpl_vars['lastAlbum'][0]->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" >
                <img alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="<?php echo @MEDIA_IMG_PATH_WEB; ?>
album/crops/<?php echo $this->_tpl_vars['lastAlbum'][0]->id; ?>
.jpg" />                
            </a>
            <div class="info"><a href="<?php echo $this->_tpl_vars['lastAlbum'][0]->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" ><?php echo ((is_array($_tmp=$this->_tpl_vars['lastAlbum'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</a></div>
        </div>
        <ul class="photos-highligher-little-section-links">
            <?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['lastAlbum']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['max'] = (int)3;
$this->_sections['i']['show'] = true;
if ($this->_sections['i']['max'] < 0)
    $this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = min(ceil(($this->_sections['i']['step'] > 0 ? $this->_sections['i']['loop'] - $this->_sections['i']['start'] : $this->_sections['i']['start']+1)/abs($this->_sections['i']['step'])), $this->_sections['i']['max']);
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
                    <li class="first"><a href="<?php echo $this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" >
                        <img alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="<?php echo @MEDIA_IMG_PATH_WEB; ?>
album/crops/<?php echo $this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->id; ?>
.jpg" />
                    </a></li>
                <?php elseif ($this->_sections['i']['last']): ?>
                    <li class="last"><a href="<?php echo $this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" >
                        <img alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="<?php echo @MEDIA_IMG_PATH_WEB; ?>
album/crops/<?php echo $this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->id; ?>
.jpg" />
                    </a></li>
                <?php else: ?>
                    <li class=""><a href="<?php echo $this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" >
                        <img alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="<?php echo @MEDIA_IMG_PATH_WEB; ?>
album/crops/<?php echo $this->_tpl_vars['lastAlbum'][$this->_sections['i']['index']]->id; ?>
.jpg" />
                    </a></li>
                <?php endif; ?>

            <?php endfor; endif; ?>
             
        </ul>
    </div>
</div>