<?php /* Smarty version 2.6.18, created on 2010-03-03 23:00:18
         compiled from frontend_menu.tpl */ ?>
    
<div id="menus" class="">

    <div id="main_menu" class="span-24 clearfix">
        <div>
            <ul class="clearfix">
                <li><a href="/" title="Portada">PORTADA</a></li>
                <?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
                    <li><a href="/seccion/<?php echo $this->_tpl_vars['v']['name']; ?>
/" title="Sección: <?php echo $this->_tpl_vars['v']['title']; ?>
"><?php echo $this->_tpl_vars['v']['title']; ?>
</a></li>
                <?php endforeach; endif; unset($_from); ?>
            </ul>
        </div>

    </div>

    <div id="sub_main_menu" class="span-24">
        <?php if (! empty ( $this->_tpl_vars['categories'][$this->_tpl_vars['posmenu']]['subcategories'] )): ?>
            <ul class="clearfix">
                <?php $_from = $this->_tpl_vars['categories'][$this->_tpl_vars['posmenu']]['subcategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
                    <li><a href="/seccion/<?php echo $this->_tpl_vars['categories'][$this->_tpl_vars['posmenu']]['name']; ?>
/<?php echo $this->_tpl_vars['k']; ?>
/" title="<?php echo $this->_tpl_vars['v']; ?>
"><?php echo $this->_tpl_vars['v']; ?>
</a></li>
                <?php endforeach; endif; unset($_from); ?>
            </ul>
        <?php endif; ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "widget_search.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    </div>

</div>

 