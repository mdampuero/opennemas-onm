<?php /* Smarty version 2.6.18, created on 2010-01-26 18:10:03
         compiled from modulo_sections_menu.tpl */ ?>
<div class="zonaSecciones">      
    <div class="menuCabeceraTexto">
      <ul>
      <?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
        <?php if ($this->_tpl_vars['category_name'] == $this->_tpl_vars['v']['name']): ?>
            <li class="menuselec"><a href="/seccion/<?php echo $this->_tpl_vars['v']['name']; ?>
/"><?php echo $this->_tpl_vars['v']['title']; ?>
</a></li>
	    <?php else: ?>
            <li class="opcion"><a href="/seccion/<?php echo $this->_tpl_vars['v']['name']; ?>
/"><?php echo $this->_tpl_vars['v']['title']; ?>
</a></li>
	    <?php endif; ?>
	  <?php endforeach; endif; unset($_from); ?>
      </ul>
    </div>    
</div>