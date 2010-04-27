<?php /* Smarty version 2.6.18, created on 2010-04-22 12:16:43
         compiled from widget_video.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'widget_video.tpl', 12, false),array('modifier', 'escape', 'widget_video.tpl', 12, false),)), $this); ?>

<div class="layout-column last-column span-12 last">
    <div class="photos-highlighter clearfix span-12">
        <div class="photos-header"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
/widgets/videos-highlighter-header.png" alt=""/></div>
        <div class="photos-highlighter-big clearfix">
             <?php if ($this->_tpl_vars['videos'][0]->author_name == 'youtube'): ?>
                <a href="<?php echo $this->_tpl_vars['videos'][0]->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
                   <object width="300" height="210">
                        <param value="http://www.youtube.com/v/<?php echo $this->_tpl_vars['videos'][0]->videoid; ?>
" name="movie" />
                        <param value="true" name="allowFullScreen" />
                        <param value="always" name="allowscriptaccess">
                        <embed width="300" height="210" src="http://www.youtube.com/v/<?php echo $this->_tpl_vars['videos'][0]->videoid; ?>
" />
                    </object>
                </a>
             <?php else: ?>
              <a href="<?php echo $this->_tpl_vars['videos'][0]->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
                <object width="320" height="220">
                    <param name="allowfullscreen" value="true" />
                    <param name="allowscriptaccess" value="always" />
                    <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $this->_tpl_vars['videos'][0]->videoid; ?>
&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" />
                    <embed src="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $this->_tpl_vars['videos'][0]->videoid; ?>
&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="330" height="220"></embed>
                </object>
                </a>
              <?php endif; ?>
              <div class="info"><a href="<?php echo $this->_tpl_vars['videos'][0]->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
                   <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 </a>
              </div>
        </div>
        <ul class="photos-highligher-little-section-links">
            <?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['videos']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                    <li  class="first"><a href="<?php echo $this->_tpl_vars['videos'][$this->_sections['i']['index']]->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
                    <?php elseif ($this->_sections['i']['last']): ?>
                        <li class="last"><a href="<?php echo $this->_tpl_vars['videos'][$this->_sections['i']['index']]->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
                    <?php else: ?>
                         <li class=""><a href="<?php echo $this->_tpl_vars['videos'][$this->_sections['i']['index']]->permalink; ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
                    <?php endif; ?>
                        <?php if ($this->_tpl_vars['videos'][$this->_sections['i']['index']]->author_name == 'youtube'): ?>
                             <img style="width:90px;" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="http://i4.ytimg.com/vi/<?php echo $this->_tpl_vars['videos'][$this->_sections['i']['index']]->videoid; ?>
/default.jpg" />
                        <?php else: ?>
                              <img style="width:90px;"  alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="<?php echo $this->_tpl_vars['videos'][$this->_sections['i']['index']]->thumbnail_small; ?>
" />
                        <?php endif; ?>
                    </a></li>
            <?php endfor; endif; ?>
        </ul>
    </div>
</div> 