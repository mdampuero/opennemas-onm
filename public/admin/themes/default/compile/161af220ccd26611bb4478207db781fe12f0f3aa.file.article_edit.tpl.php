<?php /* Smarty version Smarty3-RC3, created on 2010-11-25 18:14:39
         compiled from "/var/www/retrincos/retrincosv82/trunk/public//admin/themes/default/tpl/article_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17001278044cee997f8705b6-29339908%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '161af220ccd26611bb4478207db781fe12f0f3aa' => 
    array (
      0 => '/var/www/retrincos/retrincosv82/trunk/public//admin/themes/default/tpl/article_edit.tpl',
      1 => 1290705269,
    ),
  ),
  'nocache_hash' => '17001278044cee997f8705b6-29339908',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_clearslash')) include '/var/www/retrincos/retrincosv82/trunk/public//admin/themes/default/plugins/modifier.clearslash.php';
if (!is_callable('smarty_modifier_escape')) include '/var/www/retrincos/retrincosv82/trunk/public/libs/smarty/plugins/modifier.escape.php';
if (!is_callable('smarty_modifier_upper')) include '/var/www/retrincos/retrincosv82/trunk/public/libs/smarty/plugins/modifier.upper.php';
if (!is_callable('smarty_modifier_date_format')) include '/var/www/retrincos/retrincosv82/trunk/public/libs/smarty/plugins/modifier.date_format.php';
if (!is_callable('smarty_modifier_truncate')) include '/var/www/retrincos/retrincosv82/trunk/public/libs/smarty/plugins/modifier.truncate.php';
?>
<div id="warnings-validation">
<?php if ($_smarty_tpl->getVariable('article')->value->isClone()){?>
    <?php $_smarty_tpl->tpl_vars["original"] = new Smarty_variable($_smarty_tpl->getVariable('article')->value->getOriginal(), null, null);?>
    
    Este artículo fue <strong>clonado</strong>. <br /> Para editar contenidos propios del artículo ir al&nbsp; <a href="article.php?action=read&id=<?php echo $_smarty_tpl->getVariable('original')->value->id;?>
">artículo original</a>.
<?php }?>
</div>

<ul id="tabs">
    <li>
        <a href="#edicion-contenido">Edici&oacute;n noticia</a>
    </li>    
    <li>
        <a href="#edicion-extra" onClick="javascript:get_tags($('title').value);">Par&aacute;metros de noticia</a>
    </li>
    <?php if (!$_smarty_tpl->getVariable('article')->value->isClone()){?>
    <li>
        <a href="#comments">Comentarios</a>
    </li>
    <?php }?>
    <li>
        <a href="#contenidos-relacionados">Contenidos relacionados</a>
    </li>
    <li>
        <a href="#elementos-relacionados" onClick="mover();">Organizar relacionados</a>
    </li>
    <?php if (isset($_smarty_tpl->getVariable('clones')->value)){?>
    <li>
        <a href="#clones">Clones</a>
    </li>
    <?php }?>
</ul>


<div class="panel" id="edicion-contenido" style="width:98%">
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="96%">
<tbody>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="10%">
        <label for="title">T&iacute;tulo:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" valign="top" width="50%">
        <input type="text" id="title" name="title" title="Título de la noticia" tabindex="1"
            value="<?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('article')->value->title),"html");?>
" class="required" size="80" maxlength="105" onChange="countWords(this,document.getElementById('counter_title'));  search_related('<?php echo $_smarty_tpl->getVariable('article')->value->pk_article;?>
',$('metadata').value);"  onkeyup="countWords(this,document.getElementById('counter_title'))"  onkeyup="countWords(this,document.getElementById('counter_title'))"/>
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="10%">
        <label for="title">T&iacute;tulo Interior:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" valign="top" width="50%">
        <input type="text" id="title_int" name="title_int" title="Título de la noticia interior"
            value="<?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('article')->value->title_int),"html");?>
" class="required" size="80" maxlength="105" onChange="countWords(this,document.getElementById('counter_title_int'));get_tags(this.value);" onkeyup="countWords(this,document.getElementById('counter_title_int'))" tabindex="1"/>
        
        
        <script type="text/javascript">
        /* <![CDATA[ */
        $('title').observe('blur', function(evt) {
            var tituloInt = $('title_int').value.strip();
            if( tituloInt.length == 0 ) {
                $('title_int').value = $F('title');
                get_tags($('title_int').value);
            }
        });
        /* ]]> */
        </script>
        
    </td>
    <td valign="top" align="right" style="padding:4px;" rowspan="5" width="40%">
         <div class="utilities-conf">
           <table style="width:99%;">
           <tr>
                <td valign="top"  align="right" nowrap="nowrap">
                <label for="category">Secci&oacute;n:</label>
                </td>
                <td nowrap="nowrap" style="text-align:left;">
                   <select name="category" id="category" class="validate-section" onChange="get_tags($('title').value);" tabindex="6">
                        <option value="20" <?php if ($_smarty_tpl->getVariable('category')->value==$_smarty_tpl->getVariable('allcategorys')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->pk_content_category){?>selected="selected"<?php }?> name="<?php echo $_smarty_tpl->getVariable('allcategorys')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->title;?>
" >UNKNOWN</option>
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
" <?php if ($_smarty_tpl->getVariable('article')->value->category==$_smarty_tpl->getVariable('allcategorys')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->pk_content_category){?>selected<?php }?> name="<?php echo $_smarty_tpl->getVariable('allcategorys')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->title;?>
"><?php echo $_smarty_tpl->getVariable('allcategorys')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']]->title;?>
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
                                <?php if ($_smarty_tpl->getVariable('subcat')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['su']['index']]->internal_category==1){?>
                                    <option value="<?php echo $_smarty_tpl->getVariable('subcat')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['su']['index']]->pk_content_category;?>
" <?php if ($_smarty_tpl->getVariable('article')->value->category==$_smarty_tpl->getVariable('subcat')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['su']['index']]->pk_content_category){?> selected<?php }?> name="<?php echo $_smarty_tpl->getVariable('subcat')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['su']['index']]->title;?>
">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $_smarty_tpl->getVariable('subcat')->value[$_smarty_tpl->getVariable('smarty')->value['section']['as']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['su']['index']]->title;?>
</option>
                               
                               <?php }?>
                            <?php endfor; endif; ?>
                        <?php endfor; endif; ?>
                    </select>
                </td>
            </tr>
            <?php if ($_SESSION['desde']!='list_hemeroteca'){?>
            <tr>
                <td valign="top"  align="right" nowrap="nowrap">
                    <label for="with_comment"> Comentarios: </label>
                </td>
                <td nowrap="nowrap" style="text-align:left;vertical-align:top">
                    <select name="with_comment" id="with_comment" class="required" tabindex="7">
                        <option value="0" <?php if ($_smarty_tpl->getVariable('article')->value->with_comment==0){?>selected="selected"<?php }?>>No</option>
                        <option value="1" <?php if ($_smarty_tpl->getVariable('article')->value->with_comment==1){?>selected="selected"<?php }?>>Si</option>
                    </select>
                </td>
            </tr>
            <tr>
                    <td valign="top"  align="right" nowrap="nowrap">
                        <label for="frontpage"> En portada Sección: </label>
                    </td>
                    <td nowrap="nowrap" style="text-align:left;vertical-align:top">
                        <select name="in_home" id="in_home" class="required" tabindex="8">
                            <option value="0" <?php if ($_smarty_tpl->getVariable('article')->value->in_home==0){?>selected="selected"<?php }?>>No</option>
                            <option value="1" <?php if ($_smarty_tpl->getVariable('article')->value->in_home==1){?>selected="selected"<?php }?>>Si</option>
                       </select>
                    </td>
               </tr>
                <tr>
                    <td valign="top"  align="right" nowrap="nowrap">
                        <label for="frontpage"> En Home: </label>
                    </td>
                    <td nowrap="nowrap" style="text-align:left;vertical-align:top">
                        <select name="frontpage" id="frontpage" class="required" tabindex="8">
                            <option value="0" <?php if ($_smarty_tpl->getVariable('article')->value->frontpage==0){?>selected="selected"<?php }?>>No</option>
                            <option value="1" <?php if ($_smarty_tpl->getVariable('article')->value->frontpage==1){?>selected="selected"<?php }?>>Si</option>
                       </select>
                    </td>
            </tr>
            <tr>
                    <td valign="top"  align="right" nowrap="nowrap">
                        <label for="available"> Disponible: </label>
                    </td>
                        <td  style="text-align:left;vertical-align:top" nowrap="nowrap">
                        <select name="available" id="available" class="required" tabindex="9">
                            <option value="1" <?php if ($_smarty_tpl->getVariable('article')->value->available==1){?> selected <?php }?>>Si</option>
                            <option value="0" <?php if ($_smarty_tpl->getVariable('article')->value->available==0){?> selected <?php }?>>No</option>
                       </select>
                        <img class="inhome" src="<?php echo $_smarty_tpl->getVariable('params')->value['IMAGE_DIR'];?>
available.png" border="0" alt="Disponible" align="top" />
                </td>
            </tr>
                                    
                                                
            <?php }else{ ?>             
            <tr>
                <td valign="top"  align="right" nowrap="nowrap">
                    <label for="with_comment"> Archivado: </label>
                </td>
                <td nowrap="nowrap" style="text-align:left;vertical-align:top">
                    <select name="content_status" id="content_status" class="required">
                        <option value="0" <?php if ($_smarty_tpl->getVariable('article')->value->content_status==0){?>selected="selected"<?php }?>>Si</option>
                        <option value="1" <?php if ($_smarty_tpl->getVariable('article')->value->content_status==1){?>selected="selected"<?php }?>>No</option>
                    </select>                    
                    <input type="hidden" id="columns" name="columns"  value="<?php echo $_smarty_tpl->getVariable('article')->value->columns;?>
" />
                    <input type="hidden" id="home_columns" name="home_columns"  value="<?php echo $_smarty_tpl->getVariable('article')->value->home_columns;?>
" />
                    <input type="hidden" id="with_comment" name="with_comment"  value="<?php echo $_smarty_tpl->getVariable('article')->value->with_comment;?>
" />
                    <input type="hidden" id="available" name="available"  value="<?php echo $_smarty_tpl->getVariable('article')->value->available;?>
" />
                    <input type="hidden" id="in_home" name="in_home"  value="<?php echo $_smarty_tpl->getVariable('article')->value->in_home;?>
" />                    
                </td>
            </tr>
                
     
            <?php }?> 
            <tr>
                <td valign="top" align="right">
                    <label for="counter_title">T&iacute;tulo:</label>
                </td>
                <td nowrap="nowrap"  style="text-align:left;vertical-align:top">
                    <input type="text" id="counter_title" name="counter_title" title="counter_title" tabindex="-1"
                        value="0" class="required" size="5" onkeyup="countWords(document.getElementById('title'),this)"/>
                </td>
            </tr>
            <tr>
                <td valign="top" align="right">
                    <label for="counter_title">T&iacute;tulo Interior:</label>
                </td>
                <td nowrap="nowrap" style="text-align:left;vertical-align:top;" >
                    <input type="text" id="counter_title_int" name="counter_title_int" title="counter_title_int"
                        value="0" class="required" size="5" onkeyup="countWords(document.getElementById('title_int'),this)" tabindex="-1"/>
                </td>
            </tr>
            <tr>
                <td valign="top" align="right">
                    <label for="counter_subtitle">Antet&iacute;tulo:</label>
                </td>
                <td nowrap="nowrap" style="text-align:left;vertical-align:top" >
                    <input type="text" id="counter_subtitle" name="counter_subtitle" title="counter_subtitle" tabindex="-1"
                        value="0" class="required" size="5" onkeyup="countWords(document.getElementById('subtitle'),this)"/>
                </td>
            </tr>
            <tr>
                <td valign="top" align="right">
                    <label for="counter_summary">Entradilla:</label>
                </td>
                <td nowrap="nowrap"  style="text-align:left;vertical-align:top">
                    <input type="text" id="counter_summary" name="counter_summary" title="counter_summary" tabindex="-1"
                        value="0" class="required" size="5" onkeyup="countWords(document.getElementById('summary'),this)"/>
                </td>
            </tr>
            <tr>
                <td valign="top" align="right">
                    <label for="counter_body">Cuerpo:</label>
                </td>
                <td nowrap="nowrap"  style="text-align:left;vertical-align:top">
                    <input type="text" id="counter_body" name="counter_body" title="counter_body" tabindex="-1"
                        value="0" class="required" size="5"  onkeyup="counttiny(document.getElementById('counter_body'));"/>
                </td>
            </tr>
            </table>
         </div>
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="30%">
        <label for="metadata">Palabras clave: </label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" width="70%">
        <input type="text" id="metadata" name="metadata" size="70" title="Metadatos" value="<?php echo $_smarty_tpl->getVariable('article')->value->metadata;?>
" onChange="search_related('<?php echo $_smarty_tpl->getVariable('article')->value->pk_article;?>
',$('metadata').value);" tabindex="1" />
        <sub>Separadas por comas</sub>
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label for="subtitle">Antet&iacute;tulo:</label>
    </td>
    <td style="padding:4px;" valign="top" nowrap="nowrap">
        <input type="text" id="subtitle" name="subtitle" title="antetítulo" tabindex="2"
            value="<?php echo smarty_modifier_escape(smarty_modifier_clearslash(smarty_modifier_upper($_smarty_tpl->getVariable('article')->value->subtitle)),"html");?>
" class="required" size="100" onChange="countWords(this,document.getElementById('counter_subtitle'))" onkeyup="countWords(this,document.getElementById('counter_subtitle'))" />
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label for="agency">Agencia:</label>
    </td>
    <td style="padding:4px;" valign="top" nowrap="nowrap" >
        <input type="text" id="agency" name="agency" title="Agencia" tabindex="3"
            value="<?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('article')->value->agency),"html");?>
" class="required" size="100" 
   onblur="setTimeout(function(){tinyMCE.get('summary').focus();}, 200);" />
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label for="summary">Entradilla:</label><br />
        <?php if (!$_smarty_tpl->getVariable('article')->value->isClone()){?>
        <a href="#" onclick="OpenNeMas.tinyMceFunctions.toggle('summary');return false;" title="Habilitar/Deshabilitar editor">
                <img src="<?php echo $_smarty_tpl->getVariable('params')->value['IMAGE_DIR'];?>
/users_edit.png" alt="" border="0" /></a>
        <?php }?>
    </td>
    <td style="padding:4px;" valign="top" nowrap="nowrap">
        <textarea name="summary" id="summary" tabindex="4"
            title="Resumen de la noticia" style="width:100%; height:8em;"  onChange="countWords(this,document.getElementById('counter_summary'))" onkeyup="countWords(this,document.getElementById('counter_summary'))"><?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('article')->value->summary),"html");?>
</textarea>
    </td>    
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;" >
        <label for="body">Cuerpo:</label>
    </td>
    <td style="padding-bottom: 5px; padding-top: 10px;" valign="top" nowrap="nowrap" colspan='2'>
        <textarea name="body" id="body" tabindex="5"
            title="Cuerpo de la noticia" style="width:100%; height:20em;" onChange="counttiny(document.getElementById('counter_body'));"> <?php echo smarty_modifier_clearslash($_smarty_tpl->getVariable('article')->value->body);?>
</textarea>
    </td>
</tr>
    
<tr><td></td>
    <td valign="top" align="center" colspan=2 >
        <div id="article_images" style="display:inline;">
            <?php $_template = new Smarty_Internal_Template("article_images_edit.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
        </div>
        
    </td>    
</tr>    
<tr>
    <td> </td>
    <td valign="top" align="left" colspan="2">&nbsp;</td>
</tr>
</tbody>
</table>


<script type="text/javascript">
    countWords(document.getElementById('title'), document.getElementById('counter_title'));
    countWords(document.getElementById('subtitle'), document.getElementById('counter_subtitle'));
    countWords(document.getElementById('summary'), document.getElementById('counter_summary'));
    countWords(document.getElementById('body'), document.getElementById('counter_body'));
</script>

</div>


<div class="panel" id="edicion-extra" style="width:95%">
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="600">
<tbody>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="30%">
        <label for="starttime">Fecha inicio publicaci&oacute;n:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" width="70%">
        <div style="width:170px;">
            <input type="text" id="starttime" name="starttime" size="18" title="Fecha inicio publicaci&oacute;n"
                value="<?php echo $_smarty_tpl->getVariable('article')->value->starttime;?>
" tabindex="-1"/></div>
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="30%">
        <label for="endtime">Fecha fin publicaci&oacute;n:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" width="70%">
        <div style="width:170px;">
            <input type="text" id="endtime" name="endtime" size="18" title="Fecha fin publicaci&oacute;n"
                value="<?php echo $_smarty_tpl->getVariable('article')->value->endtime;?>
" tabindex="-1"/></div>
        <sub>Hora del servidor: <?php echo smarty_modifier_date_format(time(),"%Y-%m-%d %H:%M:%S");?>
</sub>
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="30%">
        <label for="permalink">Permalink:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" width="70%">
        <input type="text" id="p" readonly="readonly" size="100"
            title="permalink la noticia" value="<?php echo $_smarty_tpl->getVariable('article')->value->permalink;?>
" tabindex="-1" />
            </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="30%">
        <label for="description">Descripci&oacute;n:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" width="70%">
        <textarea name="description" id="description"
            title="Descripción interna de la noticia" style="width:100%; height:8em;" tabindex="-1"><?php echo smarty_modifier_clearslash($_smarty_tpl->getVariable('article')->value->description);?>
</textarea>
    </td>
</tr>

</tbody>
</table>
</div>


<div class="panel" id="comments" style="width:95%">
    <table border="0" cellpadding="0" cellspacing="4" class="fuente_cuerpo" width="99%">
    <tbody>
    <tr>                
        <th class="title" width='50%'>Comentario</th>
        <th class="title"  width='20%'>Autor</th>                    
        <th align="right">Publicar</th>        
        <th align="right">Eliminar</th>
    </tr>                
        <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['c']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['c']['name'] = 'c';
$_smarty_tpl->tpl_vars['smarty']->value['section']['c']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('comments')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
        <tr>
            <td>  <a style="cursor:pointer;font-size:14px;" onclick="new Effect.toggle($('<?php echo $_smarty_tpl->getVariable('comments')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->pk_comment;?>
'),'blind')"> <?php echo smarty_modifier_truncate($_smarty_tpl->getVariable('comments')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->body,30);?>
 </a> </td>
            <td> <?php echo $_smarty_tpl->getVariable('comments')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->author;?>
 (<?php echo $_smarty_tpl->getVariable('comments')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->ip;?>
)  <br /> <?php echo $_smarty_tpl->getVariable('comments')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->email;?>
 </td>
            <td align="right">              </td>
             <td align="right">     
                 <a href="#" onClick="javascript:confirmar(this, '<?php echo $_smarty_tpl->getVariable('comments')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->pk_comment;?>
');" title="Eliminar">
                <img src="<?php echo $_smarty_tpl->getVariable('params')->value['IMAGE_DIR'];?>
trash.png" border="0" /></a>
           </td>
        </tr>
        <tr><td>
          <div id="<?php echo $_smarty_tpl->getVariable('comments')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->pk_comment;?>
" class="<?php echo $_smarty_tpl->getVariable('comments')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->pk_comment;?>
" style="display: none;">    
           <b>Comentario:</b> (IP: <?php echo $_smarty_tpl->getVariable('comments')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->ip;?>
 - Publicado: <?php echo $_smarty_tpl->getVariable('comments')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->changed;?>
)<br /> <?php echo $_smarty_tpl->getVariable('comments')->value[$_smarty_tpl->getVariable('smarty')->value['section']['c']['index']]->body;?>

          </div>
         </td></tr>
    <?php endfor; endif; ?>    
    <td colspan="5">
         </td>
    </tbody>
    </table>
</div>


<div class="panel" id="opiniones-relacionadas" style="width:95%">
    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
    <tbody><tr>
    <td colspan="2">
     opiniones
    </td>
    </tr>
    </tbody>
    </table>
</div>


<div class="panel" id="contenidos-relacionados" style="width:95%">
    <?php $_template = new Smarty_Internal_Template("article_relacionados.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
</div>


<div class="panel" id="elementos-relacionados" style="width:95%">
    <br />
    Listado contenidos relacionados en Portada:  <br />
    <div style="position:relative;" id="scroll-container2">
        <ul id="thelist2" style="padding: 4px; background: #EEEEEE">
              <?php $_smarty_tpl->tpl_vars['cont'] = new Smarty_variable(1, null, null);?>
            <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['n']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['name'] = 'n';
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('losrel')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                <li id="<?php echo smarty_modifier_clearslash($_smarty_tpl->getVariable('losrel')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->id);?>
">
                    <table  width="99%">
                        <tr>
                            <td><?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('losrel')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->title),'html');?>
</td>
                            <td width='120'>                                 <?php $_smarty_tpl->tpl_vars["ct"] = new Smarty_variable($_smarty_tpl->getVariable('losrel')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->content_type, null, null);?>
                                <?php echo $_smarty_tpl->getVariable('content_types')->value[$_smarty_tpl->getVariable('ct')->value];?>

                            </td> 
                            <td width="120"> <?php echo smarty_modifier_clearslash($_smarty_tpl->getVariable('losrel')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->category_name);?>
 </td> 
                            <td width="120">
                                <a  href="#" onClick="javascript:del_relation('<?php echo smarty_modifier_clearslash($_smarty_tpl->getVariable('losrel')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->id);?>
','thelist2');" title="Quitar relacion">
                                    <img src="<?php echo $_smarty_tpl->getVariable('params')->value['IMAGE_DIR'];?>
trash.png" border="0" /> </a>
                            </td>
                        </tr>
                    </table>
                </li>
                <?php $_smarty_tpl->tpl_vars['cont'] = new Smarty_variable($_smarty_tpl->getVariable('cont')->value+1, null, null);?>
            <?php endfor; endif; ?>
        </ul>
    </div>
    <br />
    Listado contenidos relacionados en Interior:  <br />
    <div style="position:relative;" id="scroll-container2int">
        <ul id="thelist2int" style="padding: 4px; background: #EEEEEE">
            <?php $_smarty_tpl->tpl_vars['cont'] = new Smarty_variable(1, null, null);?>
            <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['n']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['name'] = 'n';
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('intrel')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
            <li id="<?php echo smarty_modifier_clearslash($_smarty_tpl->getVariable('intrel')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->id);?>
"">
                <table  width='99%'>
                    <tr>
                        <td><?php echo smarty_modifier_escape(smarty_modifier_clearslash($_smarty_tpl->getVariable('intrel')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->title),'html');?>
  </td> 
                        <td width='120'>                             <?php $_smarty_tpl->tpl_vars["ct"] = new Smarty_variable($_smarty_tpl->getVariable('intrel')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->content_type, null, null);?>
                            <?php echo $_smarty_tpl->getVariable('content_types')->value[$_smarty_tpl->getVariable('ct')->value];?>

                        </td> 
                        <td width='120'> <?php echo smarty_modifier_clearslash($_smarty_tpl->getVariable('intrel')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->category_name);?>
 </td> 
                        <td width='120'>
                            <a  href="#" onClick="javascript:del_relation('<?php echo smarty_modifier_clearslash($_smarty_tpl->getVariable('intrel')->value[$_smarty_tpl->getVariable('smarty')->value['section']['n']['index']]->id);?>
','thelist2int');" title="Quitar relacion">
                                <img src="<?php echo $_smarty_tpl->getVariable('params')->value['IMAGE_DIR'];?>
trash.png" border="0" /> </a> 
                        </td>
                    </tr>
                </table>  
             </li>
              <?php $_smarty_tpl->tpl_vars['cont'] = new Smarty_variable($_smarty_tpl->getVariable('cont')->value+1, null, null);?>
            <?php endfor; endif; ?>
        </ul>
    </div>
    <br /><br />
    
    <div class="p">
        <input type="hidden" id="ordenPortada" name="ordenArti" value="" size="140"></input>
        <input type="hidden" id="ordenInterior" name="ordenArtiInt" value="" size="140"></input>
    </div>
</div>

<?php if (isset($_smarty_tpl->getVariable('clones')->value)){?>
<div class="panel" id="clones" style="width:95%">
    <?php $_template = new Smarty_Internal_Template("article_clones.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
</div>
<?php }?>

<?php if ($_smarty_tpl->getVariable('article')->value->isClone()){?>

<script type="text/javascript">
(function() {
    var elements = ['title', 'description', 'subtitle', 'metadata', 'agency'];
    elements.each(function(item){
        $(item).disabled = true;
        $(item).setAttribute('disabled', 'disabled');
    });
}());
</script>

<?php }?>
