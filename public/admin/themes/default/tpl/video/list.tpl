{extends file="base/admin.tpl"}

{block name="header-css" append}
	<style type="text/css">
		table.adminlist label {
			padding-right:10px;
			width:100px !important;
			display:inline-block;
		}
		table.adminlist input, table.adminlist textarea{
			width:70%;
		}
	</style>
{/block}

{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsVideo.js"></script>

{/block}

{block name="content"}
<form action="#" method="post" name="formulario">
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Video Manager :: Listing videos{/t} {if $category eq 0}HOME{else}{$datos_cat[0]->title}{/if}</h2></div>
			<ul class="old-button">
				{acl isAllowed="VIDEO_DELETE"}
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
						<img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />Eliminar
					</a>
				</li>
				{/acl}
				{acl isAllowed="VIDEO_AVAILABLE"}
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
				{/acl}
				<li>
					<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
						<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
					</button>
				</li>
				{acl isAllowed="VIDEO_CREATE"}
				<li>
					<a href="{$smarty.server.SCRIPT_NAME}?action=new&category={$category}" accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}/video.png" title="Nuevo Video" alt="Nuevo Video"><br />Nuevo Video
					</a>
				</li>
				{/acl}
                 {acl isAllowed="ALBUM_SETTINGS"}
                <li class="separator"></li>
                    <li>
                        <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Config video module{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                            {t}Configurations{/t}
                        </a>
                    </li>
                {/acl}
			</ul>
		</div>
	</div>

        <div class="wrapper-content">
            <ul class="tabs2" style="margin-bottom: 28px;">
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=favorite" {if $category=='favorite'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {else}{if $ca eq $datos_cat[0]->fk_content_category}style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}{/if} >WIDGET HOME</a>
                </li>

                {include file="menu_categorys.tpl" home="video.php?action=list"}
            </ul>
            <div id="messageBoard"></div>

            {if (!empty($msg) || !empty($msgdel) || !empty($errors) )}
                <script type="text/javascript">
                    showMsgContainer({ 'warn':['  {$msg} , {$msgdel}, {$errors} '] },'inline','messageBoard');
                </script>
            {/if}

			{render_messages}
            <div id="{$category}">
                <table class="adminheading">
                    <tr>
                        <th nowrap> Videos</th>
                    </tr>
                </table>
				{if count($videos) > 0}
					<table class="adminlist">
						<thead>
							<tr>
								<th style="width:35px;"></th>
								<th class="title">{t}Title{/t}</th>
								<th align="center" style="width:35px;">{t}Views{/t}</th>
								<th align="center">{t}Service{/t}</th>
								<th align="center">Created</th>
								{if $category=='favorite'}<th align="center">{t}Section{/t}</th>{/if}
								<th align="center" style="width:35px;">{t}Published{/t}</th>
								<th align="center" style="width:35px;">{t}Favorite{/t}</th>
								<th align="center" style="width:35px;">{t}Actions{/t}</th>
							</tr>
						</thead>
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
									{$videos[c]->author_name}
								</td>
								<td align="center">
									{$videos[c]->created}
								</td align="center">
								{if $category=='favorite'}
									<td >
										 {$videos[c]->category_title}
									</td>
								{/if}
								<td align="center">
									{acl isAllowed="VIDEO_AVAILABLE"}
										{if $videos[c]->available == 1}
											<a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="Publicado">
													<img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
										{else}
											<a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="Pendiente">
													<img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
										{/if}
									{/acl}
								</td>
								<td align="center">
									{acl isAllowed="VIDEO_FAVORITE"}
											{if $videos[c]->favorite == 1}
											   <a href="?id={$videos[c]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_on" title="Quitar de Portada"></a>
											{else}
												<a href="?id={$videos[c]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_off" title="Meter en Portada"></a>
											{/if}
									 {/acl}
								</td>
								<td style="padding:1px; font-size:11px;" align="center">
									<ul class="action-buttons">
										{acl isAllowed="VIDEO_UPDATE"}
										<li>
											<a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$videos[c]->id}');" title="{t}Edit{/t}" >
												<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
										</li>
										{/acl}

										{acl isAllowed="VIDEO_DELETE"}
										<li>
											<a href="#" onClick="javascript:delete_videos('{$videos[c]->id}','{$paginacion->_currentPage|default:""}');" title="Eliminar">
												<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
										</li>
										{/acl}
									</ul>
								</td>
							</tr>

						{sectionelse}

						{/section}
						<tfoot>
							<tr>
								<td colspan="10" align="center">{$pagination|default:""}</td>
							</tr>
						</tfoot>
					</table>
				{else}
					<table class="adminform">
						<tbody>
							<tr>
								<td align="center" colspan="8"><br><br><h2><b>Ningun video guardado</b></h2><br><br></td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td></td>
							</tr>
						</tfoot>
					</table>
				{/if}

            </div>

        </div>

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>

{/block}
