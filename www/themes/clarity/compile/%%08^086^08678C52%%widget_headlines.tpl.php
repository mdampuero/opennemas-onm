<?php /* Smarty version 2.6.18, created on 2010-04-22 13:15:28
         compiled from widget_headlines.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'renderTypeRelated', 'widget_headlines.tpl', 21, false),array('modifier', 'clearslash', 'widget_headlines.tpl', 35, false),)), $this); ?>
 
 <div class="span-7 layout-column last widget-lastest-tab">
    <div class="title">       
    </div>
    <div id="tabs2" class="content">
            <ul>
                <li><a href="#tab-more-views"><span>+Visto</span></a></li>
                <li><a href="#tab-more-comments"><span>+Comentado</span></a></li>

            </ul>
            <div id="tab-more-views">
                               <?php unset($this->_sections['a']);
$this->_sections['a']['name'] = 'a';
$this->_sections['a']['loop'] = is_array($_loop=$this->_tpl_vars['articles_viewed']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['a']['show'] = true;
$this->_sections['a']['max'] = $this->_sections['a']['loop'];
$this->_sections['a']['step'] = 1;
$this->_sections['a']['start'] = $this->_sections['a']['step'] > 0 ? 0 : $this->_sections['a']['loop']-1;
if ($this->_sections['a']['show']) {
    $this->_sections['a']['total'] = $this->_sections['a']['loop'];
    if ($this->_sections['a']['total'] == 0)
        $this->_sections['a']['show'] = false;
} else
    $this->_sections['a']['total'] = 0;
if ($this->_sections['a']['show']):

            for ($this->_sections['a']['index'] = $this->_sections['a']['start'], $this->_sections['a']['iteration'] = 1;
                 $this->_sections['a']['iteration'] <= $this->_sections['a']['total'];
                 $this->_sections['a']['index'] += $this->_sections['a']['step'], $this->_sections['a']['iteration']++):
$this->_sections['a']['rownum'] = $this->_sections['a']['iteration'];
$this->_sections['a']['index_prev'] = $this->_sections['a']['index'] - $this->_sections['a']['step'];
$this->_sections['a']['index_next'] = $this->_sections['a']['index'] + $this->_sections['a']['step'];
$this->_sections['a']['first']      = ($this->_sections['a']['iteration'] == 1);
$this->_sections['a']['last']       = ($this->_sections['a']['iteration'] == $this->_sections['a']['total']);
?>
                    <div class="tab-lastest clearfix">
                        <div class="tab-lastest-title">
                             <?php echo smarty_function_renderTypeRelated(array('content' => $this->_tpl_vars['articles_viewed'][$this->_sections['a']['index']]), $this);?>

                                                   </div>
                    </div>
                <?php endfor; endif; ?>
            </div>
            <div id="tab-more-comments">
                                <?php $_from = $this->_tpl_vars['articles_comments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['article']):
?>
                    <div class="tab-lastest clearfix">
                        <div class="tab-lastest-title">
                            <a href="<?php echo $this->_tpl_vars['article']['permalink']; ?>
" title="<?php echo $this->_tpl_vars['article']['title']; ?>
">
                                <?php echo ((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('clearslash', true, $_tmp) : clearslash($_tmp)); ?>

                            </a>
                        </div>
                    </div>
                <?php endforeach; endif; unset($_from); ?>

                            </div>

        </div>
 </div>