<?php /* Smarty version 2.6.18, created on 2010-01-25 21:19:12
         compiled from modulo_categoriesMenu.tpl */ ?>
<?php $_from = $this->_tpl_vars['categories'][$this->_tpl_vars['posmenu']]['subcategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>    
    <div class="elemMenuBarraFecha elemMenuBarraFechaSec">        
        <?php if ($this->_tpl_vars['subcategory_name'] == $this->_tpl_vars['k']): ?>
            <a style="text-decoration:underline;" href="/seccion/<?php echo $this->_tpl_vars['categories'][$this->_tpl_vars['posmenu']]['name']; ?>
/<?php echo $this->_tpl_vars['k']; ?>
/"><?php echo $this->_tpl_vars['v']; ?>
</a>
        <?php else: ?>
            <a href="/seccion/<?php echo $this->_tpl_vars['categories'][$this->_tpl_vars['posmenu']]['name']; ?>
/<?php echo $this->_tpl_vars['k']; ?>
/"><?php echo $this->_tpl_vars['v']; ?>
</a>
        <?php endif; ?>
    </div>
    <div class="separadorElemMenuBarraFecha separadorElemMenuBarraFechaSec"></div>
<?php endforeach; endif; unset($_from); ?>