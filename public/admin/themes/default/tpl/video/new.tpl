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


<div class="wrapper-content">

<form action="#" method="post" name="formulario" id="formulario" {$formAttrs} >

        <div id="menu-acciones-admin" class="clearfix">
			<div style="float:left; margin:8px;"><h2>{t}Video manager{/t} :: {if $smarty.request.action eq "new"}{t}Creating video{/t}{else}{t}Editing video{/t}{/if}</h2></div>
			<ul>
				<li>
				{if isset($video->id)}
                    {acl isAllowed="VIDEO_UPDATE"}
                        <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$video->id}', 'formulario');" >
                    {/acl}
				{else}
                    {acl isAllowed="VIDEO_CREATE"}
                        <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');" >
                    {/acl}
				{/if}
						<img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
					</a>
				</li>
                {acl isAllowed="VIDEO_CREATE"}
				<li>
					<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$video->id}', 'formulario');" value="Validar" title="Validar">
						<img border="0" src="{$params.IMAGE_DIR}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
					</a>
				</li>
                {/acl}
				<li>
					<a href="{$smarty.server.SCRIPT_NAME}?action=list&category={$category}" value="Cancelar" title="Cancelar">
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
                    <td valign="top" style="width:70%;">
                          <br>
                        <label for="video_url">{t}Video URL:{/t}</label>
                        <input type="text" id="video_url" name="video_url" title="Video url"
                                value="{$video->video_url}" class="required"
                                onChange="javascript:loadVideoInformation(this.value);"/> &nbsp;
                        <img src="{$params.IMAGE_DIR}template_manager/refresh16x16.png"
                             onClick="javascript:loadVideoInformation($('video_url').value);" />
                    </td>
					<td valign="top">
						<table style="padding:0 4px;">
							<tr>
								<td valign="top">
									<label for="title">{t}Section:{/t}</label>
									<select name="category" id="category">
										{section name=as loop=$allcategorys}
											<option value="{$allcategorys[as]->pk_content_category}" {if $video->category eq $allcategorys[as]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
											{section name=su loop=$subcat[as]}
												<option value="{$subcat[as][su]->pk_content_category}" {if $video->category eq $subcat[as][su]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
											{/section}
										{/section}
									</select>
                                    <br />
									<label for="title">{t}Available:{/t}</label>

									<select name="available" id="available"
                                        {acl isNotAllowed="ALBUM_AVAILABLE"} disabled="disabled" {/acl} class="required">
										 <option value="1" {if $video->available eq '1'} selected {/if}>Si</option>
										 <option value="0" {if $video->available eq '0'} selected {/if}>No</option>
									</select>
									<input type="hidden" value="1" name="content_status">
								</td>
							</tr>
						</table>
					</td>
				</tr>
                <tr>
                    <td style="width:100%;" colspan="2">
                        <div id="video-information">
                            {* AJAX LOAD *}
                            {if $smarty.request.action eq "read"}
                                {include file="video/videoInformation.tpl"}
                            {/if}
                        </div>
                    </td>
                </tr>
			</tbody>
			<tfooter>
				<tr>
                    <td></td>
                    <td>
                    * {t}Only accepted videos from{/t}:
                    <ul>
                        <li>[Youtube](http://www.youtube.com/)</li>
                        <li>[Vimeo](http://vimeo.com/)</li>
                        <li>[Metacafe](http://metacafe.com/)</li>
                        <li>[Dailymotion](http://dailymotion.com/)</li>
                        <li>[Collegehumor](http://collegehumor.com/)</li>
                        <li>[Blip.tv](http://blip.tv/)</li>
                        <li>[Myspace](http://vids.myspace.com/)</li>
                        <li>[Ted Talks](http://www.ted.com/talks/)</li>
                        <li>[11870.com](http://11870.com/)</li>
                        <li>[Marca.tv](http://www.marca.tv/)</li>
                        <li>[Dalealplay](http://www.dalealplay.com/)</li>
                        <li>[RuTube](http://www.rutube.ru/)</li>
                    </ul>
                    </td>
				</tr>
			</tfooter>
        </table>

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$video->id}" />
</form>

{/block}
