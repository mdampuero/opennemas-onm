<?php /* Smarty version 2.6.18, created on 2010-03-03 17:01:15
         compiled from widget_headlines.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'widget_headlines.tpl', 12, false),array('modifier', 'clearslash', 'widget_headlines.tpl', 13, false),)), $this); ?>

<div class="lastest-news clearfix">
    <span style="float:left;"><strong>Últimas noticias</strong>: </span>
    <ul class="slide_cicle" style="width:300px;margin:0;margin-left:4px;padding:0">
        <?php unset($this->_sections['exp']);
$this->_sections['exp']['name'] = 'exp';
$this->_sections['exp']['loop'] = is_array($_loop=$this->_tpl_vars['articles_home_express']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['exp']['show'] = true;
$this->_sections['exp']['max'] = $this->_sections['exp']['loop'];
$this->_sections['exp']['step'] = 1;
$this->_sections['exp']['start'] = $this->_sections['exp']['step'] > 0 ? 0 : $this->_sections['exp']['loop']-1;
if ($this->_sections['exp']['show']) {
    $this->_sections['exp']['total'] = $this->_sections['exp']['loop'];
    if ($this->_sections['exp']['total'] == 0)
        $this->_sections['exp']['show'] = false;
} else
    $this->_sections['exp']['total'] = 0;
if ($this->_sections['exp']['show']):

            for ($this->_sections['exp']['index'] = $this->_sections['exp']['start'], $this->_sections['exp']['iteration'] = 1;
                 $this->_sections['exp']['iteration'] <= $this->_sections['exp']['total'];
                 $this->_sections['exp']['index'] += $this->_sections['exp']['step'], $this->_sections['exp']['iteration']++):
$this->_sections['exp']['rownum'] = $this->_sections['exp']['iteration'];
$this->_sections['exp']['index_prev'] = $this->_sections['exp']['index'] - $this->_sections['exp']['step'];
$this->_sections['exp']['index_next'] = $this->_sections['exp']['index'] + $this->_sections['exp']['step'];
$this->_sections['exp']['first']      = ($this->_sections['exp']['iteration'] == 1);
$this->_sections['exp']['last']       = ($this->_sections['exp']['iteration'] == $this->_sections['exp']['total']);
?>
            <li class="teaser" id="teaser-<?php echo $this->_sections['exp']['iteration']; ?>
" style="margin:0; padding:0;  ">
                <?php echo ((is_array($_tmp=$this->_tpl_vars['articles_home_express'][$this->_sections['exp']['index']]->created)) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>

                <a href="<?php echo $this->_tpl_vars['articles_home_express'][$this->_sections['exp']['index']]->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['articles_home_express'][$this->_sections['exp']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>
</a>
            </li>
        <?php endfor; endif; ?>
    </ul>
</div>

