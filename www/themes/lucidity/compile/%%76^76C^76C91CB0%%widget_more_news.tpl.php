<?php /* Smarty version 2.6.18, created on 2010-03-04 11:21:55
         compiled from widget_more_news.tpl */ ?>
    
<div class="more-news-section">
    <ul class="more-news-section-sectionlist clearfix">
        <li class="first"><strong><a href="/seccion/<?php echo $this->_tpl_vars['category_data']['name']; ?>
/" title="Sección:<?php echo $this->_tpl_vars['category_data']['title']; ?>
"><?php echo $this->_tpl_vars['category_data']['title']; ?>
</strong>:</a></li>
         <?php if (! empty ( $this->_tpl_vars['category_data']['subcategories'] )): ?>
             <?php $_from = $this->_tpl_vars['category_data']['subcategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['s'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['s']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['sub']):
        $this->_foreach['s']['iteration']++;
?>
                <?php if (($this->_foreach['s']['iteration'] == $this->_foreach['s']['total'])): ?>
                    <li class="last"><a href="/seccion/<?php echo $this->_tpl_vars['category_data']['name']; ?>
/<?php echo $this->_tpl_vars['k']; ?>
/" title="Seccion:<?php echo $this->_tpl_vars['sub']; ?>
"><?php echo $this->_tpl_vars['sub']; ?>
</a></li>
                <?php else: ?>
                    <li ><a href="/seccion/<?php echo $this->_tpl_vars['category_data']['name']; ?>
/<?php echo $this->_tpl_vars['k']; ?>
/" title="Seccion:<?php echo $this->_tpl_vars['sub']; ?>
"><?php echo $this->_tpl_vars['sub']; ?>
</a></li>
                <?php endif; ?>
             <?php endforeach; endif; unset($_from); ?>
        <?php endif; ?>
    </ul>
    <ul class="more-news-section-links">
        <?php $_from = $this->_tpl_vars['titulares_cat'][$this->_tpl_vars['index']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['t'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['t']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['sub']):
        $this->_foreach['t']['iteration']++;
?>
            <?php if (($this->_foreach['t']['iteration'] <= 1)): ?>
                <li class="first"><a href="/seccion/<?php echo $this->_tpl_vars['category_data']['name']; ?>
/<?php echo $this->_tpl_vars['k']; ?>
/" title="<?php echo $this->_tpl_vars['sub']['title']; ?>
"><?php echo $this->_tpl_vars['sub']['title']; ?>
</a></li>
            <?php elseif (($this->_foreach['t']['iteration'] == $this->_foreach['t']['total'])): ?>
                <li class="last"><a href="/seccion/<?php echo $this->_tpl_vars['category_data']['name']; ?>
/<?php echo $this->_tpl_vars['k']; ?>
/" title="<?php echo $this->_tpl_vars['sub']['title']; ?>
"><?php echo $this->_tpl_vars['sub']['title']; ?>
</a></li>
            <?php else: ?>
                <li><a href="/seccion/<?php echo $this->_tpl_vars['category_data']['name']; ?>
/<?php echo $this->_tpl_vars['k']; ?>
/" title="<?php echo $this->_tpl_vars['sub']['title']; ?>
"><?php echo $this->_tpl_vars['sub']['title']; ?>
</a></li>
            <?php endif; ?>
        <?php endforeach; endif; unset($_from); ?>
    </ul>
</div>

 