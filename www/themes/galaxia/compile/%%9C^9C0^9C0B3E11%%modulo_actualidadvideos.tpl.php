<?php /* Smarty version 2.6.18, created on 2010-01-14 01:41:18
         compiled from modulo_actualidadvideos.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'modulo_actualidadvideos.tpl', 17, false),array('modifier', 'regex_replace', 'modulo_actualidadvideos.tpl', 17, false),array('modifier', 'escape', 'modulo_actualidadvideos.tpl', 17, false),array('modifier', 'nl2br', 'modulo_actualidadvideos.tpl', 17, false),)), $this); ?>
<div class="actualidadVideos">
    <div class="cabeceraActualidadVideos"><a href="/video/"><img src="<?php echo $this->_tpl_vars['params']['IMAGE_DIR']; ?>
actualidadVideosFotos/logoActualidadVideos.gif" alt="Actualidad Videos" /></a></div>
    <div class="zonaVisualizacionVideos">
        <div class="CZonaVisorVideos" id="videoactual">
            <object width="250" height="250">
                <param value="http://www.youtube.com/v/<?php echo $this->_tpl_vars['videos'][0]->videoid; ?>
" name="movie" />
                <param value="true" name="allowFullScreen" />
                <param value="always" name="allowscriptaccess">
                <embed width="250" height="250" src="http://www.youtube.com/v/<?php echo $this->_tpl_vars['videos'][0]->videoid; ?>
" />
            </object>
        </div>
        <div class="CZonaThumbsVideos">
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
            <div class="CThumbVideo">
              <div class="CHolderThumbVideo">
                    <span class="CEdgeThumbVideo" />
                    <span onclick="cambiavideo('<?php echo $this->_tpl_vars['videos'][$this->_sections['i']['index']]->videoid; ?>
','<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/'/", "\'") : smarty_modifier_regex_replace($_tmp, "/'/", "\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
');" class="CContainerThumbVideo"><img width="70" alt="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" title="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" src="http://i4.ytimg.com/vi/<?php echo $this->_tpl_vars['videos'][$this->_sections['i']['index']]->videoid; ?>
/default.jpg"  onmouseout="UnTip()" onmouseover="Tip('<b><?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/'/", "\'") : smarty_modifier_regex_replace($_tmp, "/'/", "\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</b><br /><?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][$this->_sections['i']['index']]->description)) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)))) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/[\r\t\n]/", ' ') : smarty_modifier_regex_replace($_tmp, "/[\r\t\n]/", ' ')))) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/'/", "\'") : smarty_modifier_regex_replace($_tmp, "/'/", "\'")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
', ABOVE, false, OFFSETY, 0, BGCOLOR, '#E4DDC9', BORDERCOLOR, '#CFBA81', WIDTH, 300)" /></span>
              </div>
            </div>
            <?php endfor; endif; ?>
        </div>
        <div class="CContainerTituloVideo">
            <div class="CPieFotoPiezaVideoXornal" id="videotitle"><div class="CFlechaGrisPieGenteXornal"></div><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['videos'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</div>
        </div>
    </div>
    <div class="linkMasMedia"><a href="/video">+ Videos</a></div>
</div>