<?php /* Smarty version Smarty3-RC3, created on 2010-11-25 18:01:26
         compiled from "/var/www/retrincos/retrincosv82/trunk/public//admin/themes/default/tpl/opinion_list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11926362574cee96669ddbe9-10837516%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd1f6653c449791d601c3a9abc7865f7f0a439338' => 
    array (
      0 => '/var/www/retrincos/retrincosv82/trunk/public//admin/themes/default/tpl/opinion_list.tpl',
      1 => 1288947464,
    ),
  ),
  'nocache_hash' => '11926362574cee96669ddbe9-10837516',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_cycle')) include '/var/www/retrincos/retrincosv82/trunk/public/libs/smarty/plugins/function.cycle.php';
if (!is_callable('smarty_modifier_clearslash')) include '/var/www/retrincos/retrincosv82/trunk/public//admin/themes/default/plugins/modifier.clearslash.php';
if (!is_callable('smarty_modifier_default')) include '/var/www/retrincos/retrincosv82/trunk/public/libs/smarty/plugins/modifier.default.php';
?>
<?php if ($_smarty_tpl->getVariable('type_opinion')->value=='0'){?>
    <table class="adminheading">
	    <tr>
		    <th nowrap>Articulos de Opini&oacute;n</th> 
		    <th> Seleccione autor:
		    <select name="autores" id="autores" class="" onChange='changeList(this.options[this.selectedIndex].value);'>
		  	<option value="0" <?php if ($_smarty_tpl->getVariable('author')->value=="0"){?> selected <?php }?>> Todos </option>
			<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['as']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['name'] = 'as';
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('autores')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['as']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['as']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['as']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['as']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['as']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['as']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['total']);
?>
				<option value="<?php echo $_smarty_tpl->getVariable('autores')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->pk_author;?>
" <?php if ($_smarty_tpl->getVariable('author')->value==$_smarty_tpl->getVariable('autores')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->pk_author){?> selected <?php }?>><?php echo $_smarty_tpl->getVariable('autores')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->name;?>
</option>
		    <?php endfor; endif; ?>
	    </select>	
		    </th>
		    <th style="padding:10px;width:55%;"></th>
	    </tr>	
    </table>
<?php }?>
    <table class="adminlist">
	<tr>  
		<th class="title"  style="width:30px;"></th>
		<?php if ($_smarty_tpl->getVariable('type_opinion')->value=='0'){?>
                    <th class="title"  style="width:150px;">Autor</th> <?php }?>
		<th class="title">T&iacute;tulo</th>	
		<th align="center" style="width:70px;">Visto</th>		
		<th align="center" style="width:70px;">Votaci&oacute;n</th>
		<th align="center" style="width:70px;">Comentarios</th>		
		<th align="center" style="width:70px;">Fecha</th>
		<th align="center" style="width:70px;">Home</th>
		<th align="center" style="width:70px;">Publicado</th>
		<th align="center" style="width:70px;">Modificar</th>
		<th align="center" style="width:70px;">Eliminar</th>
	  </tr>	  
	 
 <tr> <td colspan='11'>
		<div id="cates" class="seccion" style="float:left;width:100%;border:1px solid gray;"> 
		<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['c']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['c']['name'] = 'c';
$_smarty_tpl->tpl_vars['smarty']->value['section']['c']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('opinions')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['c']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['c']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['c']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['c']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['c']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['c']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['c']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['c']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['c']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['c']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['c']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['c']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['c']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['c']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['c']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['c']['total']);
?>
			 <table width="100%" cellpadding=0 cellspacing=0  id="<?php echo $_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->id;?>
" border=0 <?php if ($_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->type_opinion==0){?>class="sortable"<?php }?>>
					<tr <?php echo smarty_function_cycle(array('values'=>"class=row0,class=row1"),$_smarty_tpl->smarty,$_smarty_tpl);?>
  style="cursor:pointer;" >
					    <td style="padding:4px;font-size: 11px;width:30px;">
						    <input type="checkbox" class="minput"  id="selected_<?php echo $_smarty_tpl->getVariable('smarty')->value['section']['c']['iteration'];?>
" name="selected_fld[]" value="<?php echo $_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->id;?>
"  style="cursor:pointer;">
					    </td>
					     <?php if ($_smarty_tpl->getVariable('type_opinion')->value=='0'){?> 
					    <td style="padding:4px;font-size: 11px;width:150px;" onClick="javascript:document.getElementById('selected_<?php echo $_smarty_tpl->getVariable('smarty')->value['section']['c']['iteration'];?>
').click();">
					    	<a href="author.php?action=read&id=<?php echo $_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->fk_author;?>
"><img src="<?php echo $_smarty_tpl->getVariable('params')->value['IMAGE_DIR'];?>
author.png" border="0" alt="Publicado" alt='Editar autor' title='Editar autor'/></a>					    					    
						     <?php echo $_smarty_tpl->getVariable('names')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']];?>
 					    
					    </td>
					    <?php }?>
					      <td style="padding:4px;font-size: 11px;" onClick="javascript:document.getElementById('selected_<?php echo $_smarty_tpl->getVariable('smarty')->value['section']['c']['iteration'];?>
').click();">
						    <?php echo smarty_modifier_clearslash($_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->title);?>

					    </td>	    

					    <td style="padding:4px;font-size: 11px;width:70px;" align="center">
						    <?php echo $_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->views;?>

					    </td>
					    <td style="padding:4px;width:70px;font-size: 11px;" align="center">		
							<?php echo $_smarty_tpl->getVariable('op_rating')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']];?>
				
						</td>
					    <td style="padding:4px;width:70px;font-size: 11px;" align="center">		
							<?php echo $_smarty_tpl->getVariable('op_comment')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']];?>
				
						</td>	
					    <td style="padding:4px;width:70px;font-size: 11px;" align="center">		    
							    <?php echo $_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->created;?>

					    </td>
					    <td style="padding:4px;width:70px" align="center">
								<?php if ($_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->in_home==1){?>
								<a href="?id=<?php echo $_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->id;?>
&amp;action=inhome_status&amp;status=0&amp;page=<?php echo smarty_modifier_default($_smarty_tpl->getVariable('paginacion')->value->_currentPage,0);?>
" class="no_home" title="Sacar de portada" ></a>
								<?php }else{ ?>
									<a href="?id=<?php echo $_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->id;?>
&amp;action=inhome_status&amp;status=1&amp;page=<?php echo smarty_modifier_default($_smarty_tpl->getVariable('paginacion')->value->_currentPage,0);?>
" class="go_home" title="Meter en portada" ></a>
								<?php }?>
						</td>
					    <td style="padding:4px;font-size: 11px;width:70px;" align="center">
						    <?php if ($_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->content_status==1){?>
							    <a href="?id=<?php echo $_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->id;?>
&amp;action=change_status&amp;status=0&amp;page=<?php echo smarty_modifier_default($_smarty_tpl->getVariable('paginacion')->value->_currentPage,0);?>
" title="Publicado">
								    <img src="<?php echo $_smarty_tpl->getVariable('params')->value['IMAGE_DIR'];?>
publish_g.png" border="0" alt="Publicado" /></a>
						    <?php }else{ ?>
							    <a href="?id=<?php echo $_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->id;?>
&amp;action=change_status&amp;status=1&amp;page=<?php echo smarty_modifier_default($_smarty_tpl->getVariable('paginacion')->value->_currentPage,0);?>
" title="Pendiente">
								    <img src="<?php echo $_smarty_tpl->getVariable('params')->value['IMAGE_DIR'];?>
publish_r.png" border="0" alt="Pendiente" /></a>
						    <?php }?>
					    </td>
					    <td style="padding:4px;font-size: 11px;width:70px;" align="center">
						    <a href="#" onClick="javascript:enviar(this, '_self', 'read', '<?php echo $_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->id;?>
');" title="Modificar">
							    <img src="<?php echo $_smarty_tpl->getVariable('params')->value['IMAGE_DIR'];?>
edit.png" border="0" /></a>
					    </td>
					    <td style="padding:4px;font-size: 11px;width:70px;" align="center">
						    <a href="#" onClick="javascript:delete_opinion('<?php echo $_smarty_tpl->getVariable('opinions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->id;?>
',<?php echo smarty_modifier_default($_smarty_tpl->getVariable('paginacion')->value->_currentPage,0);?>
);" title="Eliminar">
							    <img src="<?php echo $_smarty_tpl->getVariable('params')->value['IMAGE_DIR'];?>
trash.png" border="0" /></a>
					    </td>
					</tr>
			</table>		 
		<?php endfor; endif; ?>
		</div>
		
		</td>
	</tr>		
	
	<?php if (count($_smarty_tpl->getVariable('opinions')->value)>0){?>
	  <tr>
	      <td colspan='11' align="center"><?php echo $_smarty_tpl->getVariable('paginacion')->value;?>
</td>
	  </tr>
	<?php }?>
   </table>


<?php if ($_smarty_tpl->getVariable('type_opinion')->value=='0'){?>
    <table class="adminheading">
	    <tr>
		    <th nowrap>Articulos de Opini&oacute;n</th> 
		    <th> Seleccione autor:
		    <select name="autores" id="autores" class="" onChange='changeList(this.options[this.selectedIndex].value);'>
		  	<option value="0" <?php if ($_smarty_tpl->getVariable('author')->value=="0"){?> selected <?php }?>> Todos </option>
			<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['as']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['name'] = 'as';
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('autores')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['as']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['as']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['as']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['as']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['as']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['as']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['as']['total']);
?>
				<option value="<?php echo $_smarty_tpl->getVariable('autores')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->pk_author;?>
" <?php if ($_smarty_tpl->getVariable('author')->value==$_smarty_tpl->getVariable('autores')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->pk_author){?> selected <?php }?>><?php echo $_smarty_tpl->getVariable('autores')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->name;?>
</option>
		    <?php endfor; endif; ?>
	    </select>	
		    </th>
		    <th  style="padding:10px;width:55%;"></th>
	    </tr>	
    </table>
<?php }?>
