<?php /* Smarty version Smarty3-RC3, created on 2010-11-25 18:07:07
         compiled from "/var/www/retrincos/retrincosv82/trunk/public//admin/themes/default/tpl/article_images_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20648288894cee97bbe1aaf1-19003799%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0a612d96741e6fdb54aa3063fd1b7b57fb5648a2' => 
    array (
      0 => '/var/www/retrincos/retrincosv82/trunk/public//admin/themes/default/tpl/article_images_edit.tpl',
      1 => 1288947464,
    ),
  ),
  'nocache_hash' => '20648288894cee97bbe1aaf1-19003799',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_clearslash')) include '/var/www/retrincos/retrincosv82/trunk/public//admin/themes/default/plugins/modifier.clearslash.php';
if (!is_callable('smarty_modifier_escape')) include '/var/www/retrincos/retrincosv82/trunk/public/libs/smarty/plugins/modifier.escape.php';
if (!is_callable('smarty_function_cssimagescale')) include '/var/www/retrincos/retrincosv82/trunk/public//admin/themes/default/plugins/function.cssimagescale.php';
?><table align="center" cellpadding="15" cellspacing="0" border="0" width="1000">
<tr>	
	<td nowrap="nowrap" >
	  <div id="img_portada"  style="display:block;">
		  <div id="nifty" style="width:560px;display:block;">
  			<b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
		 		<?php if ($_smarty_tpl->getVariable('video1')->value->videoid){?> 	 		
						<table border='0' width="550">
						<tr><td>
							<h2 style="color:#2f6d9d;">Imagen de Portada:</h2></td>
							<input type="hidden" id="input_video" name="fk_video" value="<?php echo $_smarty_tpl->getVariable('article')->value->pk_video;?>
" size="70">				 							
							<input type="hidden" id="input_img1" name="img1" title="Imagen" value="" size="70"/>
						  <td  align='right'>  
						    <a style="cursor:pointer;" onclick="javascript:recuperar_eliminar('img1');">
	                 		    <img style="cursor:pointer;" src="themes/default/images/remove_image.png" id="remove_img1" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
	         			</td>					  
						 </tr><tr>
						 <td align='left'> 							 				 				
							  <div id="droppable_div1">	 																							
									<img src="http://i4.ytimg.com/vi/<?php echo $_smarty_tpl->getVariable('video1')->value->videoid;?>
/default.jpg" id="change1" name="<?php echo $_smarty_tpl->getVariable('video1')->value->pk_video;?>
" border="0" width="120px" />
								</div>					
					  	</td><td colspan="2" style="text-align:left;white-space:normal;" >
							    <div id="informa" style=" width:260px; overflow:auto;">
										<b>Archivo: <?php echo $_smarty_tpl->getVariable('video1')->value->videoid;?>
</b> <br><b>Fecha de creaci&oacute;n:</b> <?php echo $_smarty_tpl->getVariable('video1')->value->created;?>
<br>
										<b>Descripcion: </b><?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('video1')->value->description),'html');?>
 <br><b>Tags:</b> <?php echo $_smarty_tpl->getVariable('video1')->value->metadata;?>
<br>
								</div>		
								<div id="noimag" style="display: inline; width:380px; height:30px;"></div>																								
							<div id="noinfor" style="display: none; width:100%;  height:30px;"></div>											
			    		</td></tr></table>		
		    	<?php }else{ ?>
			    	  <?php if ($_smarty_tpl->getVariable('photo1')->value->name){?>
			    	  	<table border='0' style="width:550px;">
							<tr><td>
								<h2 style="color:#2f6d9d;">Imagen de Portada:</h2></td>
								<input type="hidden" id="input_video" name="fk_video" value="" size="70">				 
								<input type="hidden" id="input_img1" name="img1" title="Imagen" value="<?php echo $_smarty_tpl->getVariable('article')->value->img1;?>
" size="70"/>
							  <td  align='right'>  
							    <a style="cursor:pointer;" onclick="javascript:recuperar_eliminar('img1');">
		                 		    <img style="cursor:pointer;" src="themes/default/images/remove_image.png" id="remove_img1" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
		         			</td>					  
							 </tr><tr>
							 <td align='left'> 							 				 				
								  <div id="droppable_div1">	 																							
										<img src="<?php echo $_smarty_tpl->getVariable('MEDIA_IMG_PATH_WEB')->value;?>
<?php echo $_smarty_tpl->getVariable('photo1')->value->path_file;?>
<?php echo $_smarty_tpl->getVariable('photo1')->value->name;?>
" id="change1" name="<?php echo $_smarty_tpl->getVariable('article')->value->img1;?>
" border="0" width="300px" />
                                        
									</div>					
						  	</td><td colspan="2" style="text-align:left;white-space:normal;">
								    <div id="informa" style="text-align:left; width:260px;overflow:auto;">
											<b>Archivo: <?php echo $_smarty_tpl->getVariable('photo1')->value->name;?>
</b> <br><b>Dimensiones:</b> <?php echo $_smarty_tpl->getVariable('photo1')->value->width;?>
 x <?php echo $_smarty_tpl->getVariable('photo1')->value->height;?>
 (px)<br>
											<b>Peso:</b> <?php echo $_smarty_tpl->getVariable('photo1')->value->size;?>
 Kb<br><b>Fecha de creaci&oacute;n:</b> <?php echo $_smarty_tpl->getVariable('photo1')->value->created;?>
<br>
											<b>Descripcion:</b> <?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('photo1')->value->description),'html');?>
 <br><b>Tags:</b> <?php echo $_smarty_tpl->getVariable('photo1')->value->metadata;?>
<br>
									</div>		
									<div id="noimag" style="display: inline; width:380px; height:30px;"></div>																								
								<div id="noinfor" style="display: none; width:100%;  height:30px;"></div>											
				    		</td></tr></table>		
			    		<?php }else{ ?>
				    		<table border='0' width="96%">
							<tr><td>
								<h2 style="color:#2f6d9d;">Imagen de Portada:</h2></td>
							 	<input type="hidden" id="input_video" name="fk_video" value="" size="70">				 
								<input type="hidden" id="input_img1" name="img1" title="Imagen" value="" size="70"/>
							  <td  align='right'> 
							   <a style="cursor:pointer;" onclick="javascript:recuperar_eliminar('img1');">
			                 		    <img style="cursor:pointer;" src="themes/default/images/remove_image.png" id="remove_img1" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
			         			</td>
							  
							 </tr><tr>
							 <td align='left'> 					 				
								  <div id="droppable_div1">	 																							
										<img src="../media/images/default_img.jpg" id="change1" name="<?php echo $_smarty_tpl->getVariable('article')->value->img1;?>
" border="0" width="300px" />
                                        
									</div>					
						  	</td>
                            <td colspan="2" style="text-align:left;white-space:normal;">
								    <div id="informa" style="text-align:left;width:260px; overflow:auto;">
											<b>Archivo: default_img.jpg</b> <br><b>Dimensiones:</b> 300 x 208 (px)<br>
											<b>Peso:</b> 4.48 Kb<br><b>Fecha de creaci&oacute;n:</b> 11/06/2008<br>
											<b>Descripcion: </b>Imagen por defecto.<br><b>Tags:</b> Imagen<br>											
									</div>		
									<div id="noimag" style="display: inline; width:380px; height:30px;"></div>																								
								<div id="noinfor" style="display: none; width:100%;  height:30px;"></div>											
				    		</td></tr>
                        </table>
			    	<?php }?>			    
                <?php }?>
                                
				
                <div id="footer_img_portada"> <label for="title">Pie imagen Portada:</label>
					<input type="text" id="img1_footer" name="img1_footer" title="Imagen" value="<?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('article')->value->img1_footer),"html");?>
" size="50" />
				</div>
          
                <br />
	        <b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
		    </div>
		   </div>
				<input type="hidden" id="posic" name="posic" value="0" />		
	
	</td>
	<td valign="top" align="right"  rowspan="3" >
			  <div  onclick="new Effect.toggle($('photos_container'),'blind')" style="cursor:pointer;width:438px;height:22px; border:1px double #333333;background-color:#EEE; padding:4px;overflow:none;">
			 	<a style="cursor:pointer;" onclick="new Effect.toggle($('photos_container'),'blind')"><b> Fotos </b></a>
			  </div>
			  <div id="photos_container" class="photos" style="width:440px; height:570px; border:3px double #333333; padding:1px;overflow:hidden;">
				   <br>
                    <table width="90%">
                        <tr>
                            <td align="left">
                                <div class="cajaBusqueda">
                                    <input id="stringImageSearch" name="stringImageSearch" type="text"   onkeypress="onImageKeyEnter(event, $('category_imag').options[$('category_imag').selectedIndex].value,encodeURIComponent($('stringImageSearch').value),1);" onclick="this.select();" value="Busqueda Imagenes..." />
                                </div>
                            </td>
                            <td align="right">
                               <select id="category_imag" name="category_imag" class="required" onChange="get_images(this.options[this.selectedIndex].value,1, 'list_by_category',0);">
                                    <option value="0">GLOBAL</option>
                                    <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['as']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['name'] = 'as';
$_smarty_tpl->tpl_vars['smarty']->value['section']['as']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('allcategorys')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                                        <option value="<?php echo $_smarty_tpl->getVariable('allcategorys')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->pk_content_category;?>
" <?php if ($_smarty_tpl->getVariable('article')->value->category==$_smarty_tpl->getVariable('allcategorys')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->pk_content_category){?> selected <?php }?>><?php echo $_smarty_tpl->getVariable('allcategorys')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->title;?>
</option>
                                        <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['su']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['su']['name'] = 'su';
$_smarty_tpl->tpl_vars['smarty']->value['section']['su']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('subcat')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['su']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['su']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['su']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['su']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['su']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['su']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['su']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['su']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['su']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['su']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['su']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['su']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['su']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['su']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['su']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['su']['total']);
?>
                                                <option value="<?php echo $_smarty_tpl->getVariable('subcat')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['su']['index']]->pk_content_category;?>
" <?php if ($_smarty_tpl->getVariable('article')->value->category==$_smarty_tpl->getVariable('subcat')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['su']['index']]->pk_content_category){?> selected <?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $_smarty_tpl->getVariable('subcat')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['su']['index']]->title;?>
</option>
                                        <?php endfor; endif; ?>
                                    <?php endfor; endif; ?>
                                </select>
                            </td>
                        </tr>
                    </table>

									
                    <div id="photos" class="photos" style="width:440px; height:560px; border:0px double #333333; padding:1px;overflow:hidden;">
                            <?php if ($_smarty_tpl->getVariable('paginacion')->value){?>
                                    <p align="center"> <?php echo $_smarty_tpl->getVariable('paginacion')->value;?>
 </p>
                            <?php }?>
                             <ul id="thelist"  class="gallery_list" style="width:400px;">
                                <?php $_smarty_tpl->tpl_vars['num'] = new Smarty_variable('1', null, null);?>
                                    <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['n']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['name'] = 'n';
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('photos')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total']);
?>
                                    <?php if ($_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->content_status==1){?>
                                      <li><div>
                                        <a>
                                        <img style="vertical-align:middle;<?php echo smarty_function_cssimagescale(array('resolution'=>67,'photo'=>$_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]),$_smarty_tpl->smarty,$_smarty_tpl);?>
" src="<?php echo $_smarty_tpl->getVariable('MEDIA_IMG_PATH_WEB')->value;?>
<?php echo $_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->path_file;?>
140x100-<?php echo $_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->name;?>
" id="draggable_img<?php echo $_smarty_tpl->getVariable('num')->value;?>
" class="draggable" name="<?php echo $_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->pk_photo;?>
" border="0" de:mas="<?php echo $_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->name;?>
" de:ancho="<?php echo $_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->width;?>
" de:alto="<?php echo $_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->height;?>
" de:peso="<?php echo $_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->size;?>
" de:created="<?php echo $_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->created;?>
" de:description="<?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->description),'html');?>
" de:tags="<?php echo $_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->metadata;?>
"  title="Desc:<?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->description),'html');?>
 - Tags:<?php echo $_smarty_tpl->getVariable('photos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->metadata;?>
" />
                                        </a> </div></li>
                                           
                                                    <script type="text/javascript">
                                                      new Draggable('draggable_img<?php echo $_smarty_tpl->getVariable('num')->value;?>
', { revert:true, scroll: window, ghosting:true }  );
                                                    </script>
                                            
                                             <?php $_smarty_tpl->tpl_vars['num'] = new Smarty_variable($_smarty_tpl->getVariable('num')->value+1, null, null);?>
                                    <?php }?>
                                    <?php endfor; endif; ?>
                             </ul>
                    </div>
                </div>
                <div  onclick="new Effect.toggle($('videos-container'),'blind')" style="cursor:pointer;width:438px;height:22px; border:1px double #333333;background-color:#EEE;padding:4px;overflow:none;">
                        <a style="cursor:pointer;" onclick="new Effect.toggle($('videos'),'blind')"> <b>Videos</b> </a>
                 </div>

                 <div id="videos-container" class="photos" style="width:440px; height:400px; border:3px double #333333; padding:1px;overflow:hidden;">
                    <br>
                    <table width="90%"><tr><td><div class="cajaBusqueda" style="width:100%;" align="left"><input class="textoABuscar" id="stringVideoSearch" name="stringVideoSearch" type="text"  onkeypress="onVideoKeyEnter(event, $('stringVideoSearch').value,1);" onclick="this.select();" value="Busqueda Videos..." align="left"/></div></td></tr></table>
                    <br>
                    <div id="videos" class="photos" style="width:440px; height:380px; border:0px double #333333; padding:1px;overflow:auto;">
                         <?php if ($_smarty_tpl->getVariable('paginacionV')->value){?>
                            <p align="center"> <?php echo $_smarty_tpl->getVariable('paginacionV')->value;?>
 </p>
                        <?php }?>
                         <ul id='thelist'  class="gallery_list" style="width:440px;">
                            <?php $_smarty_tpl->tpl_vars['num'] = new Smarty_variable('1', null, null);?>
                            <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['n']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['name'] = 'n';
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('videos')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total']);
?>
                            <?php if ($_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->content_status==1){?>
                              <li><div style="float: left;"> <a>
                                    <?php if ($_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->author_name=='youtube'){?>
                                        <img class="video"  width="67" id="draggable_video<?php echo $_smarty_tpl->getVariable('num')->value;?>
" name="<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->pk_video;?>
" alt="<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->title;?>
" qlicon="<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->videoid;?>
" src="http://i4.ytimg.com/vi/<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->videoid;?>
/default.jpg" title="<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->title;?>
 - <?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->author_name;?>
" de:created="<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->created;?>
" de:description="<?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->description),'html');?>
" de:tags="<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->metadata;?>
"   title="Desc:<?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->description),'html');?>
 - Tags:<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->metadata;?>
" />
                                    <?php }else{ ?>
                                         <img class="video"  width="67" id="draggable_video<?php echo $_smarty_tpl->getVariable('num')->value;?>
" name="<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->pk_video;?>
" alt="<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->title;?>
" qlicon="<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->videoid;?>
" src="<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->thumbnail_medium;?>
" title="<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->title;?>
 - <?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->author_name;?>
" de:created="<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->created;?>
" de:description="<?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->description),'html');?>
" de:tags="<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->metadata;?>
"   title="Desc:<?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->description),'html');?>
 - Tags:<?php echo $_smarty_tpl->getVariable('videos')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->metadata;?>
" />
                                    <?php }?>
                                </a> </div></li>
                                
                                    <script type="text/javascript">
                                      new Draggable('draggable_video{$num}', { revert:true, scroll: window, ghosting:true }  );
                                    </script>
                                
                                 <?php $_smarty_tpl->tpl_vars['num'] = new Smarty_variable($_smarty_tpl->getVariable('num')->value+1, null, null);?>
                            <?php }?>
                            <?php endfor; endif; ?>
                         </ul>
                    </div>
				</div>				
	</td>
</tr>
	
<tr>

	<td>
	   <div id="img_interior"  style="display:block;">
	       <div id="nifty" style="width:560px;display:block;">
  			<b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>		 		
				<?php if ($_smarty_tpl->getVariable('photo2')->value->name){?> 	 
					  <table border='0' width="550">
						 <tr><td>
							<h2 style="color:#2f6d9d;">Imagen Interior:</h2></td>
						 <td align='right'> 
						 	<a onclick="javascript:recuperar_eliminar('img2');">
	                    	 <img src="themes/default/images/remove_image.png" id="remove_img2" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
	         			</td>
						 </tr><tr>
						 <td align='left'> 						  
						    <input type="hidden" id="input_img2" name="img2" value="<?php echo $_smarty_tpl->getVariable('article')->value->img2;?>
" size="100">				 
						   <div id="droppable_div2">						 																			
							 <img src="<?php echo $_smarty_tpl->getVariable('MEDIA_IMG_PATH_WEB')->value;?>
<?php echo $_smarty_tpl->getVariable('photo2')->value->path_file;?>
<?php echo $_smarty_tpl->getVariable('photo2')->value->name;?>
" id="change2" name="<?php echo $_smarty_tpl->getVariable('article')->value->img2;?>
" border="0" width="300px" />
                             
							</div>
						</td>	<td colspan="2" style="text-align:left;white-space:normal;" width="400">
							  <div id="informa2" style="text-align:left;width:260px; overflow:auto;">
										<b>Archivo: <?php echo $_smarty_tpl->getVariable('photo2')->value->name;?>
</b> <br><b>Dimensiones:</b> <?php echo $_smarty_tpl->getVariable('photo2')->value->width;?>
 x <?php echo $_smarty_tpl->getVariable('photo2')->value->height;?>
 (px)<br>
										<b>Peso:</b> <?php echo $_smarty_tpl->getVariable('photo2')->value->size;?>
 Kb<br><b>Fecha de creaci&oacute;n:</b> <?php echo $_smarty_tpl->getVariable('photo2')->value->created;?>
<br>
										<b>Descripcion: </b> <?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('photo2')->value->description),'html');?>
 <br><b>Tags:</b> <?php echo $_smarty_tpl->getVariable('photo2')->value->metadata;?>
<br>
										
								</div>
							 <div id="noimag2" style="display: inline; width:380px; height:30px;">	</div>
							 <div id="noinfor2" style="display: none; width:100%; height:30px;"></div>									
					</td></tr></table>
				<?php }else{ ?>
			    		<table border='0' width="96%">
						<tr><td>
							<h2 style="color:#2f6d9d;">Imagen Interior:</h2></td>
							<input type="hidden" id="input_img2" name="img2" title="Imagen" value="<?php echo $_smarty_tpl->getVariable('article')->value->img2;?>
" size="70"/>
						  <td  align='right'> 	
							  <a onclick="javascript:recuperar_eliminar('img2');">
		                    	 <img src="themes/default/images/remove_image.png" id="remove_img2" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>	         			
						  </td>
						  
						 </tr><tr>
						 <td align='left'> 					 				
							  <div id="droppable_div2">	 																							
									<img src="../media/images/default_img.jpg" id="change2" name="<?php echo $_smarty_tpl->getVariable('article')->value->img2;?>
" border="0" width="300px" />
                                    
								</div>					
					  	</td><td colspan="2" style="text-align:left;white-space:normal;" width="400">
							    <div id="informa2" style="text-align:left;width:260px; overflow:auto;">
										<b>Archivo: default_img.jpg</b> <br><b>Dimensiones:</b> 300 x 208 (px)<br>
										<b>Peso:</b> 4.48 Kb<br><b>Fecha de creaci&oacute;n:</b> 11/06/2008<br>
										<b>Descripcion:</b>  Imagen por defecto.  <br><b>Tags:</b> Imagen<br>
										
								</div>		
								<div id="noimag2" style="display: inline; width:380px; height:30px;"></div>																								
							<div id="noinfor2" style="display: none; width:100%;  height:30px;"></div>											
			    		</td></tr></table>
		    	<?php }?>			
					
				<div id="footer_img_interior"> <label for="title">Pie imagen Interior:</label>
					<input type="text" id="img2_footer" name="img2_footer" title="Imagen" value="<?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('article')->value->img2_footer),"html");?>
" size="50" />
				   </div> <br>
				<b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
		    </div>
		   </div>
		   <br>
	</td>
</tr>

<tr>

	<td>
	   <div id="video_interior"  style="display:block;">
	       <div id="nifty" style="width:560px;display:block;">
  			<b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>		 		
				 <?php if ($_smarty_tpl->getVariable('video2')->value->videoid){?> 	 		
						<table border='0' width="550"">
						<tr><td>
							<h2 style="color:#2f6d9d;">Video Interior:</h2></td>
							<input type="hidden" id="input_video2" name="fk_video2" title="Imagen" value="<?php echo $_smarty_tpl->getVariable('article')->value->fk_video2;?>
" size="70"/>
						  <td  align='right'>  
						    <a style="cursor:pointer;" onclick="javascript:recuperar_eliminar('video2');">
	                 		    <img style="cursor:pointer;" src="themes/default/images/remove_image.png" id="remove_video2" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
	         			</td>					  
						 </tr><tr>
						 <td align='left'> 							 				 				
							  <div id="droppable_div3">	 																							
									<img src="http://i4.ytimg.com/vi/<?php echo $_smarty_tpl->getVariable('video2')->value->videoid;?>
/default.jpg"  id="change3" name="<?php echo $_smarty_tpl->getVariable('article')->value->fk_video2;?>
" border="0" width="120px" />
                                    
								</div>					
					  	</td><td colspan="2" style="text-align:left;white-space:normal;">
							    <div id="informa3" style="text-align:left;width:260px; overflow:auto;">
										<b>Codigo: <?php echo $_smarty_tpl->getVariable('video2')->value->videoid;?>
</b> 
										<br><b>Fecha de creaci&oacute;n:</b> <?php echo $_smarty_tpl->getVariable('video2')->value->created;?>
<br>
										<b>Descripcion: </b> <?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('video2')->value->description),'html');?>
 <br><b>Tags:</b> <?php echo $_smarty_tpl->getVariable('video2')->value->metadata;?>
<br>
								</div>		
								<div id="noimag3" style="display: inline; width:380px; height:30px;"></div>																								
							<div id="noinfor3" style="display: none; width:100%;  height:30px;"></div>											
			    		</td></tr></table>		
		    	<?php }else{ ?>
				  <table border='0' width="96%">
					 <tr><td>
					<h2 style="color:#2f6d9d;">Video Interior:</h2></td>
					 <td  align='right'>   
					 	<a style="cursor:pointer;"  onclick="javascript:recuperar_eliminar('video2');">
                                            <img src="themes/default/images/remove_image.png" id="remove_video2" alt="Eliminar" title="Eliminar" border="0" align="absmiddle" /> </a>
                        
         			 </td>
					 </tr><tr>
					 <td align='left'> 
					  
					    <input type="hidden" id="input_video2" name="fk_video2" value="" size="70">				 
					   <div id="droppable_div3">						 																			
						 <img src="../media/images/default_img.jpg" id="change3" name="default_img" border="0" width="300px" />						
						</div>
					</td>	<td colspan="2" style="text-align:left;white-space:normal;">
						 <div id="informa3"  style="text-align:left;overflow:auto;width:260px;>
							   <b>Archivo: default_img.jpg</b> <br> 
							   <b>Peso:</b> 4.48 Kb<br><b>Fecha de creaci&oacute;n:</b> 11/06/2008<br>
							   <b>Descripcion: </b> Imagen por defecto. <br><b>Tags:</b> imagen<br>
						 </div>
						 <div id="noimag3" style="display: inline; width:380px; height:30px;">	</div>
						 <div id="noinfor3" style="display: none; width:100%; height:30px;"></div>									
					</td></tr></table>
						
				<?php }?>	
				<div id="video2_footer"> <label for="title">Pie Video Interior:</label>
					<input type="text" id="footer_video2" name="footer_video2" title="video interior footer" value="<?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('article')->value->footer_video2),"html");?>
" size="50" />
				   </div> <br>
				<b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
		    </div>
		    <br>
		   </div>
		   <br>
	</td>

</tr>
</table>

<?php if (!$_smarty_tpl->getVariable('article')->value->isClone()){?>

<script type="text/javascript">
  Droppables.add('droppable_div1', { 
    accept: ['draggable', 'video'], 
    hoverclass: 'hover',
    onDrop: function(element) { 		   	
    			recuperarOpacity('img1');	

                                var source=element.src;
		   		if($('change1')){
                                    if(element.getAttribute('class')=='draggable'){
                                        $('change1').src = source.replace( '140x100-','');
                                    }else{
                                        $('change1').src = source;
                                    }
                                    $('change1').name=element.name;
                                    var ancho=element.getAttribute('de:ancho');
                                    if(element.getAttribute('de:ancho')>300) {ancho=300;}
                                    $('change1').setAttribute('width', ancho);
		  		}
		   		
		   		$('informa').innerHTML=' ';
		   		if(element.getAttribute('class')=='draggable'){
		   		    $('input_img1').value=element.name;
		   		     $('input_video').value='';
			   		$('informa').innerHTML="<b>Archivo: </b>"+element.getAttribute('de:mas') + "<br><b>Dimensiones: </b>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><b>Peso: </b>" + element.getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created') + "<br><b>Descripcion: </b>" + element.getAttribute('de:description') + "<br><b>Tags: </b>" + element.getAttribute('de:tags');
			   		$('img1_footer').value= element.getAttribute('de:description'); 
		   		}else{
		   		    $('input_video').value=element.name;
		   		    $('input_img1').value='';
		   			$('informa').innerHTML="<b>Codigo: </b>"+element.getAttribute('title')  + "<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created') + "<br><b>Descripcion: </b>" + element.getAttribute('de:description') + "<br><b>Tags: </b>" + element.getAttribute('de:tags');
		   			$('img1_footer').value= element.getAttribute('de:description'); 
		   		}
                
                // En firefox 2, precísase reescalar o div co alto da imaxe
                if( /Firefox\/2/.test(navigator.userAgent) ) {
                    $('droppable_div1').style.height = $('change1').height + 'px';
                }
		     }
  });
  
  Droppables.add('droppable_div2', { 
    accept: 'draggable',
    hoverclass: 'hover',
    onDrop: function(element) { 		   
    			recuperarOpacity('img2');		
		   		var source2=element.src;
		   		//if($('change2')){ $('change2').src = source2;
                                if($('change2')){
                                    $('change2').src = source2.replace( '140x100-','');
                                    $('change2').name=element.name;
                                    var ancho=element.getAttribute('de:ancho');
                                    if(element.getAttribute('de:ancho')>300) {ancho=300;}
                                    $('change2').setAttribute('width',ancho);
	  			}
		   		$('input_img2').value=element.name;
		   		$('informa2').innerHTML=' ';	
		   		$('informa2').innerHTML="<b>Archivo: </b>"+element.getAttribute('de:mas') + "<br><b>Dimensiones: </b>"+element.getAttribute('de:ancho') + " x " +element.getAttribute('de:alto') + " (px)<br><b>Peso: </b>" + element.getAttribute('de:peso') + "Kb<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created') + "<br><b>Descripcion: </b>" + element.getAttribute('de:description') +"<br><b>Tags: </b>" + element.getAttribute('de:tags');
		   		$('img2_footer').value= element.getAttribute('de:description');
                
                // En firefox 2, precísase reescalar o div co alto da imaxe
                if( /Firefox\/2/.test(navigator.userAgent) ) {
                    $('droppable_div2').style.height = $('change2').height + 'px';
                }
		     }
  });
  
   
  Droppables.add('droppable_div3', { 
    accept: 'video',
    onDrop: function(element) { 		 
    			recuperarOpacity('video2'); 
		   		var source3=element.src;                                
		   		if($('change3')){
                                    $('change3').src = source3;
                                    $('change3').name=element.name;
                                    $('change3').setAttribute('width',150);
		   		}
		   		$('input_video2').value=element.name;
		   		$('informa3').innerHTML=' ';	
		   		$('informa3').innerHTML="<b>Codigo: </b>"+element.getAttribute('title')  +  "<br><b>Fecha Creaci&oacute;n: </b>" + element.getAttribute('de:created') + "<br><b>Descripcion: </b>" + element.getAttribute('de:description') + "<br><b>Tags: </b>" + element.getAttribute('de:tags');
		   		$('footer_video2').value= element.getAttribute('de:description'); 
		     }
  });
  

</script>

<?php }?>