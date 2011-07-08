{extends file="base/admin.tpl"}

{block name="header-css" append}
	<style type="text/css">
		table.adminlist label {
			padding-right:10px;
			width:100px !important;
			display:inline-block;
		}
		table.adminlist input {
			width:70%;
		}
	</style>
{/block}

{block name="content"}
<div id="wrapper-content">

<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}
	 style="max-width:70% !important; margin: 0 auto; display:block;">

    {* LISTADO ******************************************************************* *}
    {if !isset($smarty.request.action) || $smarty.request.action eq "list"}
        <ul class="tabs2" style="margin-bottom: 28px;">

             {include file="menu_categorys.tpl" home="video.php?action=list"}
        </ul>

		<div id="menu-acciones-admin" class="clearfix">
			<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{t}Video Manager :: Listing videos{/t}</h2></div>
			<ul>
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
						<img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="Eliminar" alt="Eliminar" ><br />Eliminar
					</a>
				</li>
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
						<img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
					</a>
				</li>
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
						<img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Publicar" alt="Publicar" ><br />Publicar
					</a>
				</li>
				<li>
					<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
						<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
					</button>
				</li>
				<li>
					<a href="{$smarty.server.SCRIPT_NAME}?action=new" accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}/video.png" title="Nuevo Video" alt="Nuevo Video"><br />Nuevo Video
					</a>
				</li>
			</ul>
		</div>

		<br>

        <div id="{$category}">
            <table class="adminheading">
                <tr>
                    <th nowrap> Videos</th>
                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th class="title" style="width:35px;"></th>
                    <th>{t}Title{/t}</th>
                    <th align="center" style="width:35px;">{t}Views{/t}</th>
                    <th align="center">Fecha</th>
                    <th align="center" style="width:35px;">{t}Published{/t}</th>
                    <th align="center" style="width:35px;">{t}Favorite{/t}</th>
                    <th align="center" style="width:35px;">{t}Actions{/t}</th>
                </tr>
                {section name=c loop=$videos}
                    <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;">
                        <td >
                            <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$videos[c]->id}"  style="cursor:pointer;">
                        </td>
                        <td onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
                            {$videos[c]->title|clearslash}
                        </td>

                        <td align="center">
                            {$videos[c]->views}
                        </td>
                        <td align="center">
                            {$videos[c]->created}
                        </td>
                        <td align="center">
                            {if $videos[c]->available == 1}
                                    <a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Publicado">
                                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
                            {else}
                                    <a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Pendiente">
                                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                            {/if}
                        </td>
                        <td align="center">
                                    {if $videos[c]->favorite == 1}
                                       <a href="?id={$videos[c]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_on" title="Quitar de Portada"></a>
                                    {else}
                                        <a href="?id={$videos[c]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_off" title="Meter en Portada"></a>
                                    {/if}
                            </td>

                        <td style="padding:1px; font-size:11px;" align="center">
                            <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$videos[c]->id}');" title="Modificar">
                                    <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
							<a href="#" onClick="javascript:delete_videos('{$videos[c]->id}','{$paginacion->_currentPage}');" title="Eliminar">
                                    <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                        </td>
                    </tr>

                {sectionelse}
                    <tr>
                        <td align="center" colspan="8"><br><br><p><h2><b>Ningun video guardado</b></h2></p><br><br></td>
                    </tr>
                {/section}
                {if !empty($pagination)}
                    <tfoot>
						<tr>
							<td colspan="8" align="center">{$pagination}</td>
						</tr>
					</tfoot>
                {/if}
            </table>
            {if $smarty.get.alert eq 'ok'}
                 <script type="text/javascript" language="javascript">
                    {literal}
                           alert('{/literal}{$smarty.get.msgdel}{literal}');
                    {/literal}
                    </script>
            {/if}
        </div>

    {/if}


{* FORMULARIO PARA ENGADIR || ACTUALIZAR *********************************** *}
	{if isset($smarty.request.action) && ($smarty.request.action eq "new" || $smarty.request.action eq "read")}

		<div id="menu-acciones-admin" class="clearfix">
			<div style="float:left;margin-left:10px;margin-top:10px;">
				<h2>{t}Video Manager :: Video editing{/t}</h2>
			</div>
			<ul>
				<li>
				{if isset($video->id)}
					<a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$video->id}', 'formulario');">
				{else}
					<a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
				{/if}
						<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
					</a>
				</li>
				<li>
					<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$video->id}', 'formulario');" value="Validar" title="Validar">
						<img border="0" src="{$params.IMAGE_DIR}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
					</a>
				</li>

				<li>
					<a href="{$smarty.server.SCRIPT_NAME}?action=list" value="Cancelar" title="Cancelar">
						<img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
					</a>
				</li>
			</ul>
		</div>

		<br>

		<table class="adminheading">
			<tr>
				<td>{t}Enter video information{/t}</td>
			</tr>
		</table>
        <table class="adminlist">
			<tbody>
				 <tr>
					<td style="width:80%;">
						<table style="width:100%;">
							<tr>
							<td valign="top">
								<label for="video_url">{t}Video URL:{/t}</label>
								<input type="text" id="video_url" name="video_url" value="{$video->video_url}" title="Video url" class="required" />
							</td>
							</tr>
							<tr>
								<td valign="top">
									<label for="title">{t}Title:{/t}</label>
									<input type="text" id="title" name="title" title="Título de la noticia"  onChange="javascript:get_metadata(this.value);"
											value="{$video->title|clearslash|escape:"html"}" class="required" />
								</td>
							</tr>
							<tr>
								<td valign="top">
									<label for="metadata">{t}Keywords:{/t}</label>
									<input type="text" id="metadata" name="metadata"title="Metadatos" value="{$video->metadata}" />
									<sub>{t}Comma separated{/t}</sub>
								</td>
							</tr>
							 <tr>
								<td>
									<label for="title">Descripción:</label>
									<textarea name="description" id="description" class="required" value=""
											title="{t}Video description{/t}">{$video->description|clearslash}</textarea>
								</td>
							</tr>

							{if $smarty.request.action eq "read"}
							<tr>
							   <td valign="top" align="right" style="padding:4px;" >
									<label for="title">Enlace:</label>
							   </td>
							   <td style="padding:4px;" nowrap="nowrap" >
									<a href="{$smarty.const.SITE_URL}{$video->permalink}" target="_blank">
										{$smarty.const.SITE_URL}{$video->permalink}
									</a>
							   </td>
							</tr>
							<tr>
							   <td valign="top" align="right" style="padding:4px;" >
									<label for="title">{t}Information{/t}:</label>
							   </td>
							   <td nowrap="nowrap" >
								   <div id="imgcc">
										{foreach from=$video->information key=key item=value}
										<strong>{$key}</strong>: {$value} <br/>
										{/foreach}
								   </div>
								</td>
							</tr>
							{/if}
						</table>
					</td>
					<td valign="top">
						<table>
							<tr>
								<td valign="top">
									<label for="title">{t}Section:{/t}</label>
									<select name="category" id="category"  >
										{section name=as loop=$allcategorys}
											<option value="{$allcategorys[as]->pk_content_category}" {if $video->category eq $allcategorys[as]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
											{section name=su loop=$subcat[as]}
												<option value="{$subcat[as][su]->pk_content_category}" {if $video->category eq $subcat[as][su]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
											{/section}
										{/section}
									</select>

									<label for="title">{t}Available:{/t}</label><br>

									<select name="available" id="available" class="required">
										 <option value="1" {if $video->available eq '1'} selected {/if}>Si</option>
										 <option value="0" {if $video->available eq '0'} selected {/if}>No</option>
									</select>
									<input type="hidden" value="1" name="content_status">
								</td>
							</tr>
						</table>
					</td>

				</tr>
			</tbody>
			<tfooter>
				<tr>
					<td colspan=2>&nbsp;</td>
				</tr>
			</tfooter>
        </table>


    {/if}

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id}" />
</form>

{/block}
