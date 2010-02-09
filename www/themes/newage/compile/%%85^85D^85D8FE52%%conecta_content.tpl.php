<?php /* Smarty version 2.6.18, created on 2010-01-26 01:11:30
         compiled from conecta_content.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'upper', 'conecta_content.tpl', 14, false),array('modifier', 'clearslash', 'conecta_content.tpl', 16, false),array('function', 'humandate', 'conecta_content.tpl', 18, false),)), $this); ?>
<div class="zonaClasificacionContenidosPortadaPC">
    <div class="listadoEnlacesPlanConecta">

        <div class="titleContent"> FOTOGRAFÍAS</div>
        <div class="separadorContents" style="background-color:#5ED63B;"> </div>
        <?php $_from = $this->_tpl_vars['photo_categorys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['cat_id'] => $this->_tpl_vars['category_photo']):
        $this->_foreach['foo']['iteration']++;
?>
            <?php $this->assign('content', $this->_tpl_vars['category_photo']->contents); ?>
            <?php if ($this->_foreach['foo']['iteration'] % 2 != 0): ?>
                <div class="filaPortadaPC" style="margin-top:10px;">
            <?php endif; ?>
                <div class="elementoListadoMediaPagPortadaPC">
                    <div class="fotoElemPC"><a href="<?php echo $this->_tpl_vars['content'][0]->permalink; ?>
"><img  style="width:150px;" src="<?php echo $this->_tpl_vars['MEDIA_CONECTA_WEB']; ?>
<?php echo $this->_tpl_vars['content'][0]->path_file; ?>
" /></a></div>
                    <div class="contSeccionFechaListadoPortadaPC">
                        <div class="seccionMediaListado"><a href='/conecta/fotografias/<?php echo $this->_tpl_vars['category_photo']->name; ?>
/'><?php echo ((is_array($_tmp=$this->_tpl_vars['category_photo']->title)) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
 </a></div>
                        <?php $this->assign('id_author', $this->_tpl_vars['content'][0]->fk_user); ?>
                        <div class="contentListado">  <a href="<?php echo $this->_tpl_vars['content'][0]->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['content'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
                          <br />  Enviado por: <?php echo $this->_tpl_vars['conecta_users'][$this->_tpl_vars['id_author']]->nick; ?>

                          <?php echo smarty_function_humandate(array('article' => $this->_tpl_vars['content'],'created' => $this->_tpl_vars['content'][0]->created), $this);?>

                          
                           ... 
                        </div>
                    </div>
                    
                </div>
                <?php if ($this->_foreach['foo']['iteration'] % 2 != 0): ?>
                      <div class="fileteVerticalIntraMedia"></div>
                <?php else: ?>
                    </div>
                    <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
                <?php endif; ?>
         <?php endforeach; endif; unset($_from); ?>
         <?php if ($this->_foreach['foo']['iteration'] % 2 != 0): ?>
             </div>
              <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
         <?php endif; ?>

          <div class="titleContent"> VIDEOS</div>
        <div class="separadorContents"  style="background-color:#A9087E;"> </div>
        <?php $_from = $this->_tpl_vars['video_categorys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['cat_id'] => $this->_tpl_vars['category_video']):
        $this->_foreach['foo']['iteration']++;
?>
            <?php $this->assign('content', $this->_tpl_vars['category_video']->contents); ?>
            <?php if ($this->_foreach['foo']['iteration'] % 2 != 0): ?>
                <div class="filaPortadaPC" style="margin-top:10px;">
            <?php endif; ?>
                <div class="elementoListadoMediaPagPortadaPC">
                    <div class="fotoElemPC"><a href="<?php echo $this->_tpl_vars['content'][0]->permalink; ?>
"><img style="width:150px;height:100px;" src="http://i4.ytimg.com/vi/<?php echo $this->_tpl_vars['content'][0]->code; ?>
/default.jpg" /></a></div>
                    <div class="contSeccionFechaListadoPortadaPC">
                        <div class="seccionMediaListado"><a href='/conecta/videos/<?php echo $this->_tpl_vars['category_video']->name; ?>
/'><?php echo ((is_array($_tmp=$this->_tpl_vars['category_video']->title)) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</a> </div>
                        <?php $this->assign('id_author', $this->_tpl_vars['content'][0]->fk_user); ?>
                         <div class="contentListado">  <a href="<?php echo $this->_tpl_vars['content'][0]->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['content'][0]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
                          <br />  Enviado por: <?php echo $this->_tpl_vars['conecta_users'][$this->_tpl_vars['id_author']]->nick; ?>

                          <?php echo smarty_function_humandate(array('article' => $this->_tpl_vars['content'],'created' => $this->_tpl_vars['content'][0]->created), $this);?>

                        </div>
                     </div>
                </div>
                <?php if ($this->_foreach['foo']['iteration'] % 2 != 0): ?>
                      <div class="fileteVerticalIntraMedia"></div>
                <?php else: ?>
                    </div>
                    <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
                <?php endif; ?>
         <?php endforeach; endif; unset($_from); ?>
         <?php if ($this->_foreach['foo']['iteration'] % 2 != 0): ?>
             </div>
              <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
         <?php endif; ?>

          <div class="titleContent"> OPINIÓN</div>
        <div class="separadorContents" style="background-color:#FFCA00;"> </div>
        <?php $_from = $this->_tpl_vars['opinion_categorys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['cat_id'] => $this->_tpl_vars['category_opinion']):
        $this->_foreach['foo']['iteration']++;
?>
            <?php $this->assign('content', $this->_tpl_vars['category_opinion']->contents); ?>
            <?php if ($this->_foreach['foo']['iteration'] % 2 != 0): ?>
                <div class="filaPortadaPC" style="margin-top:10px;">
            <?php endif; ?>
               <?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['content']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['c']['show'] = true;
$this->_sections['c']['max'] = $this->_sections['c']['loop'];
$this->_sections['c']['step'] = 1;
$this->_sections['c']['start'] = $this->_sections['c']['step'] > 0 ? 0 : $this->_sections['c']['loop']-1;
if ($this->_sections['c']['show']) {
    $this->_sections['c']['total'] = $this->_sections['c']['loop'];
    if ($this->_sections['c']['total'] == 0)
        $this->_sections['c']['show'] = false;
} else
    $this->_sections['c']['total'] = 0;
if ($this->_sections['c']['show']):

            for ($this->_sections['c']['index'] = $this->_sections['c']['start'], $this->_sections['c']['iteration'] = 1;
                 $this->_sections['c']['iteration'] <= $this->_sections['c']['total'];
                 $this->_sections['c']['index'] += $this->_sections['c']['step'], $this->_sections['c']['iteration']++):
$this->_sections['c']['rownum'] = $this->_sections['c']['iteration'];
$this->_sections['c']['index_prev'] = $this->_sections['c']['index'] - $this->_sections['c']['step'];
$this->_sections['c']['index_next'] = $this->_sections['c']['index'] + $this->_sections['c']['step'];
$this->_sections['c']['first']      = ($this->_sections['c']['iteration'] == 1);
$this->_sections['c']['last']       = ($this->_sections['c']['iteration'] == $this->_sections['c']['total']);
?>
                <div class="elementoListadoMediaPagPortadaPC">                  
                    <div class="contSeccionFechaListado2PortadaPC">
                        <div class="seccionMediaListado"><a href='/conecta/opiniones/<?php echo $this->_tpl_vars['category_opinion']->name; ?>
/'><?php echo ((is_array($_tmp=$this->_tpl_vars['category_opinion']->title)) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
 </a></div>
                        <?php $this->assign('id_author', $this->_tpl_vars['content'][$this->_sections['c']['index']]->fk_user); ?>
                         <div class="contentListado">  <a href="<?php echo $this->_tpl_vars['content'][$this->_sections['c']['index']]->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['content'][$this->_sections['c']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
                          <br />  Enviado por: <?php echo $this->_tpl_vars['conecta_users'][$this->_tpl_vars['id_author']]->nick; ?>

                          <?php echo smarty_function_humandate(array('article' => $this->_tpl_vars['content'],'created' => $this->_tpl_vars['content'][0]->created), $this);?>

                        </div>
                      </div>
                </div>
                    <div class="fileteVerticalIntraMedia"></div>
                <?php endfor; endif; ?>
                <?php if ($this->_foreach['foo']['iteration'] % 2 != 0): ?>
                      <div class="fileteVerticalIntraMedia"></div>
                <?php else: ?>
                    </div>
                    <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
                <?php endif; ?>
         <?php endforeach; endif; unset($_from); ?>
         <?php if ($this->_foreach['foo']['iteration'] % 2 != 0): ?>
             </div>
              <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
         <?php endif; ?>

          <div class="titleContent"> CARTAS </div>
        <div class="separadorContents" style="background-color:#00E2F6;"> </div>
        <?php $_from = $this->_tpl_vars['letter_categorys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['cat_id'] => $this->_tpl_vars['category_letter']):
        $this->_foreach['foo']['iteration']++;
?>
            <?php $this->assign('content', $this->_tpl_vars['category_letter']->contents); ?>
            <?php if ($this->_foreach['foo']['iteration'] % 2 != 0): ?>
                <div class="filaPortadaPC" style="margin-top:10px;">
            <?php endif; ?>
               <?php unset($this->_sections['c']);
$this->_sections['c']['name'] = 'c';
$this->_sections['c']['loop'] = is_array($_loop=$this->_tpl_vars['content']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['c']['show'] = true;
$this->_sections['c']['max'] = $this->_sections['c']['loop'];
$this->_sections['c']['step'] = 1;
$this->_sections['c']['start'] = $this->_sections['c']['step'] > 0 ? 0 : $this->_sections['c']['loop']-1;
if ($this->_sections['c']['show']) {
    $this->_sections['c']['total'] = $this->_sections['c']['loop'];
    if ($this->_sections['c']['total'] == 0)
        $this->_sections['c']['show'] = false;
} else
    $this->_sections['c']['total'] = 0;
if ($this->_sections['c']['show']):

            for ($this->_sections['c']['index'] = $this->_sections['c']['start'], $this->_sections['c']['iteration'] = 1;
                 $this->_sections['c']['iteration'] <= $this->_sections['c']['total'];
                 $this->_sections['c']['index'] += $this->_sections['c']['step'], $this->_sections['c']['iteration']++):
$this->_sections['c']['rownum'] = $this->_sections['c']['iteration'];
$this->_sections['c']['index_prev'] = $this->_sections['c']['index'] - $this->_sections['c']['step'];
$this->_sections['c']['index_next'] = $this->_sections['c']['index'] + $this->_sections['c']['step'];
$this->_sections['c']['first']      = ($this->_sections['c']['iteration'] == 1);
$this->_sections['c']['last']       = ($this->_sections['c']['iteration'] == $this->_sections['c']['total']);
?>
                <div class="elementoListadoMediaPagPortadaPC">                    
                    <div class="contSeccionFechaListado2PortadaPC">
                        <div class="seccionMediaListado"><a href='/conecta/cartas/<?php echo $this->_tpl_vars['category_letter']->name; ?>
/'><?php echo ((is_array($_tmp=$this->_tpl_vars['category_letter']->title)) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
 </a></div>
                        <?php $this->assign('id_author', $this->_tpl_vars['content'][$this->_sections['c']['index']]->fk_user); ?>
                         <div class="contentListado">  <a href="<?php echo $this->_tpl_vars['content'][$this->_sections['c']['index']]->permalink; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['content'][$this->_sections['c']['index']]->title)) ? $this->_run_mod_handler('clearslash', true, $_tmp) : smarty_modifier_clearslash($_tmp)); ?>
</a>
                          <br />  Enviado por: <?php echo $this->_tpl_vars['conecta_users'][$this->_tpl_vars['id_author']]->nick; ?>

                          <?php echo smarty_function_humandate(array('article' => $this->_tpl_vars['content'],'created' => $this->_tpl_vars['content'][0]->created), $this);?>

                        </div>
                     </div>
                </div>
                <div class="fileteVerticalIntraMedia"></div>
                <?php endfor; endif; ?>
                <?php if ($this->_foreach['foo']['iteration'] % 2 != 0): ?>
                      <div class="fileteVerticalIntraMedia"></div>
                <?php else: ?>
                    </div>
                    <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
                <?php endif; ?>
         <?php endforeach; endif; unset($_from); ?>
         <?php if ($this->_foreach['foo']['iteration'] % 2 != 0): ?>
             </div>
              <div class="fileteHorizontalPC" style="margin-left:14px;margin-bottom:14px;"></div>
         <?php endif; ?>


    </div>
</div>