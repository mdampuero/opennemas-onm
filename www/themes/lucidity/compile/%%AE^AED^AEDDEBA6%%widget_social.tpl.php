<?php /* Smarty version 2.6.18, created on 2010-03-03 11:02:17
         compiled from widget_social.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'clearslash', 'widget_social.tpl', 14, false),)), $this); ?>
<?php unset($this->_sections['g']);
$this->_sections['g']['name'] = 'g';
$this->_sections['g']['loop'] = is_array($_loop=$this->_tpl_vars['titulares_gente']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['g']['show'] = true;
$this->_sections['g']['max'] = $this->_sections['g']['loop'];
$this->_sections['g']['step'] = 1;
$this->_sections['g']['start'] = $this->_sections['g']['step'] > 0 ? 0 : $this->_sections['g']['loop']-1;
if ($this->_sections['g']['show']) {
    $this->_sections['g']['total'] = $this->_sections['g']['loop'];
    if ($this->_sections['g']['total'] == 0)
        $this->_sections['g']['show'] = false;
} else
    $this->_sections['g']['total'] = 0;
if ($this->_sections['g']['show']):

            for ($this->_sections['g']['index'] = $this->_sections['g']['start'], $this->_sections['g']['iteration'] = 1;
                 $this->_sections['g']['iteration'] <= $this->_sections['g']['total'];
                 $this->_sections['g']['index'] += $this->_sections['g']['step'], $this->_sections['g']['iteration']++):
$this->_sections['g']['rownum'] = $this->_sections['g']['iteration'];
$this->_sections['g']['index_prev'] = $this->_sections['g']['index'] - $this->_sections['g']['step'];
$this->_sections['g']['index_next'] = $this->_sections['g']['index'] + $this->_sections['g']['step'];
$this->_sections['g']['first']      = ($this->_sections['g']['iteration'] == 1);
$this->_sections['g']['last']       = ($this->_sections['g']['iteration'] == $this->_sections['g']['total']);
?>
    <?php if ($this->_sections['g']['last']): ?>
        <div class="layout-column first-column span-3">
    <?php else: ?>
        <div class="layout-column first-column span-4">
    <?php endif; ?>
            <div>
                <div class="nw-big">
                    <img class="nw-image" style="width:152px;height:100px;" src="<?php echo @MEDIA_IMG_PATH_WEB; ?>
<?php echo $this->_tpl_vars['titulares_gente'][$this->_sections['g']['index']]->path_img; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_gente'][$this->_sections['g']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
"/>
                    <h4 class="nw-title"><a href="<?php echo $this->_tpl_vars['titulares_gente'][$this->_sections['g']['index']]->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['titulares_gente'][$this->_sections['g']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</a></h4>
                </div>
            </div>
        </div>
 <?php endfor; endif; ?>