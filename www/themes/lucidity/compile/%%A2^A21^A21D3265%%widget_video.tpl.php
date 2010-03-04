<?php /* Smarty version 2.6.18, created on 2010-03-03 11:25:13
         compiled from widget_video.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'widget_video.tpl', 23, false),array('modifier', 'escape', 'widget_video.tpl', 23, false),)), $this); ?>
    
<?php if ($this->_tpl_vars['type_video'] == 'youtube'): ?>
   
    <div class="youtube-highlighter clearfix">
        <div class="youtube-highlighter-header"><img src="<?php echo @MEDIA_PATH_URL; ?>
/sections/youtube.png" alt=""/></div>
        <div class="youtube-highlighter-big clearfix">
            <object  width="220" height="184" >
                <param value="http://www.youtube.com/v/<?php echo $this->_tpl_vars['videos'][0]->videoid; ?>
" name="movie" />
                <param value="true" name="allowFullScreen" />
                <param value="always" name="allowscriptaccess">
                <embed width="240" height="180" src="http://www.youtube.com/v/<?php echo $this->_tpl_vars['videos'][0]->videoid; ?>
" />
            </object>
        </div>
        <ul class="youtube-highligher-little-section-links">
            <?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['videos']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['start'] = (int)0;
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
if ($this->_sections['i']['start'] < 0)
    $this->_sections['i']['start'] = max($this->_sections['i']['step'] > 0 ? 0 : -1, $this->_sections['i']['loop'] + $this->_sections['i']['start']);
else
    $this->_sections['i']['start'] = min($this->_sections['i']['start'], $this->_sections['i']['step'] > 0 ? $this->_sections['i']['loop'] : $this->_sections['i']['loop']-1);
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
                    <li class="first"><a href="#">
                        <img width="60" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="http://i4.ytimg.com/vi/<?php echo $this->_tpl_vars['videos'][$this->_sections['i']['index']]->videoid; ?>
/default.jpg" />
                    </a></li>
                <?php elseif ($this->_sections['i']['last']): ?>
                    <li class="last"><a href="#">
                        <img width="60" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="http://i4.ytimg.com/vi/<?php echo $this->_tpl_vars['videos'][$this->_sections['i']['index']]->videoid; ?>
/default.jpg" />
                    </a></li>
                <?php else: ?>
                    <li class=""><a href="#">
                        <img width="60" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="http://i4.ytimg.com/vi/<?php echo $this->_tpl_vars['videos'][$this->_sections['i']['index']]->videoid; ?>
/default.jpg" />
                    </a></li>
                <?php endif; ?>

            <?php endfor; endif; ?>
        </ul>
    </div>
   
<?php else: ?>
   
    <div class="tv-highlighter clearfix">
        <div class="tv-highlighter-header clearfix">
            <img src="<?php echo @MEDIA_PATH_URL; ?>
/sections/tv.png" alt="" />
            <form action="#">
                <select name="asdf">
                    <option>Deportes</option>
                    <option>Cotilleo</option>
                    <option>Política</option>
                    <option>Otros</option>
                </select>
            </form>
        </div>
        <div class="tv-highlighter-big clearfix">
            <img src="<?php echo @MEDIA_PATH_URL; ?>
/video.futebol.png" alt="" align="center"/>
            <p>
                Partido sin complicaciones para los Vascos<br/>
                <img src="<?php echo @MEDIA_PATH_URL; ?>
/stars.png" alt="" />
            </p>
        </div>

    </div>
 
<?php endif; ?>