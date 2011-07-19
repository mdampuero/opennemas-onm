{extends file="base/admin.tpl"}

{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsalbum.js"></script>

{/block}

{block name="footer-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}cropper.js"></script>
     <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsGallery.js"></script>
{/block}

{block name="content"}

<div class="wrapper-content">
    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

    {* LISTADO ******************************************************************* *}
    {if !isset($smarty.request.action) || $smarty.request.action eq "list"}

        <ul class="tabs2" style="margin-bottom: 28px;">
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=favorite" {if $category=='favorite'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {else}{if $ca eq $datos_cat[0]->fk_content_category}style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}{/if} >WIDGET HOME</a>
            </li>
            
           {include file="menu_categorys.tpl" home=$smarty.server.SCRIPT_NAME|cat:"?action=list"}
        </ul>

        <div id="menu-acciones-admin" class="clearfix">
            <div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
            <ul>
                {acl isAllowed="ALBUM_DELETE"}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
                        <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar" ><br />Eliminar
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ALBUM_AVAILABLE"}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
                        <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ALBUM_AVAILABLE"}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
                        <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Publicar" alt="Publicar" ><br />Publicar
                    </a>
                </li>
                {/acl}
                <li>
                    <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                        <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todos" status="0">
                    </button>
                </li>
                {acl isAllowed="ALBUM_CREATE"}
                <li>
                    <a href="#" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>uevo Album');" accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}/album.png" title="Nuevo Album" alt="Nuevo Album"><br />Nuevo Album
                    </a>
                </li>
                {/acl}
            </ul>
        </div>
        <br>
        <div id="messageBoard"></div>

        {if (!empty($msg) || !empty($msgdel) || !empty($errors) )}
            <script type="text/javascript">
                showMsgContainer({ 'warn':['  {$msg} , {$msgdel}, {$errors} '] },'inline','messageBoard');
            </script>
        {/if}
          
        <div id="{$category}">
            <table class="adminheading">
                <tr>
                    <th nowrap>{t}Albums{/t}</th>
                </tr>
            </table> 
            <table class="adminlist">
                <tr>
                    <th class="title" style="width:35px;"></th>
                    <th>{t}Title{/t}</th>

                    <th>{t}Created{/t}</th>
                    <th align="center" style="width:35px;">{t}Views{/t}</th>
                    {if $category=='favorite'}<th align="center">{t}Section{/t}</th> {/if}
                    <th align="center">{t}Published{/t}</th>
                    <th align="center" style="width:35px;">{t}Favorite{/t}</th>
                    <th align="center" style="width:35px;">{t}Edit{/t}</th>
                    <th align="center" style="width:35px;">{t}Delete{/t}</th>
                </tr>

                {section name=as loop=$albums}
                    <tr {cycle values="class=row0,class=row1"}>
                        <td align="center">
                                <input type="checkbox" class="minput"  id="selected_{$smarty.section.as.iteration}" name="selected_fld[]" value="{$albums[as]->id}"  style="cursor:pointer;" >
                        </td>
                        <td>
                                <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$albums[as]->pk_album}');" title="{$albums[as]->title|clearslash}">
                                 {$albums[as]->title|clearslash}</a>
                        </td>
                        <td align="center">
                                 {$albums[as]->created}
                        </td>
                         <td align="center">
                                 {$albums[as]->views}
                        </td>
                        {if $category=='favorite'}                            
                                <td align="center">
                                     {$albums[as]->category_title}
                                </td>
                        {/if}
                        <td align="center">
                            {acl isAllowed="ALBUM_AVAILABLE"}
                                {if $albums[as]->available == 1}
                                        <a href="?id={$albums[as]->pk_album}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}" title={t}"Published"{/t}>
                                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt={t}"Published"{/t} /></a>
                                {else}
                                        <a href="?id={$albums[as]->pk_album}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title={t}"Pending{/t}>
                                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt={t}"Pending{/t}/></a>
                                {/if}
                            {/acl}
                        </td>

                        <td align="center">
                            {acl isAllowed="ALBUM_FAVORITE"}
                                {if $albums[as]->favorite == 1}
                                   <a href="?id={$albums[as]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_on" title={t}"Take out from frontpage"{/t}></a>
                                {else}
                                    <a href="?id={$albums[as]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_off" title={t}"Put in frontpage"{/t}></a>
                                {/if}
                            {/acl}
                        </td>
                        <td align="center">
                            {acl isAllowed="ALBUM_UPDATE"}
                                <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$albums[as]->pk_album}');" title={t}"Edit"{/t}>
                                        <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                            {/acl}
                        </td>
                        <td align="center">
                            {acl isAllowed="ALBUM_DELETE"}
                                <a href="#" onClick="javascript:delete_album('{$albums[as]->pk_album}','{$paginacion->_currentPage}');" title={t}Delete{/t}>
                                        <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                            {/acl}
                        </td>

                </tr>
                {sectionelse}
                <tr>
                        <td align="center" colspan=5><br><br><h2><b>{t}No album saved{/t} </b></h2><br><br></td>
                </tr>
            {/section}
            {if !empty($pagination)}
                <tr>
                  <td colspan="9"><br><br>{$paginacion->links}<br><br></td>
                </tr>
            {/if}
            </table>
        </div>
 
    {/if}

    {* FORMULARIO PARA ENGADIR UN CONTENIDO ALBUM ***************************************}

    {if isset($smarty.request.action) && ($smarty.request.action eq "new" || $smarty.request.action eq "read")}

       <div id="menu-acciones-admin" class="clearfix">
		<ul>
			<li>
				<a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', 0);" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
				</a>
			</li>
		    <li>
                {acl isAllowed="ALBUM_CREATE"}
				<a class="admin_add" onClick="album_get_order(); if(check_crop()) enviar(this, '_self', 'validate', '{$album->id}');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
                {/acl}
		    </li>
			<li>
                {if isset($album->id)}
                    {acl isAllowed="ALBUM_UPDATE"}
                        <a onClick="javascript:album_get_order(); if(check_crop()) enviar(this, '_self', 'update', '{$album->id}');">
                    {/acl}
                {else}
                    {acl isAllowed="ALBUM_CREATE"}
                        <a onClick="javascript: album_get_order(); if(check_crop()) enviar(this, '_self', 'create', '0');">
                    {/acl}
                {/if}
                    <img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar                    
            </a>
			</li>
		</ul>
	</div>

        <ul id="tabs">
            <li>
                    <a href="#edicion-contenido">{t}Enter album information{/t}</a>
            </li>
        </ul>
       
        <div class="panel" id="edicion-contenido" style="width:100%">
            <table border="0" cellpadding="2" cellspacing="2" class="fuente_cuerpo" >
                <tbody>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="title">{t}Title:{/t}</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <input type="text" id="title" name="title" title={t}"Album"{/t}
                                size="80" value="{$album->title|clearslash|escape:"html"}"
                                class="required" onBlur="javascript:get_metadata(this.value);" />
                        </td>
                        <td rowspan="4">
                            <table style='background-color:#F5F5F5; padding:18px; width:99%;'>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                    <label for="title">Secci&oacute;n:</label>
                                    </td>
                                    <td nowrap="nowrap">
                                        <select name="category" id="category"  >                                           
                                            {section name=as loop=$allcategorys}
                                                <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{t 1=$allcategorys[as]->title}%1{/t}</option>
                                                {section name=su loop=$subcat[as]}
                                                    <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}</option>
                                                {/section}
                                            {/section}
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                        <label for="title"> {t}Available:{/t} </label>
                                    </td>
                                    <td valign="top" nowrap="nowrap">
                                            <select name="available" id="available"
                                                class="required" {acl isNotAllowed="ALBUM_AVAILABLE"} disabled="disabled" {/acl}>
                                                <option value="0">{t}No{/t}</option>
                                                <option value="1" selected>{t}Yes{/t}</option>
                                            </select>
                                    </td>
                                </tr>
                                </table>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;" >
                            <label for="title">Agencia:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <input type="text" id="agency" name="agency" title={t}"Album"{/t}
                                size="80" value="{$album->agency|clearslash|escape:"html"}" />
                        </td>                        
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="title">Descripci&oacute;n:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <textarea name="description" id="description"  title={t}"description"{/t} style="width:100%; height:8em;">{t 1=$album->description|clearslash|escape:"html"}%1{/t}</textarea>
                        </td>
                    </tr>

                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="metadata">{t}Keywords:{/t}</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <input type="text" id="metadata" name="metadata" size="80"
                               class="required" title={t}"Metadata"{/t} value="{$album->metadata}" />
                            <br><label align='right'><sub>{t}Separated by coma{/t}</sub></label>
                        </td>
                    </tr>
                    {include file="album/album_images.tpl"}
                </tbody>
            </table>
        </div>

    {/if}

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id}" />
    </form>
</div>

{/block}
