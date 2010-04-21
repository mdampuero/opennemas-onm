<?php /* Smarty version 2.6.18, created on 2010-04-21 13:34:01
         compiled from module_other_headlines.tpl */ ?>

<hr class="new-separator" />
<div class="span-24">
    <div class="layout-column first-column span-16">
        <div class="more-news">
            <h4>Más noticias</h4>
            <hr class="more-news-separator" />
            
                <?php $_from = $this->_tpl_vars['categories_data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?> 
                     <?php if (! empty ( $this->_tpl_vars['titulares_cat'][$this->_tpl_vars['k']] )): ?>
                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_more_news.tpl", 'smarty_include_vars' => array('category_hdata' => ($this->_tpl_vars['v']),'index' => ($this->_tpl_vars['k']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                        <hr class="more-news-inner-separator" />
                    <?php endif; ?>
                <?php endforeach; endif; unset($_from); ?>

        </div>
    </div>
    <div class="span-7 more-promotions-from-diary">
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_other_info.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

     </div>
</div>
